<?php
if(!empty($_FILES)) {

    $csv = $_FILES['csv_file'];
    $file_target = "files/" . $csv['name'];

    if($csv['size'] < 2 * 1024 * 1024) {
        move_uploaded_file($csv['tmp_name'], $file_target);
    }


    $file_content = array();
    if (($handle = fopen($file_target, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $file_content[] = $data;
        }
        fclose($handle);
    }

    $col_headers = $file_content[0];
    $col_values = array();
    for($i = 1; $i < sizeof($file_content); $i++) {
        $col_values[] = $file_content[$i];
    }

    $db = initDBConnection();

    $course_title = $_POST['course_title'];
    $course_year = $_POST['course_year'];
    $course_id;

    if(!courseAlreadyExists($db, $course_title, $course_year)) {
        addNewCourse($db, $course_title, $course_year);
        $course_id = $db->insert_Id();
    } else {
        $course_id = getCourseId($db, $course_title, $course_year);
    }
    addCourseDataColumns($db, $course_id, $col_headers, $col_values);
}

function courseAlreadyExists($db, $course_title, $course_year) {
    $course_title = $db->qstr($course_title);
    $course_year = $db->qstr($course_year);

    $query_course_exists = "SELECT count(*) as course_count FROM course WHERE title = $course_title AND year = $course_year";
    $result_row = $db->GetRow($query_course_exists);
    return $result_row['course_count'] != "0";
}

function addNewCourse($db, $course_title, $course_year) {
    $course_title = $db->qstr($course_title);
    $course_year = $db->qstr($course_year);

    $query_insert_course = "INSERT INTO course (title, year) VALUES ($course_title, $course_year)";
    $result_insert_course = $db->Execute($query_insert_course) or die ("Chyba v query: $query_insert_course " . $db->ErrorMsg());
}

function getCourseId($db, $course_title, $course_year) {
    $course_title = $db->qstr($course_title);
    $course_year = $db->qstr($course_year);

    $query_select_course = "SELECT * FROM course WHERE title = $course_title AND year = $course_year";
    return $db->GetRow($query_select_course)['id'];

}
function addCourseDataColumns($db, $course_id, $col_headers, $col_values) {
    foreach($col_values as $row) {
        $student_id = $row[0];
        $student_name_parts = explode(" ", $row[1]);
        $student_name = $student_name_parts[1];
        $student_surname = $student_name_parts[0];

        addStudent($db, $student_id, $student_name, $student_surname);

        //insert student columns data
        $column_order_index = 0;
        for ($i = 2; $i < sizeof($row); $i++) {
            $col_title = $db->qstr($col_headers[$i]);
            $col_data = $db->qstr($row[$i]);

            $query_insert_data_column = "INSERT INTO data_column (student_id, course_id, column_index, title, data) 
                                         VALUES ($student_id, $course_id, $column_order_index, $col_title, $col_data) 
                                         ON DUPLICATE KEY UPDATE data = $col_data";
            $result_insert_data_column = $db->Execute($query_insert_data_column) or die ("Chyba v query: $query_insert_data_column " . $db->ErrorMsg());
            $column_order_index++;
        }
    }
}


function addStudent($db, $student_id, $name, $surname) {
    $student_id = $db->qstr($student_id);
    $name = $db->qstr($name);
    $surname = $db->qstr($surname);

    $query_insert_student = "INSERT INTO student (id, name, surname) VALUES ($student_id, $name, $surname) ON DUPLICATE KEY UPDATE name = $name, surname = $surname";
    $result_insert_student = $db->Execute($query_insert_student) or die ("Chyba v query: $query_insert_student " . $db->ErrorMsg());
}


function initDBConnection() {
    require_once("config.php");
    require_once("lib/adodb5/adodb.inc.php");

    $db = NewADOConnection('mysqli');
    $db->Connect($hostname, $username, $password, $dbname);
    $db->SetCharSet('utf8');

    return $db;
}

?>

<html>
<head>

</head>
<body>

<form action="admin.php" method="post" enctype="multipart/form-data"><br>
    Vyber školský rok:

    <select name="course_year">
        <option value="2016">2016</option>
        <option value="2017">2017</option>
        <option value="2018">2018</option>
        <option value="2019">2019</option>
    </select><br>
    Zadaj názov predmetu:
    <input type="text" name="course_title"><br>

    Oddeľovač v CSV súbore:blablabla
    <select name="delimiter">
        <option value="period">.</option>
        <option value="semicolon">;</option>
    </select><br>

    Vyber CSV súbor s výsledkami:<br>
    <input type="file" name="csv_file"><br>

    <input type="submit" value="Upload" name="submit">
</form>

</body>
</html>