<?php

echo json_encode(fetchTitles($_POST['year']));

function fetchTitles($input) {
    require_once("config.php");
    require_once("lib/adodb5/adodb.inc.php");

    $db = NewADOConnection('mysqli');
    $db->Connect(HOSTNAME, USERNAME, PASSWORD, DBNAME);
    $db->SetCharSet('utf8');

    $year = $db->qstr($input);

    $query_column_values = "SELECT `title` FROM `course` WHERE `year` = $year";
    $result_column_values = $db->GetAll($query_column_values) or die ("Chyba v query: $query_column_values " . $db->ErrorMsg());

    return ($result_column_values);
}
?>