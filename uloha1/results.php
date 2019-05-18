<?php



if(isset($_POST['course_title']) and ($_POST['course_title'] != "")) {

    switch($_POST['submit']) {
        case 'show':
            $course_results = showCourseResults();
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
    $results = getCourseResults($course_title, $course_year);
//    print_r($results);
//    return buildHTMLtable($results);
    if($results) {
        return buildHTMLtable($results);
    } else {
        return "Predmet neexistuje!";
    }

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
    echo $query_delete_course;
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
    $x .= "<table  class=\"table table-hover\">";

    // echo table headers
    $index = 0;
    $x .= "<tr>";
    $x .= "<th  scope=\"col\">" . "ID študenta" . "</th><th>" . "Meno a priezvisko" . "</th>";

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

function fetchListFromDB($column_name) {
    $db = initDBConnection();
    $column_name = htmlspecialchars($column_name);

    $query_column_values = "SELECT `$column_name` FROM `course` ORDER BY `$column_name`";
    $result_column_values = $db->GetAll($query_column_values) or die ("Chyba v query: $query_column_values " . $db->ErrorMsg());

    return ($result_column_values);
}

function echoOptions($data) {
    foreach ($data as $e) {
        echo "<option value='" . $e[0] . "'>" . $e[0] . "</option>";
    }
}

function prettyPrintArray($array) {
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

?>

<?php
if(isset($_GET['lang'])) {
    setcookie('lang', htmlspecialchars($_GET['lang']), strtotime("tomorrow"), '/');
    header("Location:results.php");
}

if(($_COOKIE['lang'] == 'sk') or (!isset($_COOKIE['lang']))) {

?>


<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ADMIN</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php
if(!isset($_COOKIE['isAdmin']) and $_COOKIE['isAdmin'] !== 'admin')
{
    header("Location:login.php");
}
?>
<header>
    <nav class="navbar navbar-light navbar-custom navbar-expand-lg">
        <a class="navbar-brand" href="./index.php">
            <img src="admin.png" width="100" height="55" alt="admin">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../uloha1/admin-index.php">Úloha 1</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../uloha2/admin-index.php">Úloha 2</a>
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
            <span class="navbar-text text-right text-white">Username : admin &nbsp;</span>
            <a href="logout.php"><i class="material-icons nav-icon pt-2">exit_to_app</i></a>
        </div>
    </nav>
</header>

<div class="container-fluid root-container mt-3">
    <main>
        <div class="container mt-5 px-5">
            <div class="container-fluid">

                <form action="results.php" method="post" ><br>
                    <div class="row">
                        <h3>Zobrazenie výsledkov všetkých študentov pre daný predmet</h3>
                        <div class="col-sm-6" style="margin: 3% auto;">
                            Vyber školský rok:
                            <select name="course_year" class="custom-select">
                                <?php echoOptions(fetchListFromDB('year')); ?>
                            </select>
                        </div>
                        <div class="col-sm-6" style="margin: 3% auto;">
                            Zadaj názov predmetu:
                            <select name="course_title" class="custom-select">
                                <?php echoOptions(fetchListFromDB('title')); ?>
                            </select>
                        </div>
                    </div>
                    <!--Button to submit the form-->
                    <div class="row">
                        <div class="col-sm-3">
                            <button name='submit' value='show' class="btn btn-primary">Zobraziť výsledky</button>
                        </div>
                        <div class="col-sm-3">
                            <button name='submit' value='delete' class="btn btn-primary" >Vymazať výsledky</button>
                        </div>
                        <div class="col-sm-3">
                            <button name='submit' value='print_to_pdf' class="btn btn-primary">Vytlačiť do PDF</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </main>
</div>
<br><br>
<?php echo $course_results;
?>

<footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
    <span class="text-white pd-top">Developed by : LR, DV, MM, SR, MR</span>
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
    <title>ADMIN</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php
if(!isset($_COOKIE['isAdmin']) and $_COOKIE['isAdmin'] !== 'admin')
{
    header("Location:login.php");
}
?>
<header>
    <nav class="navbar navbar-light navbar-custom navbar-expand-lg">
        <a class="navbar-brand" href="./index.php">
            <img src="admin.png" width="100" height="55" alt="admin">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../uloha1/admin-index.php">Task 1</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../uloha2/admin-index.php">Task 2</a>
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
            <span class="navbar-text text-right text-white">Username : admin &nbsp;</span>
            <a href="logout.php"><i class="material-icons nav-icon pt-2">exit_to_app</i></a>
        </div>
    </nav>
</header>

<div class="container-fluid root-container mt-3">
    <main>
        <div class="container mt-5 px-5">
            <div class="container-fluid">

                <form action="results.php" method="post" ><br>
                    <div class="row">
                        <h3>All students' results of the specified course</h3>
                        <div class="col-sm-6" style="margin: 3% auto;">
                            Choose year:
                            <select name="course_year" class="custom-select">
                                <?php echoOptions(fetchListFromDB('year')); ?>
                            </select>
                        </div>
                        <div class="col-sm-6" style="margin: 3% auto;">
                            Choose course title:
                            <select name="course_title" class="custom-select">
                                <?php echoOptions(fetchListFromDB('title')); ?>
                            </select>
                        </div>
                    </div>
                    <!--Button to submit the form-->
                    <div class="row">
                        <div class="col-sm-3">
                            <button name='submit' value='show' class="btn btn-primary">Show results</button>
                        </div>
                        <div class="col-sm-3">
                            <button name='submit' value='delete' class="btn btn-primary" >Delete results</button>
                        </div>
                        <div class="col-sm-3">
                            <button name='submit' value='print_to_pdf' class="btn btn-primary">Print to PDF</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </main>
</div>
<br><br>
<?php echo $course_results;
?>

<footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
    <span class="text-white pd-top">Developed by : LR, DV, MM, SR, MR</span>
</footer>

    <?php

}

?>
</body>
</html>