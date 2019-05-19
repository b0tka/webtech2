<?php
if (isset($_POST)) {
    $name = htmlspecialchars(trim($_POST['login']));
    $localPassword = trim($_POST['password']);
    $typeOfLogin = htmlspecialchars(trim($_POST['type-of-login']));
    if ($typeOfLogin === "ldap") {
        require_once "ldapLogin.php";
    } elseif ($typeOfLogin === "local") {
        require_once "localLogin.php";
    }
}







