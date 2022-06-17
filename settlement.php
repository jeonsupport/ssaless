
<?php 

    include('header.php');
    include_once "../db_connecter.php";

    $conn = new Database_Connecter();
    $db = $conn->MYSQL_ConnectServer();


    // 세션 파라미터
    $session_user_id = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : '';
    $session_grp = isset($_SESSION['Grp']) ? $_SESSION['Grp'] : '';
    $session_grp_flag = isset($_SESSION['GrpFlag']) ? $_SESSION['GrpFlag'] : '';
    $_csrfToken = isset($_SESSION['_csrfToken']) ? $_SESSION['_csrfToken'] : '';

    // 파라미터
    $get_month = isset($_GET['month']) && !empty($_GET['month']) ? $_GET['month'] : date('Y-m');


    //--------------------------------------------------------
    $str_grp = $session_grp_flag==1 ? '(그룹장)' : '';
    $year_1 = $get_month.'-01';
    $year_10 = $get_month.'-10';
    $year_1_10 = $get_month.'-15';

    $year_11 = $get_month.'-11';
    $year_20 = $get_month.'-20';
    $year_11_20 = $get_month.'-25';

    $year_21 = $get_month.'-21';
    $year_last = $get_month.'-'.date('t', strtotime($get_month));
    $year_21_last = date("Y-m", strtotime("+1 month", strtotime($get_month))).'-05';

    $where_cond = $session_grp_flag==1 ? "a.recommender IN (SELECT user_id FROM sales_member WHERE grp = :grp)" : "a.recommender IN (SELECT user_id FROM sales_member WHERE grp = :grp AND user_id = '{$session_user_id}')";
    // 쿼리
    try {
        $query = 
            "
                (
                    SELECT SUM(leader_comm) AS tot_leader_comm
                            , SUM(sa_comm) AS tot_sa_comm
                            , COUNT(seq_no) AS tot_record
                            , act_button
                            , '$year_1 ~ $year_10' AS period
                            , '$year_1_10' AS year_month_day
                            , token
                            , price
                            , balance
                    FROM (
                            SELECT b.seq_no AS seq_no
                                , IF(c.grp_flag = 1, b.commission * 0.1 * d.comm_rate, (b.commission * 0.1 * d.comm_rate ) - truncate(((commission * 0.1 * d.comm_rate) * 0.01 * c.comm_rate), 0)) AS leader_comm
                                , IF(c.grp_flag = 1, 0, truncate(((commission * 0.1 * d.comm_rate) * 0.01 * c.comm_rate), 0)) AS sa_comm
                                , IF(curdate() >= '$year_1_10', 1, 0) AS act_button
                                , (SELECT token FROM sales_settlement_pincode WHERE user_id = '$session_user_id' AND set_date = '$year_1_10') as token
                                , (SELECT price FROM sales_settlement_pincode WHERE user_id = '$session_user_id' AND set_date = '$year_1_10') as price
                                , (SELECT balance FROM sales_settlement_pincode WHERE user_id = '$session_user_id' AND set_date = '$year_1_10') as balance
                            FROM   info_service AS a
                            JOIN commission_history AS b
                                ON a.token = b.auth_key
                            JOIN sales_member AS c
                                ON a.recommender = c.user_id
                            JOIN sales_group_list AS d
                                ON c.grp = d.grp
                            WHERE  {$where_cond}
                            AND DATE(b.reg_date) BETWEEN '$year_1' AND '$year_10'
                    ) AS tot_table 
                )
                UNION ALL
                (
                    SELECT SUM(leader_comm) AS tot_leader_comm
                            , SUM(sa_comm) AS tot_sa_comm
                            , COUNT(seq_no) AS tot_record
                            , act_button
                            , '$year_11 ~ $year_20' AS period
                            , '$year_11_20' AS year_month_day
                            , token
                            , price
                            , balance
                    FROM (
                            SELECT b.seq_no AS seq_no
                                , IF(c.grp_flag = 1, b.commission * 0.1 * d.comm_rate, (b.commission * 0.1 * d.comm_rate ) - truncate(((commission * 0.1 * d.comm_rate) * 0.01 * c.comm_rate), 0)) AS leader_comm
                                , IF(c.grp_flag = 1, 0, truncate(((commission * 0.1 * d.comm_rate) * 0.01 * c.comm_rate), 0)) AS sa_comm
                                , IF(curdate() >= '$year_11_20', 1, 0) AS act_button
                                , (SELECT token FROM sales_settlement_pincode WHERE user_id = '$session_user_id' AND set_date = '$year_11_20') as token
                                , (SELECT price FROM sales_settlement_pincode WHERE user_id = '$session_user_id' AND set_date = '$year_11_20') as price
                                , (SELECT balance FROM sales_settlement_pincode WHERE user_id = '$session_user_id' AND set_date = '$year_1_10') as balance
                            FROM   info_service AS a
                            JOIN commission_history AS b
                                ON a.token = b.auth_key
                            JOIN sales_member AS c
                                ON a.recommender = c.user_id
                            JOIN sales_group_list AS d
                                ON c.grp = d.grp
                            WHERE  {$where_cond}
                            AND DATE(b.reg_date) BETWEEN '$year_11' AND '$year_20'
                    ) AS tot_table 
                )
                UNION ALL
                (
                    SELECT SUM(leader_comm) AS tot_leader_comm
                            , SUM(sa_comm) AS tot_sa_comm
                            , COUNT(seq_no) AS tot_record
                            , act_button
                            , '$year_21 ~ $year_last' AS period
                            , '$year_21_last' AS year_month_day
                            , token
                            , price
                            , balance
                    FROM (
                            SELECT b.seq_no AS seq_no
                                , IF(c.grp_flag = 1, b.commission * 0.1 * d.comm_rate, (b.commission * 0.1 * d.comm_rate ) - truncate(((commission * 0.1 * d.comm_rate) * 0.01 * c.comm_rate), 0)) AS leader_comm
                                , IF(c.grp_flag = 1, 0, truncate(((commission * 0.1 * d.comm_rate) * 0.01 * c.comm_rate), 0)) AS sa_comm
                                , IF(curdate() >= '$year_21_last', 1, 0) AS act_button
                                , (SELECT token FROM sales_settlement_pincode WHERE user_id = '$session_user_id' AND set_date = '$year_21_last') as token
                                , (SELECT price FROM sales_settlement_pincode WHERE user_id = '$session_user_id' AND set_date = '$year_21_last') as price
                                , (SELECT balance FROM sales_settlement_pincode WHERE user_id = '$session_user_id' AND set_date = '$year_1_10') as balance
                            FROM   info_service AS a
                            JOIN commission_history AS b
                                ON a.token = b.auth_key
                            JOIN sales_member AS c
                                ON a.recommender = c.user_id
                            JOIN sales_group_list AS d
                                ON c.grp = d.grp
                            WHERE  {$where_cond}
                            AND DATE(b.reg_date) BETWEEN '$year_21' AND '$year_last'
                    ) AS tot_table 
                )
            ";



        $statement = $db->prepare($query);
        $statement->bindValue(':grp', $session_grp);
        $statement->execute();

    } catch (PDOException $e) {
        die($e->getMessage());
    }


