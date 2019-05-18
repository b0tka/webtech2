<?php
/*-------START OF PHP CODE WRITTEN BY STEFAN--------*/
/*Definition of inputs and external files*/


include_once("../config.php");

/*Setting upp connection to projekt-uloha1 database*/
$conn = new mysqli($serverName, $userName, $password, $dbName);
if ($conn->connect_error) {
    die("Connection to MySQL server failed. " . $conn->connect_error);
}

/*Setting up UTF8 for database entries*/
$stmt = $conn->prepare("SET NAMES 'utf8';");
$stmt->execute();

/*Declaring error message variable*/
$errormsg = " ";

/*Funciton that generates random password with length of 15 characters,
this password can include small letters, capitals and integers from 0 to 9*/
function randomPassword()
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 15; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

/*This section is where the uploaded CSV will be parsed and the passwords with
length of 15 characters will be generated. CSV will be parsed into 2D array where
last element of each index will be the passwords.*/
if (!empty($_POST) && !empty($_FILES)) {

    $extension = pathinfo($_FILES["csv-file"]["name"], PATHINFO_EXTENSION);

    /*If file has no .csv extension, cannot be parsed*/
    if ($extension != "csv") {
        $errormsg = "Súbor nie je typu .CSV <br>";
    } /*Check whether the delimiter is set*/
    else if ($_POST["delimiter"] == "none") {
        $errormsg = "Oddeľovač nebol vybraný<br>";
    } /*If everything is OK then continue*/
    else {
        /*Initializing variables for the file that is being parsed and
        declaring target path to which file will be saved*/
        $csv = $_FILES['csv-file'];
        $csv_input = "files/" . $csv['name'];

        /*Checking size of file and saving to the target folder*/
        if ($csv['size'] < 2 * 1024 * 1024) {
            move_uploaded_file($_FILES['csv-file']["tmp_name"], $csv_input);
        }

        /*Initializing array that will handle the file*/
        $csv_array = array();
        $index = 0;

        /*Initializing output file to which the generated data will be written*/
        if(($_COOKIE['lang'] == 'sk') or (!isset($_COOKIE['lang']))) {
            $download_file_name = "hesla.csv";
        }else{
            $download_file_name = "passwords.csv";
        }

        $csv_output = fopen($download_file_name, 'w');

        if (($handle = fopen($csv_input, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, $_POST["delimiter"])) !== FALSE) {

                /*The first row is the coulmn header*/
                if ($index == 0) {
                    $data[0] = "ID";
                    $data[1] = "meno";
                    $data[2] = "Email";
                    $data[3] = "login";
                    $data[4] = "heslo";
                    $csv_array[$index] = $data;
                } /*From the second to the last row tha generated password
            will be inserted*/
                else {
                    $data[4] = randomPassword();
                    $csv_array[$index] = $data;
                }
                /*Writing generated line to the ouptut csv*/
                fputcsv($csv_output, $csv_array[$index], $_POST["delimiter"]);

                $index++;
            }
            fclose($handle);
            fclose($csv_output);

            /*Telling the browser that we want to download the file*/
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="'.$download_file_name.'"');
            readfile($download_file_name);
            unlink($download_file_name);
            exit;
        }
    }
}
if(isset($_GET['lang'])) {
    setcookie('lang', htmlspecialchars($_GET['lang']), strtotime("tomorrow"), '/');
    header("Location:admin-index.php");
}

