<?php
    include_once "../inc.php";
    session_start();
    
?>

<!DOCTYPE html>
<html lang="ko">
    <head>
    <meta charset="utf-8">
        <title>apigather 영업사</title>
        <meta content="width=device-width, initial-scale=1.0, user-scalable=0" name="viewport">
        <meta name="format-detection" content="telephone=no">
        <meta http-equiv="Expires" content="10">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Cache-Control" content="no-cache">
        <meta http-equiv="Content-Type" content="text/xml;charset=utf-8">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700;900&display=swap" rel="stylesheet">
        <style>body{font-family:"Noto Sans KR",sans-serif;width:100%;height:100%;margin:0;padding:0;display:flex;justify-content:center;align-items:center;position:absolute}.login{width:350px;background:#fff}.login h1{margin:30px 0 50px;color:#333;font-size:20px}.login input{font-size:15px;color:#333;width:100%;outline:none;background:none;height:50px;border-bottom:2px solid #adadad;position:relative;margin:30px 0}.login .logo{height:50px}.login .logo img{height:100%}.login button{display:block;border:none;background:#333;color:#fff;cursor:pointer;transition:.5s;font-size:15px;float:right;padding:10px 15px}.login button:hover{opacity:.8}input{text-indent:10px;border:0}input:focus{background:#fafafa!important}::placeholder{color:#adadad}@media screen and (max-width:500px){form.login{padding:0 30px}}</style>
    </head>
    <body>
        <form action="./action/login_action.php" class="login" method="post">
            <div class="logo"><img src="assets/img/logo.png"></div>
            <h1>영업사 계정에 로그인합니다.</h1>
                <input name="id" type="text" placeholder="아이디">
                <input name="pwd" type="password" placeholder="비밀번호">
            <button type="submit">로그인</button>
        </form>
    </body>
</html>