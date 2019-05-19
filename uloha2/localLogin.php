<?php

try {
    include_once("../config.php");
    $conn = new PDO("mysql:host=$serverName;dbname=$dbName", $userName, $password);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
$stmt = $conn->prepare("SET NAMES 'utf8';");
$stmt->execute();

$stmt = $conn->prepare("SELECT password FROM task2_students WHERE id=$name");
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);


$result = $stmt->fetchColumn();


if (sha1($localPassword) === $result) {

    $stmt = $conn->prepare("SELECT * FROM task2_students WHERE id=$name");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $result = $stmt->fetchAll();

    session_start();
    $_SESSION['valid_user'] = true;
    $_SESSION['user_id'] = $name;
    $_SESSION['user_data'] = $result[0];
    header("Location: http://147.175.121.210:8117/webte2/uloha2/loggedUser.php?lang=" . $_COOKIE['lang']);


} else {
    header("Location: http://147.175.121.210:8117/webte2/uloha2/index.php?error=invalid-user");
}
