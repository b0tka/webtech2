<?php

$db = initDBConnection();
$temp_id = "78654";

$courses_of_student = getStudentCoursesIDs($db, $temp_id);


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
from data_column
join student on data_column.student_id = student.id
join course on data_column.course_id = course.id
where data_column.student_id = $student_id AND data_column.course_id = $course_id  
ORDER BY data_column.column_index ASC";

    $result_select_course_data = $db->GetAll($query_select_course_data) or die ("Chyba v query: $query_select_course_data " . $db->ErrorMsg());

    return $result_select_course_data;

//    echo "<pre>";
//print_r($result_select_course_data);
//    echo "</pre>";
}

function echoCourseTable($data) {
    $row = $data[0];

    $student_full_name = $row['student_name'] . " " . $row['student_surname'];
    $course_title =  "Výsledky z predmetu " . $row['course_title'] . " " . $row['course_year'];

    echo "<h2>" . $course_title . "</h2><br>";
    echo $student_full_name;
    echo "<table>";
    echo "<tr>";
    foreach($data as $e) {
        echo "<th>" . $e['column_title'] . "</th>";
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

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Zadanie 3</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<?php
    foreach ($courses_of_student as $course_id) {
        $course_data = getCourseData($db, $temp_id, $course_id);
        echoCourseTable($course_data);
    }
?>
</body>
</html>
