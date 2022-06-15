<?php
    session_start();

    include_once "../../inc.php";

    session_destroy();

    movepage("../login.php");