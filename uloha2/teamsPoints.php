<?php
session_start();
if (!isset($_COOKIE['isAdmin']) and $_COOKIE['isAdmin'] !== 'admin') {
    header("Location:login.php");
}
if(isset($_GET['lang'])) {
    setcookie('lang', htmlspecialchars($_GET['lang']), strtotime("tomorrow"), '/');
    header("Location: http://147.175.121.210:8117/webte2/uloha2/teamsPoints.php");
}
if (($_COOKIE['lang'] == 'sk') or (!isset($_COOKIE['lang']))) {

?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Body pre tímy</title>
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
                        <a class="dropdown-item" href="teamsPoints.php?lang=sk">Slovenský</a>
                        <a class="dropdown-item" href="teamsPoints.php?lang=en">Anglický</a>
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
        <div class="container mt-5 px-5">
            <?php

            echo "<h2>" . ucfirst($_SESSION['Subject']) . "</h2>"
            ?>
            <form action="#" enctype="multipart/form-data" method="post">
                <table class="table">
                    <thead class="thead-light">
                    <tr>
                        <th scope="col">Číslo tímu</th>
                        <th scope="col">Členovia tímu</th>
                        <th scope="col">Body</th>
                        <th scope="col">Prerozdelenia</th>

                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    include_once("../config.php");

                    $conn = new mysqli($serverName, $userName, $password, $dbName);
                    if ($conn->connect_error) {
                        die("Connection to MySQL server failed. " . $conn->connect_error);
                    }
                    $stmt = $conn->prepare("SET NAMES 'utf8';");
                    $stmt->execute();

                    if (isset($_SESSION['Subject']) && !empty($_SESSION['Subject'])) {

                        $selectAllFromTeamProject = "SELECT task2_team_points.team,task2_team_points.points 
                                                     FROM task2_team_points 
                                                     where task2_team_points.subject='" . $_SESSION['Subject'] . "' AND task2_team_points.year='" . $_SESSION['Year'] . "'";
                        $result = $conn->query($selectAllFromTeamProject);


                        $teams = array();
                        if ($result->num_rows > 0) {

                            while ($row = $result->fetch_assoc()) {

                                $SelectNamesOfTeam = "SELECT task2_students.name 
                                                      FROM task2_students INNER JOIN task2_students_subject on task2_students.id=task2_students_subject.id_student INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id 
                                                      where task2_subject.year='" . $_SESSION['Year'] . "' AND task2_subject.subject='" . $_SESSION['Subject'] . "' AND task2_students_subject.team='" . $row["team"] . "'";
                                $resultAllNames = $conn->query($SelectNamesOfTeam);
                                $arr = array();
                                if ($resultAllNames->num_rows > 0) {
                                    while ($rowNames = $resultAllNames->fetch_assoc()) {
                                        array_push($arr, $rowNames["name"]);
                                    }
                                }

                                array_push($teams, $row["team"]);
                                echo "<tr><td>".$row["team"]."</td><td>".implode(",<br> ",$arr)."</td><td> <div class=\"col-8\"><input value='".$row["points"]."' name='".$row["team"]."' class=\"form-control\" type=\"number\" required  min=\"0\" max=\"120\"></div></td><td><a class=\"btn btn-outline-info\" href=\"http://147.175.121.210:8117/webte2/uloha2/pointsOfTeams.php?team=".$row["team"]."\" role=\"button\">Prerozdelenie</a></td></tr>";

                            }
                        }

                    }
                    ?>
                    </tbody>
                </table>
                <div class="form-group" style="margin-top: 1%">
                    <button type="submit" name="Odosli" class="btn btn-primary">Pridaj body</button>
                </div>
                <?php
                echo "<a class=\"btn btn-outline-info\" href=\"http://147.175.121.210:8117/webte2/uloha2/export.php \"  role=\"button\">Export dát</a>";
                echo "<a class=\"btn btn-outline-info\" href=\"http://147.175.121.210:8117/webte2/uloha2/statistics.php \"  role=\"button\">Štatistika</a>";
                ?>

            </form>
        </div>
    </main>
</div>
<?php
if (isset($_POST['Odosli'])) {
    foreach ($teams as $team) {
        $sqlUpdate = "UPDATE task2_team_points SET points = '" . $_POST[$team] . "' 
                      WHERE task2_team_points.year='" . $_SESSION['Year'] . "' AND task2_team_points.subject='" . $_SESSION['Subject'] . "' AND task2_team_points.team='$team'";
        $resultUpdate = $conn->query($sqlUpdate);
        if ($resultUpdate === TRUE) {
            if ($conn->affected_rows > 0) {
                //Premazanie hlasovania admina k danemu timu
                $sqlDeleteAgreeOfAdmin = "UPDATE task2_team_points SET admin_agree = '0'  
                                          WHERE task2_team_points.year='" . $_SESSION['Year'] . "' AND task2_team_points.subject='" . $_SESSION['Subject'] . "' AND task2_team_points.team='$team'";
                $conn->query($sqlDeleteAgreeOfAdmin);


                //SELEKT vsetkych ucastnikov timu, ktorim sa zmenili celkove body
                $selectMembersOfTeam = "SELECT task2_students_subject.id_student,task2_students_subject.id_subject,task2_students_subject.team 
                                        FROM task2_students INNER JOIN task2_students_subject on task2_students.id=task2_students_subject.id_student INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id 
                                        WHERE task2_subject.subject='" . $_SESSION['Subject'] . "' AND task2_subject.year='" . $_SESSION['Year'] . "' AND task2_students_subject.team='$team'";
                $resultAllInfo = $conn->query($selectMembersOfTeam);
                if ($resultAllInfo->num_rows > 0) {
                    while ($row = $resultAllInfo->fetch_assoc()) {
                        //UPDATE na nastavenie noveho odsuhlasovania studentov
                        $sqlUpdateAgree = "UPDATE task2_students_subject SET agree = '0',body = '0' 
                                           WHERE task2_students_subject.id_student='" . $row['id_student'] . "' AND task2_students_subject.id_subject='" . $row['id_subject'] . "' AND task2_students_subject.team='$team'";
                        $conn->query($sqlUpdateAgree);
                    }
                }


            }

        } else {
            echo "Error" . $conn->error;
        }
    }

    echo "<script type='text/javascript'>  window.location='teamsPoints.php?lang=sk'; </script>";

}

?>


<footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
    <span class="text-white pd-top">Vývojári: LR, DV, MM, SR, MR</span>
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Team points</title>
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
    header("Location:login.php");
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
                        <a class="dropdown-item" href="teamsPoints.php?lang=sk">Slovak</a>
                        <a class="dropdown-item" href="teamsPoints.php?lang=en">English</a>
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
        <div class="container mt-5 px-5">
            <?php

            echo "<h2>" . ucfirst($_SESSION['Subject']) . "</h2>"
            ?>
            <form action="#" enctype="multipart/form-data" method="post">
                <table class="table">
                    <thead class="thead-light">
                    <tr>
                        <th scope="col">Number of team</th>
                        <th scope="col">Members of team</th>
                        <th scope="col">Points</th>
                        <th scope="col">Reallocation</th>

                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    include_once("../config.php");

                    $conn = new mysqli($serverName, $userName, $password, $dbName);
                    if ($conn->connect_error) {
                        die("Connection to MySQL server failed. " . $conn->connect_error);
                    }
                    $stmt = $conn->prepare("SET NAMES 'utf8';");
                    $stmt->execute();

                    if (isset($_SESSION['Subject']) && !empty($_SESSION['Subject'])) {

                        $selectAllFromTeamProject = "SELECT task2_team_points.team,task2_team_points.points 
                                                     FROM task2_team_points 
                                                     where task2_team_points.subject='" . $_SESSION['Subject'] . "' AND task2_team_points.year='" . $_SESSION['Year'] . "'";
                        $result = $conn->query($selectAllFromTeamProject);


                        $teams = array();
                        if ($result->num_rows > 0) {

                            while ($row = $result->fetch_assoc()) {

                                $SelectNamesOfTeam = "SELECT task2_students.name 
                                                      FROM task2_students INNER JOIN task2_students_subject on task2_students.id=task2_students_subject.id_student INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id 
                                                      where task2_subject.year='" . $_SESSION['Year'] . "' AND task2_subject.subject='" . $_SESSION['Subject'] . "' AND task2_students_subject.team='" . $row["team"] . "'";
                                $resultAllNames = $conn->query($SelectNamesOfTeam);
                                $arr = array();
                                if ($resultAllNames->num_rows > 0) {
                                    while ($rowNames = $resultAllNames->fetch_assoc()) {
                                        array_push($arr, $rowNames["name"]);
                                    }
                                }

                                array_push($teams, $row["team"]);
                                echo "<tr><td>".$row["team"]."</td><td>".implode(",<br> ",$arr)."</td><td> <div class=\"col-8\"><input value='".$row["points"]."' name='".$row["team"]."' class=\"form-control\" type=\"number\" required  min=\"0\" max=\"120\"></div></td><td><a class=\"btn btn-outline-info\" href=\"http://147.175.121.210:8117/webte2/uloha2/pointsOfTeams.php?team=".$row["team"]."\" role=\"button\">Reallocation</a></td></tr>";

                            }
                        }

                    }
                    ?>
                    </tbody>
                </table>
                <div class="form-group" style="margin-top: 1%">
                    <button type="submit" name="Odosli" class="btn btn-primary">Add points</button>
                </div>
                <?php
                echo "<a class=\"btn btn-outline-info\" href=\"http://147.175.121.210:8117/webte2/uloha2/export.php \"  role=\"button\">Export of data</a>";
                echo "<a class=\"btn btn-outline-info\" href=\"http://147.175.121.210:8117/webte2/uloha2/statistics.php \"  role=\"button\">Statistics</a>";
                ?>

            </form>
        </div>
    </main>
</div>
<?php
if (isset($_POST['Odosli'])) {
    foreach ($teams as $team) {
        $sqlUpdate = "UPDATE task2_team_points SET points = '" . $_POST[$team] . "' 
                      WHERE task2_team_points.year='" . $_SESSION['Year'] . "' AND task2_team_points.subject='" . $_SESSION['Subject'] . "' AND task2_team_points.team='$team'";
        $resultUpdate = $conn->query($sqlUpdate);
        if ($resultUpdate === TRUE) {
            if ($conn->affected_rows > 0) {
                //Premazanie hlasovania admina k danemu timu
                $sqlDeleteAgreeOfAdmin = "UPDATE task2_team_points SET admin_agree = '0'  
                                          WHERE task2_team_points.year='" . $_SESSION['Year'] . "' AND task2_team_points.subject='" . $_SESSION['Subject'] . "' AND task2_team_points.team='$team'";
                $conn->query($sqlDeleteAgreeOfAdmin);


                //SELEKT vsetkych ucastnikov timu, ktorim sa zmenili celkove body
                $selectMembersOfTeam = "SELECT task2_students_subject.id_student,task2_students_subject.id_subject,task2_students_subject.team 
                                        FROM task2_students INNER JOIN task2_students_subject on task2_students.id=task2_students_subject.id_student INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id 
                                        WHERE task2_subject.subject='" . $_SESSION['Subject'] . "' AND task2_subject.year='" . $_SESSION['Year'] . "' AND task2_students_subject.team='$team'";
                $resultAllInfo = $conn->query($selectMembersOfTeam);
                if ($resultAllInfo->num_rows > 0) {
                    while ($row = $resultAllInfo->fetch_assoc()) {
                        //UPDATE na nastavenie noveho odsuhlasovania studentov
                        $sqlUpdateAgree = "UPDATE task2_students_subject SET agree = '0',body = '0' 
                                           WHERE task2_students_subject.id_student='" . $row['id_student'] . "' AND task2_students_subject.id_subject='" . $row['id_subject'] . "' AND task2_students_subject.team='$team'";
                        $conn->query($sqlUpdateAgree);
                    }
                }


            }

        } else {
            echo "Error" . $conn->error;
        }
    }


    echo "<script type='text/javascript'>  window.location='teamsPoints.php?lang=en'; </script>";



}

?>


<footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
    <span class="text-white pd-top">Developed by : LR, DV, MM, SR, MR</span>
</footer>
</body>
</html>
    <?php

}

?>
