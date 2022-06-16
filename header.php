<?php

    session_start();

    include_once "../inc.php";
    
    if(empty($_SESSION['UserID']) || empty($_SESSION['_csrfToken']) || !check_referer()) {
        movepage("./action/logout.php", "잘못된 접근입니다.");
    }
?>

<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>apigather 영업사</title>
<meta content="width=device-width, initial-scale=1.0, user-scalable=0" name="viewport">
<meta name="format-detection" content="telephone=no">
<link href="assets/img/favicon.ico" rel="icon">
<link href="assets/img/i_logo.png" rel="apple-touch-icon">
<!-- <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet"> -->
<link href="assets/css/base.css" rel="stylesheet">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<script src="assets/vendor/jquery/jquery.min.js"></script>
<script src="assets/vendor/jquery/jquery-migrate.min.js"></script>
<!-- <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script> -->
</head>
<body>
<header id="header">
    <div class="h_bg"></div>
    <div class="inner">
        <h1 class="logo"><a href="fee_detail.php"><img src="assets/img/logo.png" alt="apigather 영업사"></a></h1>
        <div class="gnb" id="gnb">
            <ul>
                <li class="btnNav">
                    <i class="fa fa-bars ico_m" aria-hidden="true"></i>
                    <i class="fa fa-times ico_x" aria-hidden="true"></i>
                </li>
                <li class="nav"><a href="fee_detail.php">수수료 조회</a></li>
                <li class="nav"><a href="settlement.php">정산</a></li>
            </ul>
        </div>
    </div>
</header>
<script>
    $('.btnNav').click(function () {
        $('.nav').fadeToggle('fast');
        $('.gnb').toggleClass('bg');
        $('.ico_x,.ico_m').toggle();
    });
    function maxLengthCheck(object){
        if(object.value.length > object.maxLength) {
            object.value = object.value.slice(0, object.maxLength);
        }
    };
</script>