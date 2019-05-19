<?php


if (isset($_POST)) {
    session_start();
    $id_bridge = htmlspecialchars($_POST['id_bridge']);
    $bridgeArray = $_POST['bridgeArray'];
    $sumTeamPoints = $_POST['sumTemPoints'];
    $agreement = $_POST['agreement'];
    $user_id = $_SESSION['user_id'];
    $pointsPerStudentTotalSum = 0;

    $pointsPerStudent = array();
    foreach ($bridgeArray as $value) {
        $pointsPerStudent[$value] = $_POST[$value];
    }


    try {
        include_once("../config.php");
        $conn = new PDO("mysql:host=$serverName;dbname=$dbName", $userName, $password);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        die();
    }
    $stmt = $conn->prepare("SET NAMES 'utf8';");
    $stmt->execute();

    foreach ($pointsPerStudent as $value) {
        $pointsPerStudentTotalSum += $value;
    }

    if (htmlspecialchars($_POST['oneAgree']) != 1) {
        if (intval($pointsPerStudentTotalSum) != intval($sumTeamPoints)) {

            header("Location: http://147.175.121.210:8117/webte2/uloha2/loggedUser.php?error=bad-points");
            die();
        }
    }


    if ($agreement != 1 && $agreement != -1) {
        header("Location: http://147.175.121.210:8117/webte2/uloha2/loggedUser.php?error=bad-agreement");
        die();
    }

    function is_decimal($val)
    {
        return is_numeric($val) && floor($val) != $val;
    }

    foreach ($pointsPerStudent as $key => $value) {
        if (is_decimal($value)) {
            header("Location: http://147.175.121.210:8117/webte2/uloha2/loggedUser.php?error=bad-number");
            die();
        }
    }

    foreach ($pointsPerStudent as $key => $value) {
        $stmt = $conn->prepare("UPDATE task2_students_subject 
                                      SET task2_students_subject.body=$value
                                      WHERE task2_students_subject.id=$key");
        $stmt->execute();


        $stmt = $conn->prepare("UPDATE task2_students_subject
                                      SET task2_students_subject.agree=$agreement
                                      WHERE task2_students_subject.id=$key AND task2_students_subject.id_student=$user_id");
//        echo "<pre>";
//        var_dump($stmt);
//        echo "</pre>";
        $stmt->execute();
    }


    header("Location: http://147.175.121.210:8117/webte2/uloha2/loggedUser.php?success=true");
}