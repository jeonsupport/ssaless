<?php
    include('header.php');
    include_once "../db_connecter.php";
    include_once "../paging.php";

    $conn = new Database_Connecter();
    $paging = new Paging();
    $db = $conn->MYSQL_ConnectServer();


    // 세션 파라미터
    $s_user_id = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : '';
    $s_grp = isset($_SESSION['Grp']) ? $_SESSION['Grp'] : '';
    $s_grp_flag = isset($_SESSION['GrpFlag']) ? $_SESSION['GrpFlag'] : '';
    $s_comm_rate = isset($_SESSION['CommRate']) ? $_SESSION['CommRate'] : '';
    $s_grp_comm_rate = isset($_SESSION['GrpCommRate']) ? $_SESSION['GrpCommRate'] : '';

    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $startDate = isset($_GET['dateA']) ? $_GET['dateA'] : date("Y-m-d", strtotime("-1 month"));
    $endDate = isset($_GET['dateB']) ? $_GET['dateB'] : date("Y-m-d", strtotime("now"));

    $str_grp = $s_grp_flag==1 ? '(그룹장)' : '';
    ////////////////////////////////////////////////////////////////////////////////////


    //-----------------------
    //쿼리 조건 추출
    //-----------------------
    $where_buff = array();
    $where_buff[] = $s_grp_flag==1 ? "a.recommender IN (SELECT user_id FROM sales_member WHERE grp = :grp)" : "a.recommender IN (SELECT user_id FROM sales_member WHERE grp = :grp AND user_id = '{$s_user_id}')";
    if ($startDate && $endDate) {
        $where_buff[] = " DATE(b.reg_date) BETWEEN '{$startDate}' AND '{$endDate}' ";
    }
    $where_cond = $where_buff ? " WHERE ".implode(" AND ", $where_buff) : "";


    //-----------------------
    // 페이징 관련
    //-----------------------
    $pageSize = 50;
    $startRow = ($page-1) * $pageSize;
    $url = $_SERVER['PHP_SELF'];

    try {

        $query = 
            "   
                SELECT SUM(leader_comm) AS tot_leader_comm
                       , SUM(sa_comm) AS tot_sa_comm
                       , COUNT(seq_no) AS tot_record 
                FROM ( 
                        SELECT b.seq_no AS seq_no
                        , if(c.grp_flag=1, b.commission * 0.1 * d.comm_rate, (b.commission * 0.1 * d.comm_rate) - ((commission * 0.1 * d.comm_rate) * 0.01 * c.comm_rate)) AS leader_comm
                        , if(c.grp_flag=1, 0, (commission * 0.1 * d.comm_rate) * 0.01 * c.comm_rate) AS sa_comm
                        FROM info_service AS a
                            JOIN commission_history AS b
                                ON a.token = b.auth_key
                            JOIN sales_member AS c
                                ON a.recommender = c.user_id
                            JOIN sales_group_list d
                                ON c.grp = d.grp
                        {$where_cond}
                ) AS tot_table
            ";

            echo '<br><br><br><br><br><br><br><br><br>';
            echo $query;

        $statement = $db->prepare($query);
        $statement->bindValue(':grp', $s_grp);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        $tot_leader_comm = isset($row['tot_leader_comm']) ? $row['tot_leader_comm'] : 0;
        $tot_sa_comm = isset($row['tot_sa_comm']) ? $row['tot_sa_comm'] : 0;
        $totRecord = isset($row['tot_record']) ? $row['tot_record'] : 0;


        $config = array(
          'base_url' => $url,
          'page_rows' => $pageSize,
          'total_rows' => $totRecord
        );
    
        $paging->initialize($config);
        $pagination = $paging->create();

    } catch(PDOException $e) {
        die('query error');
    } catch(Exception $e) {
        die($e->getMessage());
    }

