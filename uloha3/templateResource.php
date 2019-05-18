<?php
/**
 * Created by PhpStorm.
 * User: Stefan Renczes
 * Date: 5/17/2019
 * Time: 18:49
 */
include_once("../config.php");

/*Getting information from services call*/
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$uriParsed = explode("/", $uri);
/*Setting upp connection to projekt-uloha1 database*/
$conn = new mysqli($serverName, $userName, $password, $dbName);
if ($conn->connect_error) {
    die("Connection to MySQL server failed. " . $conn->connect_error);
}

/*Service function to get template from database by it's ID*/
function getTemplateTextByID($id, $conn){
    $sqlTemplate = "SELECT * FROM template WHERE id=".$id;
    $resultTemplate = $conn->query($sqlTemplate);
    $template = "ZIADNY VYSLEDOK";
    if ($resultTemplate->num_rows > 0) {
        while($row = $resultTemplate->fetch_assoc()) {
            $template = $row["content"];
        }
    }
    return $template;
}

$template = getTemplateTextByID($uriParsed[5], $conn);

echo $template;
?>