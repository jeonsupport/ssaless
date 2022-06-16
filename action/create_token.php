<?php
    session_start();

    // DB connect
    include_once "../../db_connecter.php";
    include_once "../../inc.php";

    $sqlConnecter = new Database_Connecter();
    $db = $sqlConnecter->MYSQL_ConnectServer();

    try {

        
        $param_error = '';
        $date = isset($_POST['date']) && !empty($_POST['date']) ? strip_tags($_POST['date']) : $param_error .= 'date;';
        $hash = isset($_POST['hash']) && !empty($_POST['hash']) ? strip_tags($_POST['hash']) : $param_error .= 'hash;';
        $csrf = isset($_POST['csrf']) && !empty($_POST['csrf']) ? strip_tags($_POST['csrf']) : $param_error .= 'csrf;';
        $salt = 'ax2$@$$!w';

        // 레퍼 체크
        if (!check_referer()) throw new Exception('잘못된 접근입니다.(1)');

        // 파라미터 검증
        if ($param_error!='') throw new Exception('잘못된 접근입니다.(2)');

        // csrf 검증
        if (!hash_equals($_SESSION['_csrfToken'], $csrf)) throw new Exception('잘못된 접근입니다.(3)');

        // date 검증
        if (!password_verify($date.$salt, $hash)) throw new Exception('잘못된 접근입니다.(4)');




        // //group update
        // $query = "UPDATE sales_group_list SET comm_rate = :comm_rate WHERE grp = :grp";
        // $statement = $db->prepare($query);
        // $statement->bindValue(':comm_rate', $comm);
        // $statement->bindValue(':grp', $grp);
        // $statement->execute();

        // //개별 update
        // $query = "UPDATE sales_member SET comm_rate = :comm_rate WHERE seq_no = :seq_no";
        // $statement = $db->prepare($query);
        // $statement->bindValue(':comm_rate', $comm);
        // $statement->bindValue(':seq_no', $seq_no);
        // $statement->execute();
        

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