<?php

if(isset($_POST['course_title']) and ($_POST['course_title'] != "")) {

    $db = initDBConnection();

    $course_title = $_POST['course_title'];
    $course_year = $_POST['course_year'];

    $course_title = $db->qstr($course_title);
    $course_year = $db->qstr($course_year);

    $query_select_results = "SELECT student.id, student.name AS student_name, student.surname AS student_surname, data_column.column_index, data_column.title AS column_title, data_column.data AS column_data
                                FROM data_column
                                JOIN student ON data_column.student_id = student.id
                                JOIN course ON data_column.course_id = course.id
                                WHERE data_column.course_id = 
                                    (SELECT id FROM course WHERE course.title = $course_title and course.year = $course_year)
                                ORDER BY student.id, data_column.column_index ASC";

    $result_select_results = $db->GetAll($query_select_results);

    echoTable($result_select_results);
}




function courseAlreadyExists($db, $course_title, $course_year) {
    $course_title = $db->qstr($course_title);
    $course_year = $db->qstr($course_year);

    $query_course_exists = "SELECT count(*) as course_count FROM course WHERE title = $course_title AND year = $course_year";
    $result_row = $db->GetRow($query_course_exists);
    return $result_row['course_count'] != "0";
}



function getCourseId($db, $course_title, $course_year) {
    $course_title = $db->qstr($course_title);
    $course_year = $db->qstr($course_year);

    $query_select_course = "SELECT * FROM course WHERE title = $course_title AND year = $course_year";
    return $db->GetRow($query_select_course)['id'];

}

function echoTable($data) {
    echo "<table>";

    // echo table headers
    $index = 0;
    echo "<tr>";
    echo "<td>" . "ID študenta" . "</td><td>" . "Meno a priezvisko" . "</td>";

    do {
        echo "<th>" . $data[$index]['column_title'] . "</th>";
        $index++;
    } while($data[$index]['column_index'] != '0');

    echo "</tr>";

    //echo table content
    $data_block_length = $index;

    $data_blocks = array_chunk($data, $data_block_length);

//    prettyPrintArray($data_blocks);

    foreach($data_blocks as $student_data) {
        echo "<tr>";
        printTD($student_data[0]['id']);
        printTD($student_data[0]['student_name'] . " " . $student_data[0]['student_surname']);
        foreach ($student_data as $item) {
            printTD($item['column_data']);
        }
        echo "</tr>";
    }

    echo "</table>";

}

function printTD($element) {
    echo "<td>" . $element . "</td>";
}

function prettyPrintArray($array) {
    echo "<pre>";
    print_r($array);
    echo "</pre>";
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

<html>
<head>

</head>
<body>
Zobrazenie výsledkov všetkých študentov pre daný predmet
<form action="results.php" method="post" ><br>
    Vyber školský rok:

    <select name="course_year">
        <option value="ZS 2015/2016">ZS 2015/2016</option>
        <option value="LS 2015/2016">LS 2015/2016</option>
        <option value="ZS 2016/2017">ZS 2016/2017</option>
        <option value="LS 2016/2017">LS 2016/2017</option>
        <option value="ZS 2017/2018">ZS 2017/2018</option>
        <option value="LS 2017/2018">LS 2017/2018</option>
        <option value="ZS 2018/2019">ZS 2018/2019</option>
        <option value="LS 2018/2019">LS 2018/2019</option>
        <option value="ZS 2019/2020">ZS 2019/2020</option>
        <option value="LS 2019/2020">LS 2019/2020</option>
    </select><br>
    Zadaj názov predmetu:
    <input type="text" name="course_title"><br>

    <input type="submit" value="Zobraziť výsledky" name="submit">
</form>

</body>
</html>