<?php
    session_start();

    // DB connect
    include_once '../../db_connecter.php';
    include_once '../../inc.php';

    $sqlConnecter = new Database_Connecter();
    $db = $sqlConnecter->MYSQL_ConnectServer();

    try {
        if (!check_referer()) throw new Exception('잘못된 접근입니다.');

        $id  = isset($_POST['id'])  ? strip_tags($_POST['id'])  : '';
        $pwd = isset($_POST['pwd']) ? strip_tags($_POST['pwd']) : '';

        if($id=='' || $pwd=='') {
            throw new Exception('아이디, 패스워드를 입력해주세요.');
        }

        $query = 
            "
                SELECT a.user_id, a.user_pw, a.grp, a.grp_flag, a.comm_rate, b.comm_rate AS grp_comm_rate 
                FROM sales_member AS a
                LEFT JOIN sales_group_list AS b
                ON a.grp = b.grp
                WHERE user_id = :user_id
            ";
        $statement = $db->prepare($query);
        $statement->bindValue(':user_id', $id);
        $statement->execute();
        $rowCount = $statement->rowCount();
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        $pass_word  = isset($row['user_pw'])    ? $row['user_pw']    : '';
        $id         = isset($row['user_id'])    ? $row['user_id']    : '';
        $grp        = isset($row['grp'])        ? $row['grp']        : '';
        $grp_flag   = isset($row['grp_flag'])   ? $row['grp_flag']   : '';
        $comm_rate  = isset($row['comm_rate'])  ? $row['comm_rate']  : '';
        $grp_comm_rate = isset($row['grp_comm_rate']) ? $row['grp_comm_rate'] : '';

        if ($rowCount == 0) {
            throw new Exception('아이디 혹은 비밀번호를 확인해주세요.');
        }


        if (password_verify($pwd, $pass_word)) {
            $_SESSION['UserID']     = $id;
            $_SESSION['Grp']        = $grp;
            $_SESSION['GrpFlag']    = $grp_flag;
            $_SESSION['CommRate']   = $comm_rate;
            $_SESSION['GrpCommRate'] = $grp_comm_rate;
            $_SESSION['_csrfToken'] = bin2hex(random_bytes(32));

            $query = "UPDATE sales_member SET last_date = NOW(3) WHERE user_id = :user_id";
            $statement = $db->prepare($query);
            $statement->bindValue(':user_id', $id);
            $statement->execute();

            movepage('../fee_detail.php');
        } else {
            throw new Exception('아이디 혹은 비밀번호를 확인해주세요.');
        }


    } catch(Exception $e) {
        msgback($e->getMessage());
    }


?>