<?php

if(isset($_POST['course_title']) and ($_POST['course_title'] != "")) {

    switch($_POST['submit']) {
        case 'show':
            showCourseResults();
            break;
        case 'delete':
            deleteCourse();
            break;
        case 'print_to_pdf':
            printToPDF();
            break;
    }
}

function showCourseResults() {
    $course_title = $_POST['course_title'];
    $course_year = $_POST['course_year'];

    echo buildHTMLtable(getCourseResults($course_title, $course_year));
}

function getCourseResults($course_title, $course_year) {
    $db = initDBConnection();

    $course_title = $db->qstr($course_title);
    $course_year = $db->qstr($course_year);

    $query_select_results = "SELECT student.id, student.name AS student_name, student.surname AS student_surname, data_column.column_index, data_column.title AS column_title, data_column.data AS column_data
                                FROM data_column
                                JOIN student ON data_column.student_id = student.id
                                JOIN course ON data_column.course_id = course.id
                                WHERE data_column.course_id = 
                                    (SELECT id FROM course WHERE course.title = $course_title and course.year = $course_year)
                                ORDER BY student.id, data_column.column_index ASC";

    return $db->GetAll($query_select_results);
}

function deleteCourse() {
    $db = initDBConnection();

    $course_title = $db->qstr($_POST['course_title']);
    $course_year = $db->qstr($_POST['course_year']);
    $query_delete_course = "DELETE FROM course WHERE course.title = $course_title AND course.year = $course_year";
    $db->Execute($query_delete_course) or die ("Chyba v query: $query_delete_course " . $db->ErrorMsg());
}

function printToPDF() {
    require_once ('lib/mpdf/mpdf.php');

    $course_title = $_POST['course_title'];
    $course_year = $_POST['course_year'];

    $mpdf=new mPDF('c','A4-L','','',32,25,27,25,16,13);
    $css = "table { border: 2px solid black; border-collapse: collapse; } td, th { padding: 4px; border: 1px solid black; text-align: center; }";
    $mpdf->useOnlyCoreFonts = true;
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->WriteHTML($css,1);
    $mpdf->WriteHTML(buildHTMLtable(getCourseResults($course_title, $course_year), 2));
    $mpdf->Output('mpdf.pdf','I');
    exit;
}

function buildHTMLtable($data) {
    $x = "";
    $x .= "<table>";

    // echo table headers
    $index = 0;
    $x .= "<tr>";
    $x .= "<th>" . "ID študenta" . "</th><th>" . "Meno a priezvisko" . "</th>";

    do {
        $x .= "<th>" . $data[$index]['column_title'] . "</th>";
        $index++;
    } while($data[$index]['column_index'] != '0');

    $x .= "</tr>";

    //echo table content
    $data_block_length = $index;

    $data_blocks = array_chunk($data, $data_block_length);

//    prettyPrintArray($data_blocks);

    foreach($data_blocks as $student_data) {
        $x .= "<tr>";
        $x .= "<td>" . $student_data[0]['id'] . "</td>";
        $x .= "<td>" . $student_data[0]['student_name'] . " " . $student_data[0]['student_surname'] . "</td>";
        foreach ($student_data as $item) {
            $x .= "<td>" . $item['column_data'] . "</td>";
        }
        $x .= "</tr>";
    }

    $x .= "</table>";

    return $x;
}

function initDBConnection() {
    require_once("config.php");
    require_once("lib/adodb5/adodb.inc.php");

    $db = NewADOConnection('mysqli');
    $db->Connect(HOSTNAME, USERNAME, PASSWORD, DBNAME);
    $db->SetCharSet('utf8');

    return $db;
}

function prettyPrintArray($array) {
    echo "<pre>";
    print_r($array);
    echo "</pre>";
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

    <button name='submit' value='show'>Zobraziť výsledky</button>
    <button name='submit' value='delete'>Vymazať výsledky</button>
    <button name='submit' value='print_to_pdf'>Vytlačiť do PDF</button>
</form>

</body>
</html>