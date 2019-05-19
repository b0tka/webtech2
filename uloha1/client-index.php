<?php
session_start();
if(!(isset($_SESSION['uloha1_user']))) {
    header("Location:http://147.175.121.210:8117/webte2/uloha1/index.php");
}


$db = initDBConnection();
$id_from_session = $_SESSION['uloha1_user'];

$courses_of_student = getStudentCoursesIDs($db, $id_from_session);


function getStudentCoursesIDs($db, $student_id) {
    $student_id = $db->qstr($student_id);

    $query_student_courses = "SELECT `course_id` FROM `data_column` WHERE `student_id` = $student_id GROUP BY `course_id`";
    $result_student_courses = $db->Execute($query_student_courses) or die ("Chyba v query: $query_student_courses " . $db->ErrorMsg());

    $courses_id_array = array();
    foreach($result_student_courses as $item) {
        $courses_id_array[] = $item['course_id'];
    }

    return $courses_id_array;
}

function getCourseData($db, $student_id, $course_id) {
    $student_id = $db->qstr($student_id);
    $course_id = $db->qstr($course_id);

    $query_select_course_data = "SELECT student.name AS student_name, student.surname AS student_surname, course.title AS course_title, course.year AS course_year, data_column.column_index, data_column.title AS column_title, data_column.data AS column_data
                                FROM data_column
                                JOIN student ON data_column.student_id = student.id
                                JOIN course ON data_column.course_id = course.id
                                WHERE data_column.student_id = $student_id AND data_column.course_id = $course_id  
                                ORDER BY data_column.column_index ASC";

    $result_select_course_data = $db->GetAll($query_select_course_data) or die ("Chyba v query: $query_select_course_data " . $db->ErrorMsg());

    return $result_select_course_data;

}

function echoCourseTable($data) {
    $row = $data[0];

    $student_full_name = $row['student_name'] . " " . $row['student_surname'];
    if($_COOKIE['lang'] == 'en') {
        $text_prefix  = "Course results for ";
    } else {
        $text_prefix = "Výsledky z predmetu ";
    }
    $course_title =  $text_prefix . $row['course_title'] . " " . $row['course_year'];

    echo "<h2>" . $course_title . "</h2><br>";
    echo $student_full_name;
    echo "<table class=\"table table-hover\">";
    echo "<tr>";
    foreach($data as $e) {
        echo "<th  scope=\"col\">" . $e['column_title'] . "</th>";
    }
    echo "</tr>";
    foreach($data as $e) {
        echo "<td>" . $e['column_data'] . "</td>";
    }
    echo "</table>";
}

function initDBConnection() {
    require_once("config.php");
    require_once("lib/adodb5/adodb.inc.php");

    $db = NewADOConnection('mysqli');
    $db->Connect(HOSTNAME, USERNAME, PASSWORD, DBNAME);
    $db->SetCharSet('utf8');

    return $db;
}

?>

<?php
if(isset($_GET['lang'])) {
    setcookie('lang', htmlspecialchars($_GET['lang']), strtotime("tomorrow"), '/');
    header("Location:client-index.php");
}

if(($_COOKIE['lang'] == 'sk') or (!isset($_COOKIE['lang']))) {

?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Úloha 1</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav class="navbar navbar-light navbar-custom navbar-expand-lg">
        <a class="navbar-brand" href="../index.php">
            <i class="material-icons nav-icon pt-2">home</i>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../uloha1/index.php">Úloha 1</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../uloha2/index.php">Úloha 2</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../uloha3/admin-index.php">Úloha 3</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        Jazyk
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="?lang=sk">Slovenský</a>
                        <a class="dropdown-item" href="?lang=en">Anglický</a>
                    </div>
                </li>
            </ul>
            <span class="navbar-text text-right text-white">
                        Používateľ : <?php echo $_SESSION['uloha1_username']; ?>
                </span>
            <a href="logout.php"><i class="material-icons nav-icon pt-2">exit_to_app</i></a>
        </div>
    </nav>
</header>

<div class="container-fluid root-container mt-3">
    <main>
        <div class="container mt-5 px-5">

            <?php
            foreach ($courses_of_student as $course_id) {
                $course_data = getCourseData($db, $id_from_session, $course_id);
                echoCourseTable($course_data);
            }
            ?>

        </div>
    </main>
</div>

<footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
    <span class="text-white pd-top">Vývojári : LR, DV, MM, SR, MR</span>
</footer>

<?php
} elseif($_COOKIE['lang'] == 'en') {
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Task 1</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav class="navbar navbar-light navbar-custom navbar-expand-lg">
        <a class="navbar-brand" href="./index.php">
            <i class="material-icons nav-icon pt-2">home</i>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../uloha1/index.php">Task 1</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../uloha2/index.php">Task 2</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../uloha3/admin-index.php">Task 3</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        Language
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="?lang=sk">Slovak</a>
                        <a class="dropdown-item" href="?lang=en">English</a>
                    </div>
                </li>
            </ul>
            <span class="navbar-text text-right text-white">
                        Username : <?php echo $_SESSION['uloha1_username']; ?>
                </span>
            <a href="logout.php"><i class="material-icons nav-icon pt-2">exit_to_app</i></a>
        </div>
    </nav>
</header>

<div class="container-fluid root-container mt-3">
    <main>
        <div class="container mt-5 px-5">

            <?php
            foreach ($courses_of_student as $course_id) {
                $course_data = getCourseData($db, $id_from_session, $course_id);
                echoCourseTable($course_data);
            }
            ?>

        </div>
    </main>
</div>

<footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
    <span class="text-white pd-top">Developed by : LR, DV, MM, SR, MR</span>
</footer>

<?php

}

?>

</body>
</html>