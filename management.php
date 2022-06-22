
<?php 

    include('header.php');
    include_once "../db_connecter.php";
    include_once "../paging.php";

    $conn = new Database_Connecter();
    $paging = new Paging();
    $db = $conn->MYSQL_ConnectServer();


    // 세션 파라미터
    $session_user_id = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : '';
    $session_grp = isset($_SESSION['Grp']) ? $_SESSION['Grp'] : '';
    $session_grp_flag = isset($_SESSION['GrpFlag']) ? $_SESSION['GrpFlag'] : '';
    $_csrfToken = isset($_SESSION['_csrfToken']) ? $_SESSION['_csrfToken'] : '';
    $str_grp = $session_grp_flag==1 ? '(그룹장)' : '';


    //-----------------------
    // 페이징 관련
    //-----------------------
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $pageSize = 50;
    $startRow = ($page-1) * $pageSize;
    $url = $_SERVER['PHP_SELF'];

    // 쿼리
    try {

        // 잔액
        $query = "SELECT share FROM sales_member WHERE user_id = :user_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':user_id', $session_user_id);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        $share = isset($row['share']) && !empty($row['share']) ? $row['share'] : 0;


        //페이징 레코드
        $query = "SELECT * FROM sales_qr_pin_list WHERE user_id = :user_id ORDER BY reg_date DESC";
        $statement = $db->prepare($query);
        $statement->bindValue(':user_id', $session_user_id);
        $statement->execute();
        $totRecord = $statement->rowCount();

        $config = array(
          'base_url' => $url,
          'page_rows' => $pageSize,
          'total_rows' => $totRecord
        );
    
        $paging->initialize($config);
        $pagination = $paging->create();
        

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
            <h1>잔액관리</h1>
        </div>
        <div class="cont management">
            <div class="contInput">
                <form class="frm" name="frm" id="frm" onsubmit="return form_check(this);">
                    <input type="number" name="price" placeholder="금액" step="1000" min="0">
                    <input type="hidden" name="hap" value="<?=$share?>" />
                    <input type="hidden" name="_csrfToken" value="<?=$_csrfToken?>" />
                    <button type="submit" class="btn">QR핀코드 추출</button>
                </form>
            </div>
            <ul class="flexBox">
                <li><p>현재잔액:</p><p><?=number_format($share)?></p>원</li>      
            </ul>
            <div class="contTable">
                <table>
                    <thead>
                        <tr>
                            <td>번호</td>
                            <td>상품권종류</td>
                            <td>이전잔액</td>
                            <td>요청금액</td>
                            <td>갱신잔액</td>
                            <td>QR핀코드</td>
                            <td>등록날짜</td>
                        </tr> 
                    </thead>
                    <?php
                        while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                            $seq_no = isset($row['seq_no']) ? $row['seq_no'] : 0;
                            $product_no = isset($row['product_no']) ? $row['product_no'] : '';
                            $product_name = isset($row['product_name']) ? $row['product_name'] : '';
                            $before_price = isset($row['before_price']) ? $row['before_price'] : 0;
                            $price = isset($row['price']) ? $row['price'] : 0;
                            $now_price = isset($row['now_price']) ? $row['now_price'] : 0;
                            $token = isset($row['token']) ? $row['token'] : '';
                            $reg_date = isset($row['reg_date']) ? $row['reg_date'] : '';
                    ?>
                    <tbody>
                        <tr>
                            <td><?=$seq_no?></td>
                            <td><?=$product_name?></td>
                            <td><?=number_format($before_price)?></td>
                            <td><?=number_format($price)?></td>
                            <td><?=number_format($now_price)?></td>
                            <td><?=$token?></td>
                            <td><?=$reg_date?></td>
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