if(($_COOKIE['lang'] == 'sk') or (!isset($_COOKIE['lang']))) {
/*-------END OF PHP CODE WRITTEN BY STEFAN--------*/
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Framework</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="ckeditor/ckeditor.js" charset="utf-8"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php
if (!isset($_COOKIE['isAdmin']) and $_COOKIE['isAdmin'] !== 'admin') {
    header("Location:http://147.175.121.210:8117/webte2/general.admin/login.php");
}
?>
<header>
    <nav class="navbar navbar-light navbar-custom navbar-expand-lg">
        <a class="navbar-brand" href="../general.admin/index.php">
            <img src="../general.admin/admin.png" width="100" height="55" alt="admin">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../general.admin/login.php">Úloha 1</a>
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
                        <a class="dropdown-item" href="admin-index.php?lang=sk">Slovenský</a>
                        <a class="dropdown-item" href="admin-index.php?lang=en">Anglický</a>
                    </div>
                </li>
            </ul>
            <span class="navbar-text text-right text-white">Používateľ: admin &nbsp;</span>
            <a href="../general.admin/logout.php"><i class="material-icons nav-icon pt-2">exit_to_app</i></a>
        </div>
    </nav>
</header>
<!--HTML "PROGRAMMED" BY STEFAN-->
<div class="container mt-3">
    <div class="container-fluid">
        <h3>Generovanie hesiel</h3>
        <form action="admin-index.php" enctype="multipart/form-data" method="post">
            <!--DIV container for the 'file selection' input-->
            <div class="row align-self-center">
                <div class="col-sm-6" style="margin: 3% auto;">
                    <input type="file" class="custom-file-input" id="customFile" name="csv-file" required>
                    <label class="custom-file-label" for="customFile">Vyber súbor</label>
                </div>
                <!--Selection for separator by which csv can be separated and parsed-->
                <div class="input-group col-sm-6" style="margin: 3% auto;">
                    <select class="custom-select" id="inputGroupSelect02" name="delimiter" required>
                        <option value="" selected>Vyber...</option>
                        <option value=";">;</option>
                        <option value=",">,</option>
                    </select>
                    <div class="input-group-append">
                        <label class="input-group-text" for="inputGroupSelect02">Oddeľovač</label>
                    </div>
                </div>
            </div>
            <!--Button to submit the form-->
            <div class="col-sm-12 text-center">
                <button type="submit" name="submit" class="btn btn-primary">Nahraj CSV a generuj heslá</button>
            </div>
        </form>
    </div>
    <!--Seperator line to make beautiful designs on the website-->
    <hr class="style1" style="margin-top: 3%">

    <!--Section for the second file upload and mail sending, when admin did the modifications to the csv and added
    new columns. Procedure:
        1) Admin selects modified csv file from his computer, that was created and downloaded in
        first section of this page
        2) Admin selects delimiter of csv
        3) Admin selects template for mail sending from database
        4) Admin fills in the required information (name, email, login, password, mail subject)
        5) Admin can modify email using basic editor
        6) Admin can send the file by pressing 'Poslať mail' button
    Mail will be sent using SMTP mail server stuba.sk-->
    <div class="container-fluid">
        <h3>Konfigurácia emailu</h3>
        <form action="PHPMail.php" enctype="multipart/form-data" method="post">
            <!--DIV container for the 'file selection' input and template selection-->
            <div class="row">
                <!--File browser for uploading from PC-->
                <div class="input-group col-sm-6" style="margin: 3% auto;">
                    <input type="file" class="custom-file-input" id="customFileMail" required name="mail-file">
                    <label class="custom-file-label" for="customFileMail">Vyber súbor</label>
                </div>
                <!--Selection for separator by which final csv can be separated and parsed-->
                <div class="input-group col-sm-6" style="margin: 3% auto;">
                    <select class="custom-select" required id="inputGroupSelect03" name="delimiter-mail">
                        <option value="none" selected>Vyber...</option>
                        <option value=";">;</option>
                        <option value=",">,</option>
                    </select>
                    <div class="input-group-append">
                        <label class="input-group-text btn-outline-danger" for="inputGroupSelect03">Oddeľovač</label>
                    </div>
                </div>
            </div>
            <div class="justify-content-center">
                <h4 class="mb-4 mt-3">Údaje o odosieľateľovi</h4>
                <!--Admin fills in his/her email-->
                <div class="form-group col-sm-12 row justify-content-center">
                    <label for="email" class="col-sm-2 col-form-label">Email<span
                                class="required-star"> *</span></label>
                    <div class="col-sm-10">
                        <input type="text" name="email" required class="form-control" id="email"
                               placeholder="email@example.com">
                    </div>
                </div>
                <!--Admin fills in his/her name-->
                <div class="form-group col-sm-12 row justify-content-center">
                    <label for="name" class="col-sm-2 col-form-label">Meno<span class="required-star"> *</span></label>
                    <div class="col-sm-10">
                        <input type="text" name="name" required class="form-control" id="name"
                               placeholder="Jožo Mrkvička">
                    </div>
                </div>
                <!--Admin types his/her password to be able send mail using SMTP stuba.sk-->
                <div class="form-group row col-sm-12 justify-content-center">
                    <label for="password" class="col-sm-2 col-form-label">Heslo<span
                                class="required-star"> *</span></label>
                    <div class="col-sm-10">
                        <input type="password" name="password" required class="form-control" id="password"
                               placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;">
                    </div>
                </div>
                <!--Admin fills in the subject of the mail-->
                <div class="form-group row col-sm-12 justify-content-center">
                    <label for="subjectMail" class="col-sm-2 col-form-label">Predmet<span
                                class="required-star"> *</span></label>
                    <div class="col-sm-10">
                        <input type="text" name="subjectMail" required class="form-control" id="subjectMail"
                               placeholder="Zadaj predmet mailu">
                    </div>
                </div>

                <div class="row col-sm-12 justify-content-center">
                    <!--Admin can add attachments-->
                    <div class="input-group col-sm-6" style="margin: 3% auto;">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="mailAttachmentDesc">Príloha</span>
                        </div>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="mailAttachment"
                                   aria-describedby="mailAttachmentDesc" name="mailAttachment">
                            <label class="custom-file-label" for="mailAttachment">Vyber súbor</label>
                        </div>
                    </div>
                    <!--Selection for mail template that is saved in database-->
                    <div class="input-group col-sm-6" style="margin: 3% auto;">
                        <select class="custom-select" required id="templateSelection" name="templateSelection">
                            <option value="none" selected>Vyber...</option>
                            <?php
                            /*This section provides dynamic generation of options for mail templates.
                            This script selects all the templates from database and inserts their name
                            to template selection as possible options.*/
                            $sqlTemplatesName = "SELECT * FROM template t";
                            $resultTemplateName = $conn->query($sqlTemplatesName);
                            if ($resultTemplateName->num_rows > 0) {
                                while ($row = $resultTemplateName->fetch_assoc()) {
                                    echo " <option value='" . $row["id"] . "'>" . $row["name"] . "</option>";
                                    $niec = $row["content"];
                                }
                            }
                            ?>
                        </select>
                        <div class="input-group-append">
                            <label class="input-group-text btn-outline-danger" for="templateSelection">Šablóna</label>
                        </div>
                    </div>
                </div>
                <!--TextArea with CKEditor what is used for editing the inserted template-->
                <div class="col-sm-12 row justify-content-center" style="margin-top: 3%">
                    <div class="col-sm-1"></div>
                    <div class="form-group col-sm-10">
                        <textarea class="form-control" id="mailBodyTextArea" name="mailBodyTextArea" rows="60"></textarea>
                        <script>
                            /*Replace the <textarea id="editor1"> with a CKEditor
                            instance, using default configuration.*/
                            CKEDITOR.replace('mailBodyTextArea');
                        </script>
                    </div>
                    <div class="col-sm-1"></div>
                </div>
                <!--Button to submit the form and send mail-->
                <div class="col-sm-12 text-center" style="margin-top: 2%">
                    <button type="submit" name="submitMail" class="btn btn-success">Poslať maily</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
