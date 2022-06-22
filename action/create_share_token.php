<?php
    session_start();


    // DB connect
    include_once "../../db_connecter.php";
    include_once "../../inc.php";

    $sqlConnecter = new Database_Connecter();
    $db = $sqlConnecter->MYSQL_ConnectServer();

    try {

        //---------------------------------------
        // 세션 파라미터
        //---------------------------------------
        $session_user_id = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : '';

        
        $param_error = '';
        $price = isset($_POST['price']) && !empty($_POST['price']) ? strip_tags($_POST['price']) : $param_error .= 'price;';
        $csrf = isset($_POST['csrf']) && !empty($_POST['csrf']) ? strip_tags($_POST['csrf']) : $param_error .= 'csrf;';
        $price = abs($price);

        //---------------------------------------
        // 유효성 체크
        //---------------------------------------
        // 레퍼 체크
        if (!check_referer()) throw new Exception('잘못된 접근입니다.(1)');

        // 파라미터 검증
        if ($param_error!='') throw new Exception('잘못된 접근입니다.(2)');

        // csrf 검증
        if (!hash_equals($_SESSION['_csrfToken'], $csrf)) throw new Exception('잘못된 접근입니다.(3)');

        // 금액 체크(1000단위)
        if ($price % 1000 > 0) throw new Exception('유효한 값을 입력해주세요.');


        // 영업사 잔금 체크
        $query = "SELECT share FROM sales_member WHERE user_id = :user_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':user_id', $session_user_id);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        $share = isset($row['share']) ? $row['share'] : 0;
        if ($price > $share) throw new Exception ('잔액이 부족합니다.');


        //---------------------------------------            
        //api 전송
        //---------------------------------------
        $request_array = array (
            'auth'         => AUTH_TOKEN
            , 'action'     => 'qrCreateToken'
            , 'product_no' => 1008
            , 'price'      => intval($price)
        );

        $json = post(API_URL, $request_array);
        $result = json_decode($json, true);
        if ($result['status'] == 0) throw new Exception($result['msg']);


        //---------------------------------------------------------------------------------------------------------------
        // 트랜잭션 시작
        $db->beginTransaction();


        
        //---------------------------------------
        // 추출 핀코드 저장
        //---------------------------------------
        $query = 
            "
                INSERT INTO sales_qr_pin_list (user_id
                                                , product_no
                                                , product_name
                                                , before_price
                                                , price
                                                , now_price
                                                , token

                                            ) VALUES ( :user_id
                                                , :product_no
                                                , :product_name
                                                , :before_price
                                                , :price
                                                , :now_price
                                                , :token
                                        )
            ";
        $statement = $db->prepare($query);
        $statement->bindValue(':user_id', $session_user_id);
        $statement->bindValue(':product_no', 1008);
        $statement->bindValue(':product_name', $result['product_name']);
        $statement->bindValue(':before_price', $share);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':now_price', $share-$price);
        $statement->bindValue(':token', $result['token']);
        $statement->execute();
        $rowCount = $statement->rowCount();
        if ($rowCount <> 1) {
            $db->rollBack();
            throw new Exception('핀코드 정보 입력 실패');
        }

        //---------------------------------------
        // 잔액 업뎃
        //---------------------------------------
        $query = "UPDATE sales_member SET share = share - :share WHERE user_id = :user_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':share', intval($price));
        $statement->bindValue(':user_id', $session_user_id);
        $statement->execute();
        $rowCount = $statement->rowCount();

        if ($rowCount <> 1) {
            $db->rollBack();
            throw new Exception('잔액 정보 입력 실패');
        }
        

        // 완료되었으면 커밋
        $db->commit();


        $result_array = array (
            'status' => 1
            , 'msg'  => 'ok'
        );
        
        return parseJson($result_array);
        
    } catch(Exception $e) {
        return parseJson(array('status' => 0, 'msg' => $e->getMessage()));
    } catch(PDOException $e) {
        return parseJson(array('status' => 0, 'msg' => $e->getMessage()));
    }

    function parseJson($data){
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); // 유니코드, 역슬래시 제거
    }

?>