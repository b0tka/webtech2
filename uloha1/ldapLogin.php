<?php
session_start();
if (isset($_POST)) {

    $name = htmlspecialchars(trim($_POST['login']));
    $localPassword = trim($_POST['password']);

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


        require_once("config.php");
        require_once("lib/adodb5/adodb.inc.php");

        $db = NewADOConnection('mysqli');
        $db->Connect(HOSTNAME, USERNAME, PASSWORD, DBNAME);
        $db->SetCharSet('utf8');

        $query_select_student = "SELECT id FROM student WHERE id = $usrId";

        $result_select_student = $db->GetRow($query_select_student) or die ("Chyba v query: $query_select_student " . $db->ErrorMsg());


        if($result_select_student) {
            $_SESSION['uloha1_username'] = $ldapuid;
            $_SESSION['uloha1_user'] = $result_select_student['id'];
            header("Location: http://147.175.121.210:8117/webte2/uloha1/client-index.php");
        } else {
            header("Location: http://147.175.121.210:8117/webte2/uloha1/index.php?error=invalid-user");
        }

    } else {
        header("Location: http://147.175.121.210:8117/webte2/uloha1/index.php?error=invalid-user");
    }

}