?>
    <section class="sub_bo_Wrap">
        <div class="subTopBox">
            <div class="subT_SideBox">
                <div class="admin_btn">
                    <p class="btn btn-light"><?=$s_user_id.$str_grp?> 님</p>
                    <button type="button" class="btn btn-danger" onclick="location.replace('./action/logout.php');">로그아웃</button>
                </div>
                <div class="subtName"><h1>수수료 조회(상세보기)
                    <!-- <a href=""><p>일별보기<i class="fa fa-angle-right" aria-hidden="true"></i></p></a> -->
                </h1></div>
            </div>
        </div>
        <div id="process_submit" class="fee">
            <div class="process_sideBox">
                <form class="frm" name="schfrm" id="schfrm" action="<?=$_SERVER['PHP_SELF']?>">
                    <div>
                        <p class="cell text-center">
                            <input type="date" class="" name="dateA" value="<?=$startDate?>"> ~ 
                            <input type="date" class="" name="dateB" value="<?=$endDate?>">
                            <button type="submit" class="btn go">조회</button>
                        </p> 
                    </div><br><br>
                </form>
                <ul class='flexBox'>
                    <li><p>조회수</p><p><?=number_format($totRecord)?> 건</p></li>      
                    <li><p>수수료 합계</p><p><?= $s_grp_flag==1 ? number_format($tot_leader_comm) : number_format($tot_sa_comm) ?> 원</p></li>
                    <?php if ($s_grp_flag) { ?> 
                    <li><p>비율(본사:영업)</p><p><?=10 - $s_grp_comm_rate.' : '.$s_grp_comm_rate?></p></li>
                    <?php }?>
                </ul>
                <div class="oi_inputArea">
                    <div class="recentTableBox">
                        <table>
                            <tr class="ret_tr">
                                <td class="re_top">고유번호</td>
                                <td class="re_top">사용처</td>
                                <?php if ($s_grp_flag) { ?> 
                                <td class="re_top">추천인</td>
                                <?php }?>
                                <td class="re_top">거래금액</td>
                                <?php if ($s_grp_flag) { ?> 
                                <td class="re_top">전체수수료</td>
                                <td class="re_top">영업수수료</td>
                                <td class="re_top">수수료(그룹장)</td>
                                <?php }?>
                                <td class="re_top"><?=$s_grp_flag==1 ? '수수료(사원)' : '수수료'?></td>
                                <td class="re_top">판매날짜</td>
                            </tr>
                            <?php

                                $query = 
                                    "
                                        SELECT a.chain_name
                                                , a.recommender
                                                , b.price
                                                , b.commission
                                                , b.reg_date
                                                , b.seq_no
                                                , c.comm_rate
                                                , c.grp_flag
                                                , d.comm_rate AS grp_comm_rate
                                                , (b.commission * 0.1 * d.comm_rate) AS grp_comm
                                                , if(c.grp_flag=1, b.commission * 0.1 * d.comm_rate, (b.commission * 0.1 * d.comm_rate) - ((commission * 0.1 * d.comm_rate) * 0.01 * c.comm_rate)) AS leader_comm
                                                , if(c.grp_flag=1, 0, (commission * 0.1 * d.comm_rate) * 0.01 * c.comm_rate) AS sa_comm
                                        FROM info_service AS a
                                            JOIN commission_history AS b
                                                ON a.token = b.auth_key
                                            JOIN sales_member AS c
                                                ON a.recommender = c.user_id
                                            JOIN sales_group_list d
                                                ON c.grp = d.grp
                                        {$where_cond}
                                        ORDER BY b.reg_date DESC
                                        LIMIT {$startRow}, {$pageSize}
                                    ";


                                $statement = $db->prepare($query);
                                $statement->bindValue(':grp', $s_grp);
                                $statement->execute();

                                while($row = $statement->fetch(PDO::FETCH_ASSOC)){

                                    $seq_no        = isset($row['seq_no'])        ? $row['seq_no']        : '';
                                    $recommender   = isset($row['recommender'])   ? $row['recommender']   : '';
                                    $chain_name    = isset($row['chain_name'])    ? $row['chain_name']    : '';
                                    $commission    = isset($row['commission'])    ? $row['commission']    : '';
                                    $reg_date      = isset($row['reg_date'])      ? $row['reg_date']      : '';
                                    $comm_rate     = isset($row['comm_rate'])     ? $row['comm_rate']     : '';
                                    $grp_flag      = isset($row['grp_flag'])      ? $row['grp_flag']      : '';
                                    $grp_comm      = isset($row['grp_comm'])      ? $row['grp_comm']      : '';
                                    $grp_comm_rate = isset($row['grp_comm_rate']) ? $row['grp_comm_rate'] : '';
                                    $leader_comm   = isset($row['leader_comm'])   ? $row['leader_comm']   : '';
                                    $sa_comm       = isset($row['sa_comm'])       ? $row['sa_comm']       : '';
                                    $price         = isset($row['price'])         ? $row['price']         : '';

                                    $str_comm_rate = $grp_flag==1 ? '(100%)' : '(' . $comm_rate . '%)';

                            ?>
                            <tr class="rem_tr">
                                <td class="re_mid"><?=$seq_no?></td>
                                <td class="re_mid"><?=$chain_name?></td>
                                <?php if ($s_grp_flag) { ?> 
                                <td class="re_mid"><?=$recommender.$str_comm_rate?></td>
                                <?php }?>
                                <td class="re_mid"><?=number_format($price)?></td>
                                <?php if ($s_grp_flag) { ?> 
                                <td class="re_mid"><?=number_format($commission)?></td>
                                <td class="re_mid"><?=number_format($grp_comm)?></td>
                                <td class="re_mid"><?=number_format($leader_comm)?></td>
                                <?php }?>
                                <td class="re_mid"><?=number_format($sa_comm)?></td>
                                <td class="re_mid"><?=$reg_date?></td>
                            </tr>
                            <?php } // end while ?>
                        </table>
                        <div class="d-flex justify-content-center"><?=$pagination?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>