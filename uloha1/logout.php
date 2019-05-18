<?php

session_start();

if(isset($_SESSION['uloha1_user'])) {
    session_destroy();
    header("Location:http://147.175.121.210:8117/webte2/index.php");
}
if(isset($_COOKIE['isAdmin'])) {
    setcookie('isAdmin', 'admin', time() - 36000, '/');
    header('Refresh: 2; URL=http://147.175.121.210:8117/webte2/index.php');
}

?>
