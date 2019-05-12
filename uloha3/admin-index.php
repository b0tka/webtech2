<?php
/*-------START OF PHP CODE WRITTEN BY STEFAN--------*/
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

    /*If file has no .csv extension, cannot be parsed*/
    $extension = pathinfo($_FILES["csv-file"]["name"], PATHINFO_EXTENSION);
    if ($extension != "csv") {
        echo "Súbor nie je typu .CSV <br>";
        return;
    }

    /*Check whether the delimiter is set*/
    if ($_POST["delimiter"] == "none") {
        echo "Oddeľovač nebol vybraný<br>";
        return;
    }

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
    $csv_output = fopen('passwords.csv', 'w');

    if (($handle = fopen($csv_input, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, $_POST["delimiter"])) !== FALSE) {

            /*The first row is the coulmn header*/
            if ($index == 0) {
                $data[0] = "id";
                $data[1] = "meno";
                $data[2] = "email";
                $data[3] = "login";
                $data[4] = "password";
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

        /*Telling the browser that we want to download the file*/
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="passwords.csv"');
        readfile("passwords.csv");
        exit;
    }
}
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
                    <a class="nav-link" href="./history.php">Úloha 2</a>
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
                        <a class="dropdown-item" href="#">Slovenský</a>
                        <a class="dropdown-item" href="#">Anglický</a>
                    </div>
                </li>
            </ul>
            <span class="navbar-text text-right text-white">Username :</span>
            <a href="../general.admin/logout.php"><i class="material-icons nav-icon pt-2">exit_to_app</i></a>
        </div>
    </nav>
</header>
<!--File upload section MODIFIED BY STEFAN-->
<div class="container mt-3">
    <div class="container-fluid">
        <form action="admin-index.php" enctype="multipart/form-data" method="post">
            <!--DIV container for the 'file selection' input-->
            <div class="col-sm-12" style="width: 400px; margin: 3% auto;">
                <input type="file" class="custom-file-input" id="customFile" name="csv-file">
                <label class="custom-file-label" for="customFile">Choose file</label>
            </div>
            <!--Selection for separator by which csv can be separated and parsed-->
            <div class="input-group col-sm-12" style="width: 430px; margin: 3% auto;">
                <select class="custom-select" id="inputGroupSelect02" name="delimiter">
                    <option value="none" selected>Vyber...</option>
                    <option value=";">;</option>
                    <option value=",">,</option>
                </select>
                <div class="input-group-append">
                    <label class="input-group-text" for="inputGroupSelect02">Oddeľovač</label>
                </div>
            </div>
            <!--Button to submit the form-->
            <div class="col-sm-12 text-center" style="margin-top: 3%">
                <button type="submit" name="submit" class="btn btn-primary">Nahraj CSV a generuj heslá</button>
            </div>
        </form>
    </div>
</div>
<!--END of File upload section MODIFIED BY STEFAN-->
<footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
    <span class="text-white pd-top">Developed by : LR, DV, MM, SR, MR</span>
</footer>
</body>
</html>