echo "<div class='err-msg-cont'>" . $errormsg . "</div>";
?>
<!--END OF STEFAN'S HTML "PROGRAM CODE"-->
<footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
    <span class="text-white pd-top">Vývojári : LR, DV, MM, SR, MR</span>
</footer>
<script src="getTemplateAjax.js"></script>
</body>
</html>
    <?php
} elseif($_COOKIE['lang'] == 'en') {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Framework</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="ckeditor/ckeditor.js" charset="utf-8"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php
if (!isset($_COOKIE['isAdmin']) and $_COOKIE['isAdmin'] !== 'admin') {
    header("Location:http://147.175.121.210:8117/webte2/general.admin/login.php");
}
?>
<header>
    <nav class="navbar navbar-light navbar-custom navbar-expand-lg">
        <a class="navbar-brand" href="../general.admin/index.php">
            <img src="../general.admin/admin.png" width="100" height="55" alt="admin">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../general.admin/login.php">Task 1</a>
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
                        <a class="dropdown-item" href="admin-index.php?lang=sk">Slovak</a>
                        <a class="dropdown-item" href="admin-index.php?lang=en">English</a>
                    </div>
                </li>
            </ul>
            <span class="navbar-text text-right text-white">Username : admin &nbsp;</span>
            <a href="../general.admin/logout.php"><i class="material-icons nav-icon pt-2">exit_to_app</i></a>
        </div>
    </nav>
</header>
<!--HTML "PROGRAMMED" BY STEFAN-->
<div class="container mt-3">
    <div class="container-fluid">
        <h3>Password generator</h3>
        <form action="admin-index.php" enctype="multipart/form-data" method="post">
            <!--DIV container for the 'file selection' input-->
            <div class="row align-self-center">
                <div class="col-sm-6" style="margin: 3% auto;">
                    <input type="file" class="custom-file-input" id="customFile" name="csv-file" required>
                    <label class="custom-file-label" for="customFile">Select file</label>
                </div>
                <!--Selection for separator by which csv can be separated and parsed-->
                <div class="input-group col-sm-6" style="margin: 3% auto;">
                    <select class="custom-select" id="inputGroupSelect02" name="delimiter" required>
                        <option value="" selected>Select...</option>
                        <option value=";">;</option>
                        <option value=",">,</option>
                    </select>
                    <div class="input-group-append">
                        <label class="input-group-text" for="inputGroupSelect02">Delimiter</label>
                    </div>
                </div>
            </div>
            <!--Button to submit the form-->
            <div class="col-sm-12 text-center">
                <button type="submit" name="submit" class="btn btn-primary">Upload CSV and generate passwords</button>
            </div>
        </form>
    </div>
    <!--Seperator line to make beautiful designs on the website-->
    <hr class="style1" style="margin-top: 3%">

    <!--Section for the second file upload and mail sending, when admin did the modifications to the csv and added
    new columns. Procedure:
        1) Admin selects modified csv file from his computer, that was created and downloaded in
        first section of this page
        2) Admin selects delimiter of csv
        3) Admin selects template for mail sending from database
        4) Admin fills in the required information (name, email, login, password, mail subject)
        5) Admin can modify email using basic editor
        6) Admin can send the file by pressing 'Poslať mail' button
    Mail will be sent using SMTP mail server stuba.sk-->
    <div class="container-fluid">
        <h3>Mail configuration</h3>
        <form action="PHPMail.php" enctype="multipart/form-data" method="post">
            <!--DIV container for the 'file selection' input and template selection-->
            <div class="row">
                <!--File browser for uploading from PC-->
                <div class="input-group col-sm-6" style="margin: 3% auto;">
                    <input type="file" class="custom-file-input" id="customFileMail" required name="mail-file">
                    <label class="custom-file-label" for="customFileMail">Select file</label>
                </div>
                <!--Selection for separator by which final csv can be separated and parsed-->
                <div class="input-group col-sm-6" style="margin: 3% auto;">
                    <select class="custom-select" required id="inputGroupSelect03" name="delimiter-mail">
                        <option value="none" selected>Select...</option>
                        <option value=";">;</option>
                        <option value=",">,</option>
                    </select>
                    <div class="input-group-append">
                        <label class="input-group-text btn-outline-danger" for="inputGroupSelect03">Delimiter</label>
                    </div>
                </div>
            </div>
            <div class="justify-content-center">
                <h4 class="mb-4 mt-3">Sender information</h4>
                <!--Admin fills in his/her email-->
                <div class="form-group col-sm-12 row justify-content-center">
                    <label for="email" class="col-sm-2 col-form-label">Email<span
                                class="required-star"> *</span></label>
                    <div class="col-sm-10">
                        <input type="text" name="email" required class="form-control" id="email"
                               placeholder="email@example.com">
                    </div>
                </div>
                <!--Admin fills in his/her name-->
                <div class="form-group col-sm-12 row justify-content-center">
                    <label for="name" class="col-sm-2 col-form-label">Name<span class="required-star"> *</span></label>
                    <div class="col-sm-10">
                        <input type="text" name="name" required class="form-control" id="name"
                               placeholder="Joseph Carrot">
                    </div>
                </div>
                <!--Admin types his/her password to be able send mail using SMTP stuba.sk-->
                <div class="form-group row col-sm-12 justify-content-center">
                    <label for="password" class="col-sm-2 col-form-label">Password<span
                                class="required-star"> *</span></label>
                    <div class="col-sm-10">
                        <input type="password" name="password" required class="form-control" id="password"
                               placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;">
                    </div>
                </div>
                <!--Admin fills in the subject of the mail-->
                <div class="form-group row col-sm-12 justify-content-center">
                    <label for="subjectMail" class="col-sm-2 col-form-label">Subject<span
                                class="required-star"> *</span></label>
                    <div class="col-sm-10">
                        <input type="text" name="subjectMail" required class="form-control" id="subjectMail"
                               placeholder="Add subject">
                    </div>
                </div>

                <div class="row col-sm-12 justify-content-center">
                    <!--Admin can add attachments-->
                    <div class="input-group col-sm-6" style="margin: 3% auto;">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="mailAttachmentDesc">Attachment</span>
                        </div>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="mailAttachment"
                                   aria-describedby="mailAttachmentDesc" name="mailAttachment">
                            <label class="custom-file-label" for="mailAttachment">Select file</label>
                        </div>
                    </div>
                    <!--Selection for mail template that is saved in database-->
                    <div class="input-group col-sm-6" style="margin: 3% auto;">
                        <select class="custom-select" required id="templateSelection" name="templateSelection">
                            <option value="none" selected>Select...</option>
                            <?php
                            /*This section provides dynamic generation of options for mail templates.
                            This script selects all the templates from database and inserts their name
                            to template selection as possible options.*/
                            $sqlTemplatesName = "SELECT * FROM template t";
                            $resultTemplateName = $conn->query($sqlTemplatesName);
                            if ($resultTemplateName->num_rows > 0) {
                                while ($row = $resultTemplateName->fetch_assoc()) {
                                    echo " <option value='" . $row["id"] . "'>" . $row["name"] . "</option>";
                                    $niec = $row["content"];
                                }
                            }
                            ?>
                        </select>
                        <div class="input-group-append">
                            <label class="input-group-text btn-outline-danger" for="templateSelection">Templates</label>
                        </div>
                    </div>
                </div>
                <!--TextArea with CKEditor what is used for editing the inserted template-->
                <div class="col-sm-12 row justify-content-center" style="margin-top: 3%">
                    <div class="col-sm-1"></div>
                    <div class="form-group col-sm-10">
                        <textarea class="form-control" id="mailBodyTextArea" name="mailBodyTextArea" rows="60"></textarea>
                        <script>
                            /*Replace the <textarea id="editor1"> with a CKEditor
                            instance, using default configuration.*/
                            CKEDITOR.replace('mailBodyTextArea');
                        </script>
                    </div>
                    <div class="col-sm-1"></div>
                </div>
                <!--Button to submit the form and send mail-->
                <div class="col-sm-12 text-center" style="margin-top: 2%">
                    <button type="submit" name="submitMail" class="btn btn-success">Send mails</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
echo "<div class='err-msg-cont'>" . $errormsg . "</div>";
?>
<!--END OF STEFAN'S HTML "PROGRAM CODE"-->
<footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
    <span class="text-white pd-top">Developed by : LR, DV, MM, SR, MR</span>
</footer>
<script src="getTemplateAjax.js"></script>
</body>
</html>
    <?php

}

?>