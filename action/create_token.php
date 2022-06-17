<?php
    session_start();

    // DB connect
    include_once "../../db_connecter.php";
    include_once "../../inc.php";

    $sqlConnecter = new Database_Connecter();
    $db = $sqlConnecter->MYSQL_ConnectServer();

    try {


        // 세션 파라미터
        $session_user_id = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : '';
        $session_grp = isset($_SESSION['Grp']) ? $_SESSION['Grp'] : '';
        $session_grp_flag = isset($_SESSION['GrpFlag']) ? $_SESSION['GrpFlag'] : '';
        
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


        // 금액 추출
        $arr_date = explode('-', $date);
        $year  = isset($arr_date[0]) && !empty($arr_date[0]) ? strval($arr_date[0]) : '';
        $month = isset($arr_date[1]) && !empty($arr_date[1]) ? strval($arr_date[1]) : '';
        $day   = isset($arr_date[2]) && !empty($arr_date[2]) ? strval($arr_date[2]) : '';
        if ($year=='' || $month=='' || $day=='') throw new Exception ('날짜 추출 실패');

        if ($day==='15') {
            $start_date = $year.'-'.$month.'-01';
            $end_date = $year.'-'.$month.'-10';
        } else if ($day==='25') {
            $start_date = $year.'-'.$month.'-11';
            $end_date = $year.'-'.$month.'-20';
        } else if ($day==='05') {
            $start_date = $year.'-'.$month.'-21';
            $end_date = $year.'-'.$month.'-'.date('t', strtotime($year.'-'.$month));
        } else {
            throw new Exception ('잘못된 접근입니다.(5)');
        }

        // temp테이블 체크할 것!


        $query = 
            "
                SELECT Sum(leader_comm) AS tot_leader_comm
                    , Sum(sa_comm) AS tot_sa_comm
                FROM   (
                        SELECT IF(c.grp_flag = 1, b.commission * 0.1 * d.comm_rate, (b.commission * 0.1 * d.comm_rate ) - Truncate(( ( commission * 0.1 * d.comm_rate ) * 0.01 * c.comm_rate ), 0)) AS leader_comm
                                , IF(c.grp_flag = 1, 0, Truncate(( ( commission * 0.1 * d.comm_rate ) * 0.01 * c.comm_rate ), 0)) AS sa_comm
                        FROM   info_service AS a
                            JOIN commission_history AS b
                                ON a.token = b.auth_key
                            JOIN sales_member AS c
                                ON a.recommender = c.user_id
                            JOIN sales_group_list d
                                ON c.grp = d.grp
                        WHERE  a.recommender IN (SELECT user_id FROM sales_member WHERE  grp = :grp)
                        AND Date(b.reg_date) BETWEEN '$start_date' AND '$end_date'
                    ) AS tot_table
            ";
        $statement = $db->prepare($query);
        $statement->bindValue(':grp', $session_grp);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        $tot_leader_comm = isset($row['tot_leader_comm']) && !empty($row['tot_leader_comm']) ? $row['tot_leader_comm'] : 0;
        $tot_sa_comm = isset($row['tot_sa_comm']) && !empty($row['tot_sa_comm']) ? $row['tot_sa_comm'] : 0;

        
        

        //api 전송

        // 정보 insert



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