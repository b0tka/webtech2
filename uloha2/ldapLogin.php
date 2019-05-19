<?php
$ldapuid = $name;
$ldappass = $localPassword;


$dn = 'ou=People, DC=stuba, DC=sk';
$ldaprdn = "uid=$ldapuid, $dn";
$ldapconn = ldap_connect("ldap.stuba.sk") or die("Cannot connect to stuba.sk");
if ($ldapconn) {
    $set = ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);
}


if ($ldapbind) {

    $sr = ldap_search($ldapconn, $ldaprdn, "uid=" . $ldapuid);
    $entry = ldap_first_entry($ldapconn, $sr);
    $usrId = ldap_get_values($ldapconn, $entry, "uisid")[0];
    $usrName = ldap_get_values($ldapconn, $entry, "givenname")[0];
    $usrSurname = ldap_get_values($ldapconn, $entry, "sn")[0];
    $usrMail = ldap_get_values($ldapconn, $entry, "mail")[2];


    try {
        include_once("../config.php");
        $conn = new PDO("mysql:host=$serverName;dbname=$dbName", $userName, $password);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        die();
    }
    $stmt = $conn->prepare("SET NAMES 'utf8';");
    $stmt->execute();

    $stmt = $conn->prepare("SELECT id FROM task2_students WHERE id=$usrId");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $result = $stmt->fetchColumn();
    if($result) {
        session_start();
        $stmt = $conn->prepare("SELECT * FROM task2_students WHERE id=$usrId");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();

        $_SESSION['valid_user'] = true;
        $_SESSION['user_data'] = $result[0];
        $_SESSION['user_id'] = $usrId;
        header("Location: http://147.175.121.210:8117/webte2/uloha2/loggedUser.php");
    } else {
        header("Location: http://147.175.121.210:8117/webte2/uloha2/index.php?error=invalid-user");
    }


} else {
    header("Location: http://147.175.121.210:8117/webte2/uloha2/index.php?error=invalid-user");

}