<?php include('header.php');?>
    <section class="sub_bo_Wrap">
        <div class="subTopBox">
            <div class="subT_SideBox">
                <div class="admin_btn">
                    <p class="btn btn-light"><?=$s_user_id.$str_grp?> 님</p>
                    <button type="button" class="btn btn-danger" onclick="location.replace('./action/logout.php');">로그아웃</button>
                </div>
                <div class="subtName"><h1>정산</h1></div>
            </div>
        </div>
        <div id="process_submit" class="settlement">
            <div class="process_sideBox">
                <form class="frm" name="schfrm" id="schfrm" action="<?=$_SERVER['PHP_SELF']?>">
                    <div>
                        <p class="cell text-center">
                            <input type="month" value="<?=$startDate?>"> 
                            <button type="submit" class="btn go">조회</button>
                        </p> 
                    </div><br><br>
                </form>
                <div class="oi_inputArea">
                    <div class="recentTableBox">
                        <table>
                            <tr class="ret_tr">
                                <td class="re_top">기간</td>
                                <td class="re_top">수수료 합계</td>
                                <td class="re_top">정산</td>
                                <td class="re_top">pin</td>
                            </tr> 
                            <tr class="rem_tr">
                                <td class="re_mid">2022-06-01~2022-06-10</td>
                                <td class="re_mid">1,000</td>
                                <td class="re_mid">
                                    <button>버튼</button>
                                </td>
                                <td class="re_mid">1234</td>
                            </tr>
                            <tr class="rem_tr">
                                <td class="re_mid">2022-06-01~2022-06-10</td>
                                <td class="re_mid">1,000</td>
                                <td class="re_mid">
                                    <button disabled>버튼</button>
                                    <!-- 버튼 태그 안에 disabled 넣으면 비활성화 -->
                                </td>
                                <td class="re_mid">1234</td>
                            </tr>
                        </table>
                        <div class="d-flex justify-content-center"><?=$pagination?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
