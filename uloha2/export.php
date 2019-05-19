<?php
session_start();
if (!isset($_COOKIE['isAdmin']) and $_COOKIE['isAdmin'] !== 'admin') {
    header("Location:login.php");
}
include_once("../config.php");
$conn = new mysqli($serverName, $userName, $password, $dbName);
if ($conn->connect_error) {
    die("Connection to MySQL server failed. " . $conn->connect_error);
}



if (isset($_SESSION['Subject']) && !empty($_SESSION['Subject']) && (isset($_SESSION['Year']) && !empty($_SESSION['Year']))) {
    /*Initializing output file to which the generated data will be written*/
//        if(($_COOKIE['lang'] == 'sk') or (!isset($_COOKIE['lang']))) {
//            $download_file_name = "hesla.csv";
//        }else{
//            $download_file_name = "passwords.csv";
//        }

    $download_file_name = "students.csv";

    $index = 0;
    $csv_output = fopen($download_file_name, 'w');
    $csv_array = array();
    $data = array();
    $data[0] = "ID";
    $data[1] = "Name";
    $data[2] = "Points";
    $csv_array[$index] = $data;
    fputcsv($csv_output, $csv_array[$index], ";");

    $stmt = $conn->prepare("SET NAMES 'utf8';");
    $stmt->execute();
    $querry = "SELECT task2_students.id,task2_students.name,task2_students_subject.body FROM task2_students INNER JOIN task2_students_subject on task2_students.id=task2_students_subject.id_student INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id where  task2_subject.subject='" . $_SESSION['Subject'] . "' AND task2_subject.year='" . $_SESSION['Year'] . "'";
    $result = $conn->query($querry);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $index++;
            $data[0] = $row['id'];
            $data[1] = $row['name'];
            $data[2] = $row['body'];
            $csv_array[$index] = $data;
            fputcsv($csv_output, $csv_array[$index], ";");
        }
    } else {
        $data[0] = "chyba";
        $data[1] = "chyba";
        $data[2] = "chyba";
        $index++;
        fputcsv($csv_output, $csv_array[$index], ";");

    }


    /*Writing generated line to the ouptut csv*/

    fclose($csv_output);


    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="' . $download_file_name . '"');
    readfile($download_file_name);
    unlink($download_file_name);
}


?>