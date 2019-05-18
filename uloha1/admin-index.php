<?php

if (!isset($_COOKIE['isAdmin']) and $_COOKIE['isAdmin'] !== 'admin') {
    header("Location:http://147.175.121.210:8117/webte2/general.admin/login.php");
}



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

    unlink($file);
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

//    echo $query_insert_course;

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

//            echo $query_insert_data_column;
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
//    echo $query_insert_student;
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

<?php
if(isset($_GET['lang'])) {
    setcookie('lang', htmlspecialchars($_GET['lang']), strtotime("tomorrow"), '/');
    header("Location:admin-index.php");
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
<div>
    <div class="col-sm-12 text-center">
        <a class="btn btn-primary" href="results.php">Prejsť na manipuláciu s výsledkami</a>
    </div>
    <main>
        <div class="container mt-3">
            <div class="container-fluid">
                <h3>Upload CSV súboru s výsledkami</h3>
                <form action="admin-index.php" method="post" enctype="multipart/form-data">

                <div class="row">
                    <div class="col-sm-6" style="margin: 3% auto;">
                        <select class="custom-select" name="course_year">
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
                        </select>
                    </div>

                    <div class="col-sm-6" style="margin: 3% auto;">
                        <input type="text" class="form-control" name="course_title" placeholder="Názov predmetu:">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6" style="margin: 3% auto;">
                        <input type="file" class="custom-file-input" id="customFile" name="csv_file">
                        <label class="custom-file-label" for="customFile">Vyber CSV súbor s výsledkami</label>
                    </div>
                    <div class="input-group col-sm-6" style="margin: 3% auto;">
                        <select class="custom-select" id="inputGroupSelect02" name="delimiter">
                            <option value="semicolon">;</option>
                            <option value="comma">,</option>
                        </select>
                        <div class="input-group-append">
                            <label class="input-group-text" for="inputGroupSelect02">Oddeľovač</label>
                        </div>
                    </div>
                </div>
                    <div class="col-sm-12 text-center">
                        <button type="submit" name="submit" class="btn btn-primary">Nahraj CSV a vytvor predmet</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

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
<?php
if(!isset($_COOKIE['isAdmin']) and $_COOKIE['isAdmin'] !== 'admin')
{
    header("Location:login.php");
}
?>
<body>
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
    <div>
        <div class="col-sm-12 text-center">
            <a class="btn btn-primary" href="results.php">Go to results page</a>
        </div>
        <main>
            <div class="container mt-3">
                <div class="container-fluid">
                    <h3>Upload CSV results file</h3>
                    <form action="admin-index.php" method="post" enctype="multipart/form-data">

                        <div class="row">
                            <div class="col-sm-6" style="margin: 3% auto;">
                                <select class="custom-select" name="course_year">
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
                                </select>
                            </div>

                            <div class="col-sm-6" style="margin: 3% auto;">
                                <input type="text" class="form-control" name="course_title" placeholder="Course title:">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6" style="margin: 3% auto;">
                                <input type="file" class="custom-file-input" id="customFile" name="csv_file">
                                <label class="custom-file-label" for="customFile">Choose CSV file:</label>
                            </div>
                            <div class="input-group col-sm-6" style="margin: 3% auto;">
                                <select class="custom-select" id="inputGroupSelect02" name="delimiter">
                                    <option value="semicolon">;</option>
                                    <option value="comma">,</option>
                                </select>
                                <div class="input-group-append">
                                    <label class="input-group-text" for="inputGroupSelect02">Delimiter</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 text-center">
                            <button type="submit" name="submit" class="btn btn-primary">Upload CSV and create course</button>
                        </div>
                    </form>
                </div>
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