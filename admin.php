<?php

if((!empty($_FILES)) && (isset($_POST['course_title'])) && ($_POST['course_title'] != "")) {

    if(($csv_file = validateAndUploadCSVFile($_FILES['csv_file'])) !== FALSE) {

        $file_content = parseCSVFile($csv_file);
        $table_data = splitToHeadersAndValues($file_content);

        $db = initDBConnection();

        $course_title = $_POST['course_title'];
        $course_year = $_POST['course_year'];

        $db->startTrans();

        if (!courseAlreadyExists($db, $course_title, $course_year)) {
            addNewCourse($db, $course_title, $course_year);
            $course_id = $db->insert_Id();
        } else {
            $course_id = getCourseId($db, $course_title, $course_year);
        }

        processCourseData($db, $course_id, $table_data['headers'], $table_data['values']);

        $db->completeTrans();

    }
}

function validateAndUploadCSVFile($uploaded_file) {
    define(MB, 1024 * 1024);

    $target_location = "files/" . $uploaded_file['name'];
    $file_extension = pathinfo($uploaded_file['name'])['extension'];

    if(($uploaded_file['size'] < 1*MB) && ($file_extension == "csv") ) {
        move_uploaded_file($uploaded_file['tmp_name'], $target_location);
        return $target_location;
    } else {
        return FALSE;
    }
}

function parseCSVFile($file) {
    $delimiter = ($_POST['delimiter'] == "semicolon") ? ";" : ",";

    $file_content = array();
    if (($handle = fopen($file, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            $file_content[] = $data;
        }
        fclose($handle);
    }
    return $file_content;
}

function splitToHeadersAndValues($data) {
    $col_headers = $data[0];
    $col_values = array();
    for ($i = 1; $i < sizeof($data); $i++) {
        $col_values[] = $data[$i];
    }
    $result['headers'] = $col_headers;
    $result['values'] = $col_values;

    return $result;
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

function processCourseData($db, $course_id, $col_headers, $col_values) {
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
    $db->Connect(HOSTNAME, USERNAME, PASSWORD, DBNAME);
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

    Oddeľovač v CSV súbore:
    <select name="delimiter">
        <option value="semicolon">  ;  </option>
        <option value="comma">  ,  </option>
    </select><br>

    Vyber CSV súbor s výsledkami:<br>
    <input type="file" name="csv_file"><br>

    <input type="submit" value="Upload" name="submit">
</form>

</body>
</html>