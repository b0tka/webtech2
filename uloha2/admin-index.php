<?php
if (!isset($_COOKIE['isAdmin']) and $_COOKIE['isAdmin'] !== 'admin') {
    header("Location:login.php");
}
if(isset($_GET['lang'])) {
    setcookie('lang', htmlspecialchars($_GET['lang']), strtotime("tomorrow"), '/');
    header("Location:admin-index.php");
}
if (($_COOKIE['lang'] == 'sk') or (!isset($_COOKIE['lang']))) {

    ?>
    <!DOCTYPE html>
    <html lang="sk">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Admin Úloha 2</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
              integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
              crossorigin="anonymous">
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

    include_once("../config.php");

    $conn = new mysqli($serverName, $userName, $password, $dbName);
    if ($conn->connect_error) {
        die("Connection to MySQL server failed. " . $conn->connect_error);
    }
    $stmt = $conn->prepare("SET NAMES 'utf8';");
    $stmt->execute();

    if (!empty($_POST) && !empty($_FILES)) {


        $extension = pathinfo($_FILES["csv-file"]["name"], PATHINFO_EXTENSION);
        $errormsg = "";

        if ($extension != "csv") {
            $errormsg = "Súbor nie je typu .CSV <br>";
        } else if ($_POST["delimiter"] == "none") {
            $errormsg = "Oddeľovač nebol vybraný<br>";
        } else {

            $csv = $_FILES['csv-file'];
            $csv_input = "files/" . $csv['name'];

            if ($csv['size'] < 2 * 1024 * 1024) {
                move_uploaded_file($_FILES['csv-file']["tmp_name"], $csv_input);
            } else {
                $errormsg = "Príliš veľký súbor<br>";
            }


            $selected_year = $_POST["selected_year"];
            $selected_subject = $_POST["subject"];
            $selected_subject = strtolower($selected_subject);
            session_start();
            $_SESSION['Subject'] = $selected_subject;
            $_SESSION['Year'] = $_POST["selected_year"];

            $flag = true;
            $setHeslo = true;
            $badFile = false;
            if (($handle = fopen($csv_input, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, $_POST["delimiter"])) !== FALSE) {
                    if ($flag) {
                        $num = count($data);
                        if ($num == 4) {
                            $setHeslo = false;
                        } else if ($num == 5) {
                            $setHeslo = true;
                        } else {
                            $badFile = true;
                        }
                        $flag = false;
                        continue;
                    }

                    if ($badFile) {
                        $errormsg = "Zle stĺpce súboru";
                        break;
                    }
                    if ($setHeslo) {
                        //                    $sqlQuerry = "INSERT IGNORE INTO task2_students (id,school_year,subject,name,mail,password,team) VALUES ('$data[0]','$selected_year','$selected_subject','$data[1]','$data[2]','$hashHeslo','$data[4]')";
                        $hashHeslo = sha1($data[3]);
                        $sqlStudent = "INSERT IGNORE INTO task2_students (id,name,mail,password) VALUES ('$data[0]','$data[1]','$data[2]','$hashHeslo')";
                        $sqlSubject = "INSERT IGNORE INTO task2_subject (year,subject) VALUES ('$selected_year','$selected_subject')";
                    } else {
                        $sqlStudent = "INSERT IGNORE INTO task2_students (id,name,mail,password) VALUES ('$data[0]','$data[1]','$data[2]','" . nezadane . "')";
                        $sqlSubject = "INSERT IGNORE INTO task2_subject (year,subject) VALUES ('$selected_year','$selected_subject')";
                    }

                    if ($conn->query($sqlSubject) === TRUE) {
                    } else {
                        echo "Error" . $conn->error;
                    }

                    if ($conn->query($sqlStudent) === TRUE) {
                    } else {
                        echo "Error" . $conn->error;
                    }


                    $sqlSelectSubjectID = "SELECT task2_subject.id From task2_subject where task2_subject.year='$selected_year' AND task2_subject.subject='$selected_subject'";
                    $result = $conn->query($sqlSelectSubjectID);


                    if ($result->num_rows == 1) {
                        $row = $result->fetch_assoc();
                        $idOfSubject = $row["id"];

                        $sqlSelcetStrudentSubject = "SELECT task2_students_subject.id From task2_students_subject where task2_students_subject.id_student='$data[0]' AND task2_students_subject.id_subject='$idOfSubject'";
                        $resultOfDuplicity = $conn->query($sqlSelcetStrudentSubject);
                        if ($resultOfDuplicity->num_rows == 0) {
                            if ($setHeslo) {
                                $sqlInsertStudentSubject = "INSERT INTO  task2_students_subject (id_student,id_subject,body,team,agree) VALUES ('$data[0]','$idOfSubject',0,'$data[4]',0)";
                            } else {
                                $sqlInsertStudentSubject = "INSERT INTO  task2_students_subject (id_student,id_subject,body,team,agree) VALUES ('$data[0]','$idOfSubject',0,'$data[3]',0)";
                            }
                            if ($conn->query($sqlInsertStudentSubject) === TRUE) {
                            } else {
                                echo "Error" . $conn->error;
                            }
                        }
                    }

                }
                fclose($handle);
                $SelectAllFromTables = "SELECT task2_subject.subject,task2_subject.year,task2_students_subject.team FROM task2_students INNER JOIN task2_students_subject on task2_students.id=task2_students_subject.id_student INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id GROUP BY task2_subject.subject,task2_subject.year,task2_students_subject.team";
                $SelectAllFromTablesSelected = $conn->query($SelectAllFromTables);
                if ($SelectAllFromTablesSelected->num_rows > 0) {
                    while ($row = $SelectAllFromTablesSelected->fetch_assoc()) {
                        $subjectSelected = $row["subject"];
                        $yearSelected = $row["year"];
                        $teamSelected = $row["team"];
                        $sqlInsertAllPoints = "INSERT IGNORE INTO task2_team_points (	year,points,subject,team) VALUES ('$yearSelected',0,'$subjectSelected','$teamSelected')";
                        if ($conn->query($sqlInsertAllPoints) === TRUE) {

                        } else {
                            echo "Error" . $conn->error;
                        }

                    }
                }
                header("Location: http://147.175.121.210:8117/webte2/uloha2/teamsPoints.php");

            }
        }
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

    <div class="container mt-3">
        <div class="container-fluid">
            <form action="admin-index.php" enctype="multipart/form-data" method="post">
                <div class="form-group">
                    <label for="exampleFormControlInput1">Názov predmetu</label>
                    <input type="text" class="form-control" name="subject" placeholder="Webtech2" required>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlSelect1">Ročník</label>
                    <select class="form-control" name="selected_year" id="exampleFormControlSelect1" required>
                        <option>2016/2017</option>
                        <option>2017/2018</option>
                        <option>2018/2019</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="inputGroupSelect02">Oddeľovač</label>
                    <select class="form-control" id="inputGroupSelect02" name="delimiter" required>
                        <option value=";">;</option>
                        <option value=",">,</option>
                    </select>
                </div>
                <div style="margin-bottom: 0.5%"><span>.csv file</span></div>
                <div class="custom-file">

                    <input type="file" class="custom-file-input" id="csv-file" required name="csv-file">
                    <label class="custom-file-label" for="csv-file">Zvoľ...</label>
                </div>

                <div class="form-group" style="margin-top: 1%">

                    <button type="submit" name="Odosli" class="btn btn-primary">Pridať</button>

                </div>
            </form>
        </div>
    </div>
    <?php
    echo "<div class='err-msg-cont'>" . $errormsg . "</div>";
    ?>
    <footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
        <span class="text-white pd-top">Vývojári : LR, DV, MM, SR, MR</span>
    </footer>
    </body>
    </html>
    <?php
} elseif ($_COOKIE['lang'] == 'en') {


    /*
     *
     * ANGLICKY
     *
     * */
    ?>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Admin Task 2</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
              integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
              crossorigin="anonymous">
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

    include_once("../config.php");

    $conn = new mysqli($serverName, $userName, $password, $dbName);
    if ($conn->connect_error) {
        die("Connection to MySQL server failed. " . $conn->connect_error);
    }
    $stmt = $conn->prepare("SET NAMES 'utf8';");
    $stmt->execute();

    if (!empty($_POST) && !empty($_FILES)) {


        $extension = pathinfo($_FILES["csv-file"]["name"], PATHINFO_EXTENSION);
        $errormsg = "";

        if ($extension != "csv") {
            $errormsg = " Not .CSV File <br>";
        } else if ($_POST["delimiter"] == "none") {
            $errormsg = "Not specified sepparator<br>";
        } else {

            $csv = $_FILES['csv-file'];
            $csv_input = "files/" . $csv['name'];

            if ($csv['size'] < 2 * 1024 * 1024) {
                move_uploaded_file($_FILES['csv-file']["tmp_name"], $csv_input);
            } else {
                $errormsg = "Big file<br>";
            }


            $selected_year = $_POST["selected_year"];
            $selected_subject = $_POST["subject"];
            $selected_subject = strtolower($selected_subject);
            session_start();
            $_SESSION['Subject'] = $selected_subject;
            $_SESSION['Year'] = $_POST["selected_year"];

            $flag = true;
            $setHeslo = true;
            $badFile = false;
            if (($handle = fopen($csv_input, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, $_POST["delimiter"])) !== FALSE) {
                    if ($flag) {
                        $num = count($data);
                        if ($num == 4) {
                            $setHeslo = false;
                        } else if ($num == 5) {
                            $setHeslo = true;
                        } else {
                            $badFile = true;
                        }
                        $flag = false;
                        continue;
                    }

                    if ($badFile) {
                        $errormsg = "Zle stĺpce súboru";
                        break;
                    }
                    if ($setHeslo) {
                        //                    $sqlQuerry = "INSERT IGNORE INTO task2_students (id,school_year,subject,name,mail,password,team) VALUES ('$data[0]','$selected_year','$selected_subject','$data[1]','$data[2]','$hashHeslo','$data[4]')";
                        $hashHeslo = sha1($data[3]);
                        $sqlStudent = "INSERT IGNORE INTO task2_students (id,name,mail,password) VALUES ('$data[0]','$data[1]','$data[2]','$hashHeslo')";
                        $sqlSubject = "INSERT IGNORE INTO task2_subject (year,subject) VALUES ('$selected_year','$selected_subject')";
                    } else {
                        $sqlStudent = "INSERT IGNORE INTO task2_students (id,name,mail,password) VALUES ('$data[0]','$data[1]','$data[2]','" . nezadane . "')";
                        $sqlSubject = "INSERT IGNORE INTO task2_subject (year,subject) VALUES ('$selected_year','$selected_subject')";
                    }

                    if ($conn->query($sqlSubject) === TRUE) {
                    } else {
                        echo "Error" . $conn->error;
                    }

                    if ($conn->query($sqlStudent) === TRUE) {
                    } else {
                        echo "Error" . $conn->error;
                    }


                    $sqlSelectSubjectID = "SELECT task2_subject.id From task2_subject where task2_subject.year='$selected_year' AND task2_subject.subject='$selected_subject'";
                    $result = $conn->query($sqlSelectSubjectID);


                    if ($result->num_rows == 1) {
                        $row = $result->fetch_assoc();
                        $idOfSubject = $row["id"];

                        $sqlSelcetStrudentSubject = "SELECT task2_students_subject.id From task2_students_subject where task2_students_subject.id_student='$data[0]' AND task2_students_subject.id_subject='$idOfSubject'";
                        $resultOfDuplicity = $conn->query($sqlSelcetStrudentSubject);
                        if ($resultOfDuplicity->num_rows == 0) {
                            if ($setHeslo) {
                                $sqlInsertStudentSubject = "INSERT INTO  task2_students_subject (id_student,id_subject,body,team,agree) VALUES ('$data[0]','$idOfSubject',0,'$data[4]',0)";
                            } else {
                                $sqlInsertStudentSubject = "INSERT INTO  task2_students_subject (id_student,id_subject,body,team,agree) VALUES ('$data[0]','$idOfSubject',0,'$data[3]',0)";
                            }
                            if ($conn->query($sqlInsertStudentSubject) === TRUE) {
                            } else {
                                echo "Error" . $conn->error;
                            }
                        }
                    }

                }
                fclose($handle);
                $SelectAllFromTables = "SELECT task2_subject.subject,task2_subject.year,task2_students_subject.team FROM task2_students INNER JOIN task2_students_subject on task2_students.id=task2_students_subject.id_student INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id GROUP BY task2_subject.subject,task2_subject.year,task2_students_subject.team";
                $SelectAllFromTablesSelected = $conn->query($SelectAllFromTables);
                if ($SelectAllFromTablesSelected->num_rows > 0) {
                    while ($row = $SelectAllFromTablesSelected->fetch_assoc()) {
                        $subjectSelected = $row["subject"];
                        $yearSelected = $row["year"];
                        $teamSelected = $row["team"];
                        $sqlInsertAllPoints = "INSERT IGNORE INTO task2_team_points (	year,points,subject,team) VALUES ('$yearSelected',0,'$subjectSelected','$teamSelected')";
                        if ($conn->query($sqlInsertAllPoints) === TRUE) {

                        } else {
                            echo "Error" . $conn->error;
                        }

                    }
                }
                header("Location: http://147.175.121.210:8117/webte2/uloha2/teamsPoints.php");

            }
        }
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
                            <a class="dropdown-item" href="admin-index.php?lang=sk">Slovak</a>
                            <a class="dropdown-item" href="admin-index.php?lang=en">English</a>
                        </div>
                    </li>
                </ul>
                <span class="navbar-text text-right text-white">Ussername: admin &nbsp;</span>
                <a href="../general.admin/logout.php"><i class="material-icons nav-icon pt-2">exit_to_app</i></a>
            </div>
        </nav>
    </header>

    <div class="container mt-3">
        <div class="container-fluid">
            <form action="admin-index.php" enctype="multipart/form-data" method="post">
                <div class="form-group">
                    <label for="exampleFormControlInput1">Name of subject</label>
                    <input type="text" class="form-control" name="subject" placeholder="Webtech2" required>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlSelect1">Year</label>
                    <select class="form-control" name="selected_year" id="exampleFormControlSelect1" required>
                        <option>2016/2017</option>
                        <option>2017/2018</option>
                        <option>2018/2019</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="inputGroupSelect02">Sepparator</label>
                    <select class="form-control" id="inputGroupSelect02" name="delimiter" required>
                        <option value=";">;</option>
                        <option value=",">,</option>
                    </select>
                </div>
                <div style="margin-bottom: 0.5%"><span>.csv file</span></div>
                <div class="custom-file">

                    <input type="file" class="custom-file-input" id="csv-file" required name="csv-file">
                    <label class="custom-file-label" for="csv-file">Choose...</label>
                </div>

                <div class="form-group" style="margin-top: 1%">

                    <button type="submit" name="Odosli" class="btn btn-primary">Add</button>

                </div>
            </form>
        </div>
    </div>
    <?php
    echo "<div class='err-msg-cont'>" . $errormsg . "</div>";
    ?>
    <footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
        <span class="text-white pd-top">Developed by : LR, DV, MM, SR, MR</span>
    </footer>
    </body>
    </html>
    <?php

}

?>