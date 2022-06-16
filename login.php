<?php
    include_once "../inc.php";
    session_start();
    
?>

<!DOCTYPE html>
<html lang="ko">
    <head>
        <title>apigather 영업사 로그인</title>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Language" content="ko">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700;900&family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&display=swap" rel="stylesheet">
        <style>
            body{font-family:'Noto Sans KR','Open Sans',sans-serif;height:100%;margin:0;padding:0}
            #login{width:350px;background:#fff;padding:120px 50px 150px;position:relative;margin:0 auto}
            #login h1{margin:30px 0 60px;color:#333;font-size:22px;line-height:1.2}
            #login .inputBox{border-bottom:2px solid #adadad;position:relative;margin:30px 0}
            #login .inputBox input{font-size:15px;color:#333;border:none;width:100%;outline:none;background:none;height:40px;text-indent:10px}
            #login .inputBox span::after{content:'';position:absolute;width:0;height:2px;background:linear-gradient(120deg,#2196F3,#FF5722);transition:.5s}
            #login .logoLog{height:50px}
            #login .logoLog img{height:100%}
            #login button{display:block;border:none;background:#333;color:#fff;cursor:pointer;transition:.5s;font-size:15px;float:right;padding:10px 15px}
            #login button:hover{opacity:.8}
            input{line-height:28px;vertical-align:top;text-indent:10px;margin-top:13px}
            input:focus, 
            select:focus{background:#fafafa}
            ::placeholder{color:#adadad}
            @media screen and (max-width:500px){
                form#login{width:100%;margin:0;padding:10px 20px}
                form#login h1{font-size:20px;height:10%;margin:50px 0}
                form#login .inputBox{margin:30px 0;font-size:20px}
                form#login .inputBox input{font-size:15px;padding:0 5%;height:30px}
                form#login .login-btn{font-size:15px;margin-top:0;z-index:99999999}
            }
        </style>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" charset="utf-8"></script>
    </head>
    <body>
        <form action="./action/login_action.php" id="login" method='post'>
            <div class='logoLog'><img src='assets/img/logo.png'></div>
            <h1>영업사 계정에 로그인합니다.</h1>
            <div class="inputBox">
                <input name='id' type="text" placeholder="아이디"><span></span>
            </div>
            <div class="inputBox">
                <input name='pwd' type="password" placeholder="비밀번호"><span></span>
            </div>
            <button type="submit">로그인</button>
        </form>
    </body>
</html>