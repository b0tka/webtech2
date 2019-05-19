<?php
session_start();
if (!isset($_COOKIE['isAdmin']) and $_COOKIE['isAdmin'] !== 'admin') {
    header("Location:http://147.175.121.210:8117/webte2/general.admin/login.php");
}
if (isset($_GET['lang'])) {
    setcookie('lang', htmlspecialchars($_GET['lang']), strtotime("tomorrow"), '/');
    header("Location: statistics.php");
}

if (($_COOKIE['lang'] == 'en') or (!isset($_COOKIE['lang']))) {


    include_once("../config.php");
    $conn = new mysqli($serverName, $userName, $password, $dbName);
    if ($conn->connect_error) {
        die("Connection to MySQL server failed. " . $conn->connect_error);
    }
    $stmt = $conn->prepare("SET NAMES 'utf8';");
    $stmt->execute();


    if (isset($_SESSION['Subject']) && !empty($_SESSION['Subject']) && (isset($_SESSION['Year']) && !empty($_SESSION['Year']))) {


        $data = array();

        $querry = "SELECT COUNT(task2_students_subject.id_student) as pocet FROM task2_students_subject INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id WHERE task2_subject.year='" . $_SESSION['Year'] . "' AND task2_subject.subject='" . $_SESSION['Subject'] . "' GROUP BY task2_subject.subject";
        $result = $conn->query($querry);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data[0] = $row['pocet'];
        } else {
            $data[0] = "chyba";
        }

        $querry = "SELECT COUNT(task2_students_subject.agree) as pocet FROM task2_students_subject INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id WHERE task2_subject.year='" . $_SESSION['Year'] . "' AND task2_subject.subject='" . $_SESSION['Subject'] . "' AND task2_students_subject.agree='1'";
        $result = $conn->query($querry);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data[1] = $row['pocet'];
        } else {
            $data[1] = "chyba";
        }

        $querry = "SELECT COUNT(task2_students_subject.agree) as pocet FROM task2_students_subject INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id WHERE task2_subject.year='" . $_SESSION['Year'] . "' AND task2_subject.subject='" . $_SESSION['Subject'] . "' AND task2_students_subject.agree='-1'";
        $result = $conn->query($querry);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data[2] = $row['pocet'];
        } else {
            $data[2] = "chyba";
        }

        $querry = "SELECT COUNT(task2_students_subject.agree) as pocet FROM task2_students_subject INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id WHERE task2_subject.year='" . $_SESSION['Year'] . "' AND task2_subject.subject='" . $_SESSION['Subject'] . "' AND task2_students_subject.agree='0'";
        $result = $conn->query($querry);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data[3] = $row['pocet'];
        } else {
            $data[3] = "chyba";
        }

        $querry = "SELECT COUNT(task2_team_points.team) as pocet FROM task2_team_points  WHERE task2_team_points.year='" . $_SESSION['Year'] . "' AND task2_team_points.subject='" . $_SESSION['Subject'] . "' GROUP BY task2_team_points.team";
        $result = $conn->query($querry);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data[4] = $row['pocet'];
        } else {
            $data[4] = "chyba";
        }

        $querry = "SELECT COUNT(task2_team_points.team) as pocet FROM task2_team_points WHERE task2_team_points.year='" . $_SESSION['Year'] . "' AND task2_team_points.subject='" . $_SESSION['Subject'] . "'AND task2_team_points.admin_agree='1'";
        $result = $conn->query($querry);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data[5] = $row['pocet'];
        } else {
            $data[5] = "chyba";
        }

        $querry = "SELECT COUNT(task2_team_points.team) as pocet FROM task2_team_points WHERE task2_team_points.year='" . $_SESSION['Year'] . "' AND task2_team_points.subject='" . $_SESSION['Subject'] . "'AND (task2_team_points.admin_agree='0' OR  task2_team_points.admin_agree IS NULL)";
        $result = $conn->query($querry);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data[6] = $row['pocet'];
        } else {
            $data[6] = "chyba";
        }

        $querry = "SELECT COUNT(*) as pocet FROM (SELECT count(*) FROM task2_students INNER JOIN task2_students_subject on task2_students.id=task2_students_subject.id_student INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id WHERE task2_students_subject.agree=0 AND task2_subject.subject='" . $_SESSION['Subject'] . "' AND task2_subject.year='" . $_SESSION['Year'] . "' GROUP BY task2_students_subject.id_subject) src";
        $result = $conn->query($querry);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data[7] = $row['pocet'];
        } else {
            $data[7] = "chyba";
        }
        $csv_array[$index] = $data;


    }


    $dataPoints = array(
        array("label" => "Count of studens in subject", "y" => $data[0]),
        array("label" => "Count of studens which agree", "y" => $data[1]),
        array("label" => "Count of studens which disagree", "y" => $data[2]),
        array("label" => "Count of studens which hava not made decision", "y" => $data[3]),

    );

    $dataPointsTeams = array(
        array("label" => "Count of teams in subject", "y" => $data[4]),
        array("label" => "Count of teams which are closed", "y" => $data[5]),
        array("label" => "Count of teams waiting for evaluation", "y" => $data[6]),
        array("label" => "Count of team which have studens that not have made agreement", "y" => $data[7]),

    );

    ?>


    <!DOCTYPE html>
    <html lang="sk">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Framework</title>
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
        <script>
            window.onload = function () {

                var chart = new CanvasJS.Chart("chartContainer", {
                    animationEnabled: true,
                    exportEnabled: true,
                    title: {
                        text: "Studens statistics"
                    },

                    data: [{
                        type: "pie",

                        legendText: "{label}",
                        indexLabelFontSize: 16,
                        indexLabel: "{label} - #percent%",
                        yValueFormatString: "#,##0",
                        dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                    }]
                });
                chart.render();

                var secondChart = new CanvasJS.Chart("teamChartContainer", {
                    animationEnabled: true,
                    exportEnabled: true,
                    title: {
                        text: "Teams statistics"
                    },

                    data: [{
                        type: "pie",
                        legendText: "{label}",
                        indexLabelFontSize: 16,
                        indexLabel: "{label} - #percent%",
                        yValueFormatString: "#,##0",
                        dataPoints: <?php echo json_encode($dataPointsTeams, JSON_NUMERIC_CHECK); ?>
                    }]
                });
                secondChart.render();

            }
        </script>
    </head>
    <body>
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
                    <li class="nav-item">
                        <a class="nav-link" href="teamsPoints.php">Reallocation</a>
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
                <span class="navbar-text text-right text-white">Ussername: admin &nbsp;</span>
                <a href="../general.admin/logout.php"><i class="material-icons nav-icon pt-2">exit_to_app</i></a>
            </div>
        </nav>
    </header>

    <div class="container-fluid root-container mt-3">
        <main>


            <div id="chartContainer" style="height: 370px; width: 100%;"></div>
            <table class="table">
                <tr>
                    <th>Count of studens in subject</th>
                    <td><?php echo $data[0] ?></td>
                </tr>
                <tr>
                    <th>Count of studens which agree</th>
                    <td><?php echo $data[1] ?></td>
                </tr>
                <tr>
                    <th>Count of studens which disagree</th>
                    <td><?php echo $data[2] ?></td>
                </tr>
                <tr>
                    <th>Count of studens which hava not made decision</th>
                    <td><?php echo $data[3] ?></td>
                </tr>
            </table>
            <div id="teamChartContainer" style="height: 370px; width: 100%;"></div>

            <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

            <table class="table">
                <tr>
                    <th>Count of teams in subject</th>
                    <td><?php echo $data[4] ?></td>
                </tr>
                <tr>
                    <th>Count of teams which are closed</th>
                    <td><?php echo $data[5] ?></td>
                </tr>
                <tr>
                    <th>Count of teams waiting for evaluation</th>
                    <td><?php echo $data[6] ?></td>
                </tr>
                <tr>
                    <th>Count of team which have studens that not have made agreement</th>
                    <td><?php echo $data[7] ?></td>
                </tr>
            </table>


        </main>
    </div>

    <footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
        <span class="text-white pd-top"> Developed by : LR, DV, MM, SR, MR</span>
    </footer>
    </body>
    </html>

    <?php
} elseif ($_COOKIE['lang'] == 'sk') {

    include_once("../config.php");
    $conn = new mysqli($serverName, $userName, $password, $dbName);
    if ($conn->connect_error) {
        die("Connection to MySQL server failed. " . $conn->connect_error);
    }
    $stmt = $conn->prepare("SET NAMES 'utf8';");
    $stmt->execute();


    if (isset($_SESSION['Subject']) && !empty($_SESSION['Subject']) && (isset($_SESSION['Year']) && !empty($_SESSION['Year']))) {


        $data = array();

        $querry = "SELECT COUNT(task2_students_subject.id_student) as pocet FROM task2_students_subject INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id WHERE task2_subject.year='" . $_SESSION['Year'] . "' AND task2_subject.subject='" . $_SESSION['Subject'] . "' GROUP BY task2_subject.subject";
        $result = $conn->query($querry);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data[0] = $row['pocet'];
        } else {
            $data[0] = "chyba";
        }

        $querry = "SELECT COUNT(task2_students_subject.agree) as pocet FROM task2_students_subject INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id WHERE task2_subject.year='" . $_SESSION['Year'] . "' AND task2_subject.subject='" . $_SESSION['Subject'] . "' AND task2_students_subject.agree='1'";
        $result = $conn->query($querry);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data[1] = $row['pocet'];
        } else {
            $data[1] = "chyba";
        }

        $querry = "SELECT COUNT(task2_students_subject.agree) as pocet FROM task2_students_subject INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id WHERE task2_subject.year='" . $_SESSION['Year'] . "' AND task2_subject.subject='" . $_SESSION['Subject'] . "' AND task2_students_subject.agree='-1'";
        $result = $conn->query($querry);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data[2] = $row['pocet'];
        } else {
            $data[2] = "chyba";
        }

        $querry = "SELECT COUNT(task2_students_subject.agree) as pocet FROM task2_students_subject INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id WHERE task2_subject.year='" . $_SESSION['Year'] . "' AND task2_subject.subject='" . $_SESSION['Subject'] . "' AND task2_students_subject.agree='0'";
        $result = $conn->query($querry);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data[3] = $row['pocet'];
        } else {
            $data[3] = "chyba";
        }

        $querry = "SELECT COUNT(task2_team_points.team) as pocet FROM task2_team_points  WHERE task2_team_points.year='" . $_SESSION['Year'] . "' AND task2_team_points.subject='" . $_SESSION['Subject'] . "' GROUP BY task2_team_points.team";
        $result = $conn->query($querry);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data[4] = $row['pocet'];
        } else {
            $data[4] = "chyba";
        }

        $querry = "SELECT COUNT(task2_team_points.team) as pocet FROM task2_team_points WHERE task2_team_points.year='" . $_SESSION['Year'] . "' AND task2_team_points.subject='" . $_SESSION['Subject'] . "'AND task2_team_points.admin_agree='1'";
        $result = $conn->query($querry);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data[5] = $row['pocet'];
        } else {
            $data[5] = "chyba";
        }

        $querry = "SELECT COUNT(task2_team_points.team) as pocet FROM task2_team_points WHERE task2_team_points.year='" . $_SESSION['Year'] . "' AND task2_team_points.subject='" . $_SESSION['Subject'] . "'AND (task2_team_points.admin_agree='0' OR  task2_team_points.admin_agree IS NULL)";
        $result = $conn->query($querry);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data[6] = $row['pocet'];
        } else {
            $data[6] = "chyba";
        }

        $querry = "SELECT COUNT(*) as pocet FROM (SELECT count(*) FROM task2_students INNER JOIN task2_students_subject on task2_students.id=task2_students_subject.id_student INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id WHERE task2_students_subject.agree=0 AND task2_subject.subject='" . $_SESSION['Subject'] . "' AND task2_subject.year='" . $_SESSION['Year'] . "' GROUP BY task2_students_subject.id_subject) src";
        $result = $conn->query($querry);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data[7] = $row['pocet'];
        } else {
            $data[7] = "chyba";
        }
        $csv_array[$index] = $data;


    }


    $dataPoints = array(
        array("label" => "Počet študentov v predmete", "y" => $data[0]),
        array("label" => "Počet súhlasiacich študentov", "y" => $data[1]),
        array("label" => "Počet nesúhlasiacich študentov", "y" => $data[2]),
        array("label" => "Počet študentov, ktorí sa nevyjadrili", "y" => $data[3]),

    );

    $dataPointsTeams = array(
        array("label" => "Počet tímov", "y" => $data[4]),
        array("label" => "Počet uzavretých tímov", "y" => $data[5]),
        array("label" => "Počet tímov, ku ktorým sa treba vyjadriť", "y" => $data[6]),
        array("label" => "Počet tímov s nevyjadrenými študentami", "y" => $data[7]),

    );

    ?>


    <!DOCTYPE html>
    <html lang="sk">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Štatistika</title>
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
        <script>
            window.onload = function () {

                var chart = new CanvasJS.Chart("chartContainer", {
                    animationEnabled: true,
                    exportEnabled: true,
                    title: {
                        text: "Štatistika študentov"
                    },

                    data: [{
                        type: "pie",

                        legendText: "{label}",
                        indexLabelFontSize: 16,
                        indexLabel: "{label} - #percent%",
                        yValueFormatString: "#,##0",
                        dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                    }]
                });
                chart.render();

                var secondChart = new CanvasJS.Chart("teamChartContainer", {
                    animationEnabled: true,
                    exportEnabled: true,
                    title: {
                        text: "Tímové štatistiky"
                    },

                    data: [{
                        type: "pie",
                        legendText: "{label}",
                        indexLabelFontSize: 16,
                        indexLabel: "{label} - #percent%",
                        yValueFormatString: "#,##0",
                        dataPoints: <?php echo json_encode($dataPointsTeams, JSON_NUMERIC_CHECK); ?>
                    }]
                });
                secondChart.render();

            }
        </script>
    </head>
    <body>
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
                    <li class="nav-item">
                        <a class="nav-link" href="teamsPoints.php">Prerozdelenie</a>
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
                <span class="navbar-text text-right text-white">Používateľ: admin &nbsp;</span>
                <a href="../general.admin/logout.php"><i class="material-icons nav-icon pt-2">exit_to_app</i></a>
            </div>
        </nav>
    </header>

    <div class="container-fluid root-container mt-3">
        <main>


            <div id="chartContainer" style="height: 370px; width: 100%;"></div>
            <table class="table">
                <tr>
                    <th>Počet študentov v predmete</th>
                    <td><?php echo $data[0] ?></td>
                </tr>
                <tr>
                    <th>Počet súhlasiacich študentov</th>
                    <td><?php echo $data[1] ?></td>
                </tr>
                <tr>
                    <th>Počet nesúhlasiacich študentov</th>
                    <td><?php echo $data[2] ?></td>
                </tr>
                <tr>
                    <th>Počet študentov, ktorí sa nevyjadrili</th>
                    <td><?php echo $data[3] ?></td>
                </tr>
            </table>
            <div id="teamChartContainer" style="height: 370px; width: 100%;"></div>

            <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>




                <table class="table">
                    <tr>
                        <th>Počet tímov</th>
                        <td><?php echo $data[4] ?></td>
                    </tr>
                    <tr>
                        <th>Počet uzavretých tímov</th>
                        <td><?php echo $data[5] ?></td>
                    </tr>
                    <tr>
                        <th>Počet tímov, ku ktorým sa treba vyjadriť</th>
                        <td><?php echo $data[6] ?></td>
                    </tr>
                    <tr>
                        <th>Počet tímov s nevyjadrenými študentami</th>
                        <td><?php echo $data[7] ?></td>
                    </tr>
                </table>


        </main>
    </div>

    <footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
        <span class="text-white pd-top">Vyvynuté : LR, DV, MM, SR, MR</span>
    </footer>
    </body>
    </html>

    <?php
}