?>
    <section class="contWrap inner">
        <div class="titleBox">
            <div class="admin_btn">
                <p><?=$session_user_id.$str_grp?> 님</p>
                <button type="button" class="btn" onclick="location.replace('./action/logout.php');">로그아웃</button>
            </div>
            <h1>정산</h1>
        </div>
        <div class="cont settlement">
            <div class="contInput">
                <form class="frm" name="schfrm" id="schfrm" action="<?=$_SERVER['PHP_SELF']?>">
                    <input type="month" name="month" value="<?=$get_month?>"> 
                    <button type="submit" class="btn">조회</button>
                </form>
            </div>
            <div class="contTable">
                <table>
                    <thead>
                        <tr>
                            <td>기간</td>
                            <td>수수료 합계</td>
                            <td>정산</td>
                            <td>핀코드 금액</td>
                            <td>잔액</td>
                            <td>핀코드</td>
                        </tr> 
                    </thead>
                    <?php
                        
                        while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                            $tot_leader_comm = isset($row['tot_leader_comm']) && !empty($row['tot_leader_comm']) ? $row['tot_leader_comm'] : 0;
                            $tot_sa_comm = isset($row['tot_sa_comm']) && !empty($row['tot_sa_comm']) ? $row['tot_sa_comm'] : 0;
                            $act_button = isset($row['act_button']) ? $row['act_button'] : 0;
                            $period = isset($row['period']) ? $row['period'] : '';
                            $year_month_day = isset($row['year_month_day']) ? $row['year_month_day'] : '';
                            $token = isset($row['token']) && !empty($row['token']) ? $row['token'] : '-';
                            $price = isset($row['price']) && !empty($row['price']) ? number_format($row['price']) : 0;
                            $balance = isset($row['balance']) && !empty($row['balance']) ? number_format($row['balance']) : 0;

                            $date_hash = password_hash($year_month_day.'ax2$@$$!w', PASSWORD_DEFAULT);
                    ?>
                    <tbody>
                        <tr>
                            <td><?=$period?></td>
                            <td><?=$session_grp_flag==1 ? number_format($tot_leader_comm) : number_format($tot_sa_comm)?></td>
                            <td>
                                <?php 
                                    if ($token=='-' && $act_button==1) {
                                ?>
                                    <button onclick="create_token('<?=$year_month_day?>', '<?=$date_hash?>', '<?=$_csrfToken?>');">정산</button>
                                <?php } else { ?>
                                    <button disabled>정산</button>
                                <?php } ?>
                            </td>
                            <td><?=$price?></td>
                            <td><?=$balance?></td>
                            <td><?=$token=='balance' ? '-' : $token?></td>
                        </tr>
                        <?php } // end while ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <script type="text/javascript" src="./assets/js/common.js"></script>
</body>
</html>