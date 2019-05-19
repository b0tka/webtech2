<?php
session_start();
if ($_SESSION['valid_user']) {



    if(isset($_GET['lang'])) {
        setcookie('lang', htmlspecialchars($_GET['lang']), strtotime("tomorrow"), '/');
        header("Location:loggedUser.php");
    }

    if(($_COOKIE['lang'] == 'en') or (!isset($_COOKIE['lang']))) {


        $userData = $_SESSION['user_data'];
        $idStudent = $userData['id'];
        try {
            include_once("../config.php");
            $conn = new PDO("mysql:host=$serverName;dbname=$dbName", $userName, $password);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            die();
        }
        $stmt = $conn->prepare("SET NAMES 'utf8';");
        $stmt->execute();

        $stmt = $conn->prepare("SELECT * FROM task2_students 
                                      INNER JOIN task2_students_subject on task2_students.id=task2_students_subject.id_student 
                                      INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id 
                                      WHERE task2_students.id=$idStudent
                          ");

        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        $i = 0;

        function getTeamResult($conn, $teamNumber, $year, $subject)
        {
            $stmt = $conn->prepare("SELECT *,task2_students_subject.id as id_bridge FROM task2_students 
                                      INNER JOIN task2_students_subject on task2_students.id=task2_students_subject.id_student 
                                      INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id 
                                      WHERE task2_subject.year='$year' 
                                      AND task2_subject.subject='$subject' 
                                      AND task2_students_subject.team='$teamNumber'
                          ");

            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetchAll();


            return $result;
        }


        function getTeamTotalPoints($conn, $teamNumber, $year, $subject)
        {
            $stmt = $conn->prepare("SELECT * FROM task2_team_points
                                      WHERE task2_team_points.year='$year' 
                                      AND task2_team_points.subject='$subject' 
                                      AND task2_team_points.team='$teamNumber'
                          ");
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetchAll();
            return $result;
        }


        ?>


        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Task 2</title>
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
            <link rel="stylesheet" href="style.css">
        </head>
        <body>
        <header>
            <nav class="navbar navbar-light navbar-custom navbar-expand-lg">
                <a class="navbar-brand" href="../index.php">
                    <i class="material-icons nav-icon pt-2">home</i>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="../uloha1/index.php">Task 1</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../uloha2/index.php">Task 2</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../uloha3/admin-index.php">Task 3</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink"
                               data-toggle="dropdown"
                               aria-haspopup="true" aria-expanded="false">
                                Language
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="?lang=sk">Slovak</a>
                                <a class="dropdown-item" href="?lang=en">English</a>
                            </div>
                        </li>
                    </ul>
                    <span class="navbar-text text-right text-white">
                        <?php echo $userData['name'] ?>
                </span>
                    <a href="logOut.php"><i class="material-icons nav-icon pt-2">exit_to_app</i></a>
                </div>
            </nav>
        </header>

        <div class="container-fluid root-container mt-3">
            <main>
                <div class="container mt-5 px-5">
                    <!--        core website-->
                    <h1 class="text-center">Click show to check your results</h1>
                    <?php
                    if (isset($_GET) && ($_GET['error'] === "bad-points")) {
                        ?>
                        <div class="alert alert-danger">
                            <strong>Bad points!</strong> You set more or less points than you have
                        </div>
                        <?php
                    } elseif (isset($_GET) && ($_GET['error'] === "bad-agreement")) {
                        ?>
                        <div class="alert alert-danger">
                            <strong>You MUST agree or disagree to your decision!</strong>
                        </div>
                        <?php
                    } elseif (isset($_GET) && ($_GET['error'] === "bad-number")) {
                        ?>
                        <div class="alert alert-danger">
                            <strong>You must set a valid number!</strong>
                        </div>
                        <?php

                    } elseif (isset($_GET) && ($_GET['success'] === "true")) {
                        ?>
                        <div class="alert alert-success">
                            <strong>Success!</strong>
                        </div>
                        <?php
                    }
                    ?>

                    <?php
                    foreach ($result as $row) {
                        echo "<h2>Team number: <b>" . $row['team'] . "</b>, Year: <b>" . $row['year'] . "</b>, Subject: <b>" . $row['subject'] . "</b></h2>";
                        ?>

                        <button type="button" class="show-team btn btn-primary" data-toggle="collapse"
                                data-target=<?php echo "#team" . $i; ?>>Show
                        </button>
                        <div id=<?php echo "team" . $i;
                        $i++; ?> class="collapse">
                            <?php
                            $finalTeamResult = getTeamTotalPoints($conn, $row['team'], $row['year'], $row['subject'])
                            ?>
                            <h3><?php echo "Group valuation: <b>" . $sumTeamPoints = $finalTeamResult[0]['points'] . "</b>"; ?></h3>
                            <p>
                                <?php
                                if ($finalTeamResult[0]['admin_agree'] == 1) {
                                    echo "Admin agree with your points.";
                                } elseif ($finalTeamResult[0]['admin_agree'] == -1) {
                                    echo "Admin disagree with your points.";
                                } else {
                                    echo "Admin has not made decesion yet.";
                                }

                                ?>
                            </p>
                            <form action="insertPointsFromStudent.php" method="post">

                                <table class="table table-striped">
                                    <tr>
                                        <th>Email</th>
                                        <th>Full name</th>
                                        <th>Points</th>
                                        <th>Agree</th>
                                    </tr>

                                    <?php $teamResult = getTeamResult($conn, $row['team'], $row['year'], $row['subject']);
                                    echo '<input type="hidden" name="sumTemPoints" value="' . $sumTeamPoints . '">';

                                    $studensId = array();
                                    $bridgeTable = array();
                                    $counter = 0;
                                    $agreement = 0;

                                    foreach ($teamResult as $secondRow) {
                                        array_push($bridgeTable, $secondRow['id_bridge']);
                                        array_push($studensId, $secondRow['id_student']);
                                        echo "<tr>";
                                        if ($_SESSION['user_id'] == $secondRow['id_student']) {
                                            echo "<td><b>" . $secondRow['mail'] . "<b></td>";
                                        } else {
                                            echo "<td>" . $secondRow['mail'] . "</td>";

                                        }
                                        if ($_SESSION['user_id'] == $secondRow['id_student']) {
                                            echo "<td><b>" . $secondRow['name'] . "<b></td>";
                                        } else {
                                            echo "<td>" . $secondRow['name'] . "</td>";

                                        }

                                        if ($secondRow['body'] > 0) {
                                            echo "<td>" . $secondRow['body'] . "</td>";
                                        } else {
                                            echo "<td><input name=" . $secondRow['id_bridge'] . " type=\"number\" class=\"form-control \" id=\"exampleInputPassword1\"></td>";
                                        }
                                        if ($_SESSION['user_id'] == $secondRow['id_student']) {
                                            $agreement = $secondRow['agree'];
                                        }

                                        if ($secondRow['agree'] == 0 && $_SESSION['user_id'] == $secondRow['id_student']) {

                                            ?>
                                            <td>
                                                <i class="agree-with-points material-icons">thumb_up</i>
                                                <i class="disagree-with-points material-icons">thumb_down</i>
                                            </td>
                                            <?php
                                        } elseif ($secondRow['agree'] == 0) {
                                            ?>
                                            <td>
                                                <i style="color: #4d5053" class="material-icons">thumb_up</i>
                                                <i style="color: #4d5053" class="material-icons">thumb_down</i>
                                            </td>

                                            <?php
                                        } elseif ($secondRow['agree'] == 1) {
                                            ?>
                                            <td>
                                                <i style="color: #ff2366" class="material-icons">thumb_up</i>
                                                <i style="color: #4d5053" class="material-icons">thumb_down</i>
                                                <input type="hidden" name="oneAgree" value="1">
                                            </td>
                                            <?php
                                        } elseif ($secondRow['agree'] == -1) {
                                            ?>
                                            <td>
                                                <input type="hidden" name="oneAgree" value="1">
                                                <i style="color: #4d5053" class="material-icons">thumb_up</i>
                                                <i style="color:#ff2366;" class="material-icons">thumb_down</i>
                                            </td>
                                            <?php
                                        }

                                        echo "</tr>";
                                    }
                                    foreach ($studensId as $value) {
                                        echo '<input type="hidden" name="studentsArray[]" value="' . $value . '">';
                                    }
                                    foreach ($bridgeTable as $value) {
                                        echo '<input type="hidden" name="bridgeArray[]" value="' . $value . '">';
                                    }
                                    ?>
                                </table>
                                <input class="final-agreement" type="hidden" name="agreement" value="">

                                <?php

                                if ($secondRow['body'] == 0 || $agreement == 0) { ?>
                                    <input type="submit" class="btn btn-success" value="Submit">
                                <?php } ?>
                            </form>
                        </div>
                        <?php
                    }
                    ?>
                    </form>
                </div>
            </main>

        </div>
        <script src="custom.js">

        </script>

        <footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
            <span class="text-white pd-top">Developed by : LR, DV, MM, SR, MR</span>
        </footer>
        </body>
        </html>

        <?php
    } elseif($_COOKIE['lang'] == 'sk') {
        $userData = $_SESSION['user_data'];
        $idStudent = $userData['id'];
        try {
            include_once("../config.php");
            $conn = new PDO("mysql:host=$serverName;dbname=$dbName", $userName, $password);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            die();
        }
        $stmt = $conn->prepare("SET NAMES 'utf8';");
        $stmt->execute();

        $stmt = $conn->prepare("SELECT * FROM task2_students 
                                      INNER JOIN task2_students_subject on task2_students.id=task2_students_subject.id_student 
                                      INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id 
                                      WHERE task2_students.id=$idStudent
                          ");

        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        $i = 0;

        function getTeamResult($conn, $teamNumber, $year, $subject)
        {
            $stmt = $conn->prepare("SELECT *,task2_students_subject.id as id_bridge FROM task2_students 
                                      INNER JOIN task2_students_subject on task2_students.id=task2_students_subject.id_student 
                                      INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id 
                                      WHERE task2_subject.year='$year' 
                                      AND task2_subject.subject='$subject' 
                                      AND task2_students_subject.team='$teamNumber'
                          ");

            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetchAll();


            return $result;
        }


        function getTeamTotalPoints($conn, $teamNumber, $year, $subject)
        {
            $stmt = $conn->prepare("SELECT * FROM task2_team_points
                                      WHERE task2_team_points.year='$year' 
                                      AND task2_team_points.subject='$subject' 
                                      AND task2_team_points.team='$teamNumber'
                          ");
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetchAll();
            return $result;
        }


        ?>


        <!DOCTYPE html>
        <html lang="sk">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Úloha 2</title>
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
            <link rel="stylesheet" href="style.css">
        </head>
        <body>
        <header>
            <nav class="navbar navbar-light navbar-custom navbar-expand-lg">
                <a class="navbar-brand" href="./index.php">
                    <i class="material-icons nav-icon pt-2">home</i>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="../uloha1/index.php">Úloha 1</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../uloha2/index.php">Úloha 2</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../general.admin/login.php">Úloha 3</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink"
                               data-toggle="dropdown"
                               aria-haspopup="true" aria-expanded="false">
                                Jazyk
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="?lang=sk">Slovenský</a>
                                <a class="dropdown-item" href="?lang=en">Anglický</a>
                            </div>
                        </li>
                    </ul>
                    <span class="navbar-text text-right text-white">
                        <?php echo $userData['name'] ?>
                </span>
                    <a href="logOut.php"><i class="material-icons nav-icon pt-2">exit_to_app</i></a>
                </div>
            </nav>
        </header>

        <div class="container-fluid root-container mt-3">
            <main>
                <div class="container mt-5 px-5">
                    <!--        core website-->
                    <h1 class="text-center">Klikni ukáž pre zobrazenie výsledkov</h1>
                    <?php
                    if (isset($_GET) && ($_GET['error'] === "bad-points")) {
                        ?>
                        <div class="alert alert-danger">
                            <strong>Zle zadané body!</strong> Zadal si viac alebo menej bodov
                        </div>
                        <?php
                    } elseif (isset($_GET) && ($_GET['error'] === "bad-agreement")) {
                        ?>
                        <div class="alert alert-danger">
                            <strong>Musíš súhlaiť alebo nesúhlasiť!</strong>
                        </div>
                        <?php
                    } elseif (isset($_GET) && ($_GET['error'] === "bad-number")) {
                        ?>
                        <div class="alert alert-danger">
                            <strong>Musíš vlažiť platné číslo!</strong>
                        </div>
                        <?php

                    } elseif (isset($_GET) && ($_GET['success'] === "true")) {
                        ?>
                        <div class="alert alert-success">
                            <strong>Success!</strong>
                        </div>
                        <?php
                    }
                    ?>

                    <?php
                    foreach ($result as $row) {
                        echo "<h2>Číslo tímu: <b>" . $row['team'] . "</b>, Rok: <b>" . $row['year'] . "</b>, Predmet: <b>" . $row['subject'] . "</b></h2>";
                        ?>

                        <button type="button" class="show-team btn btn-primary" data-toggle="collapse"
                                data-target=<?php echo "#team" . $i; ?>>Ukáž
                        </button>
                        <div id=<?php echo "team" . $i;
                        $i++; ?> class="collapse">
                            <?php
                            $finalTeamResult = getTeamTotalPoints($conn, $row['team'], $row['year'], $row['subject'])
                            ?>
                            <h3><?php echo "Ohodnotenie skupiny: <b>" . $sumTeamPoints = $finalTeamResult[0]['points'] . "</b>"; ?></h3>
                            <p>
                                <?php
                                if ($finalTeamResult[0]['admin_agree'] == 1) {
                                    echo "Admin súhlasí s vašimi bodmi.";
                                } elseif ($finalTeamResult[0]['admin_agree'] == -1) {
                                    echo "Admin nesúhlasí.";
                                } else {
                                    echo "Admin ešte nerozhodol so súhlasom.";
                                }

                                ?>
                            </p>
                            <form action="insertPointsFromStudent.php" method="post">

                                <table class="table table-striped">
                                    <tr>
                                        <th>Email</th>
                                        <th>Celé meno</th>
                                        <th>Body</th>
                                        <th>Súhlas</th>
                                    </tr>

                                    <?php $teamResult = getTeamResult($conn, $row['team'], $row['year'], $row['subject']);
                                    echo '<input type="hidden" name="sumTemPoints" value="' . $sumTeamPoints . '">';

                                    $studensId = array();
                                    $bridgeTable = array();
                                    $counter = 0;
                                    $agreement = 0;

                                    foreach ($teamResult as $secondRow) {
                                        array_push($bridgeTable, $secondRow['id_bridge']);
                                        array_push($studensId, $secondRow['id_student']);
                                        echo "<tr>";
                                        if ($_SESSION['user_id'] == $secondRow['id_student']) {
                                            echo "<td><b>" . $secondRow['mail'] . "<b></td>";
                                        } else {
                                            echo "<td>" . $secondRow['mail'] . "</td>";

                                        }
                                        if ($_SESSION['user_id'] == $secondRow['id_student']) {
                                            echo "<td><b>" . $secondRow['name'] . "<b></td>";
                                        } else {
                                            echo "<td>" . $secondRow['name'] . "</td>";

                                        }

                                        if ($secondRow['body'] > 0) {
                                            echo "<td>" . $secondRow['body'] . "</td>";
                                        } else {
                                            echo "<td><input name=" . $secondRow['id_bridge'] . " type=\"number\" class=\"form-control \" id=\"exampleInputPassword1\"></td>";
                                        }
                                        if ($_SESSION['user_id'] == $secondRow['id_student']) {
                                            $agreement = $secondRow['agree'];
                                        }

                                        if ($secondRow['agree'] == 0 && $_SESSION['user_id'] == $secondRow['id_student']) {

                                            ?>
                                            <td>
                                                <i class="agree-with-points material-icons">thumb_up</i>
                                                <i class="disagree-with-points material-icons">thumb_down</i>
                                            </td>
                                            <?php
                                        } elseif ($secondRow['agree'] == 0) {
                                            ?>
                                            <td>
                                                <i style="color: #4d5053" class="material-icons">thumb_up</i>
                                                <i style="color: #4d5053" class="material-icons">thumb_down</i>
                                            </td>

                                            <?php
                                        } elseif ($secondRow['agree'] == 1) {
                                            ?>
                                            <td>
                                                <i style="color: #ff2366" class="material-icons">thumb_up</i>
                                                <i style="color: #4d5053" class="material-icons">thumb_down</i>
                                                <input type="hidden" name="oneAgree" value="1">
                                            </td>
                                            <?php
                                        } elseif ($secondRow['agree'] == -1) {
                                            ?>
                                            <td>
                                                <input type="hidden" name="oneAgree" value="1">
                                                <i style="color: #4d5053" class="material-icons">thumb_up</i>
                                                <i style="color:#ff2366;" class="material-icons">thumb_down</i>
                                            </td>
                                            <?php
                                        }

                                        echo "</tr>";
                                    }
                                    foreach ($studensId as $value) {
                                        echo '<input type="hidden" name="studentsArray[]" value="' . $value . '">';
                                    }
                                    foreach ($bridgeTable as $value) {
                                        echo '<input type="hidden" name="bridgeArray[]" value="' . $value . '">';
                                    }
                                    ?>
                                </table>
                                <input class="final-agreement" type="hidden" name="agreement" value="">

                                <?php

                                if ($secondRow['body'] == 0 || $agreement == 0) { ?>
                                    <input type="submit" class="btn btn-success" value="Potvrdiť">
                                <?php } ?>
                            </form>
                        </div>
                        <?php
                    }
                    ?>
                    </form>
                </div>
            </main>

        </div>
        <script src="custom.js">

        </script>

        <footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
            <span class="text-white pd-top">Vyvynuté : LR, DV, MM, SR, MR</span>
        </footer>
        </body>
        </html>


        <?php
    }

} else {
    header("Location: http://147.175.121.210:8117/webte2/uloha2/index.php?error=invalid-user");
}

