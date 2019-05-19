<?php

if (!isset($_COOKIE['isAdmin']) and $_COOKIE['isAdmin'] !== 'admin') {
    header("Location:login.php");
}
if(isset($_GET['lang'])) {
    setcookie('lang', htmlspecialchars($_GET['lang']), strtotime("tomorrow"), '/');
    header("Location: http://147.175.121.210:8117/webte2/uloha2/pointsOfTeams.php?team=3");
}
if (($_COOKIE['lang'] == 'sk') or (!isset($_COOKIE['lang']))) {

?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Prerozdelenie</title>
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
                        <a class="dropdown-item" href="pointsOfTeams.php?lang=sk&team=3">Slovenský</a>
                        <a class="dropdown-item" href="pointsOfTeams.php?lang=en&team=3">Anglický</a>
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
        <?php

        include_once("../config.php");
        $conn = new mysqli($serverName, $userName, $password, $dbName);
        if ($conn->connect_error) {
            die("Connection to MySQL server failed. " . $conn->connect_error);
        }
        $stmt = $conn->prepare("SET NAMES 'utf8';");
        $stmt->execute();
        session_start();

        $someoneDisagre = true;
        if (isset($_GET['agree'])) {
            if ($_GET['agree'] == "yes"){
                $sqlUpdate = "UPDATE task2_team_points SET admin_agree = '1' where task2_team_points.subject='" . $_SESSION['Subject'] . "' AND task2_team_points.year='" . $_SESSION['Year'] . "' AND task2_team_points.team='" . $_GET['team'] . "'";
            }
            else {
                $sqlUpdate = "UPDATE task2_team_points SET admin_agree = '-1' where task2_team_points.subject='" . $_SESSION['Subject'] . "' AND task2_team_points.year='" . $_SESSION['Year'] . "' AND task2_team_points.team='" . $_GET['team'] . "'";
            }
            if ($conn->query($sqlUpdate) === TRUE) {
            } else {
                echo "Error" . $conn->error;
            }
//            header("Location: http://147.175.121.210:8117/webte2/uloha2/pointsOfTeams.php?team=1");
        }


        if (isset($_GET['team'])) {
            echo "<h2>Skupina : " . $_GET['team'] . ". tím</h2>";
            $teamSelected=$_GET['team'];

            if (isset($_SESSION['Subject']) && !empty($_SESSION['Subject'])) {

                //Selekt na zistenie poctu bodov
                $selectPointsFromTeamProject = "SELECT task2_team_points.team,task2_team_points.points,task2_team_points.admin_agree FROM task2_team_points where task2_team_points.subject='" . $_SESSION['Subject'] . "' AND task2_team_points.year='" . $_SESSION['Year'] . "' AND task2_team_points.team='" . $_GET['team'] . "'";
                $result = $conn->query($selectPointsFromTeamProject);
                $buttons=null;
                if ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    $buttons=$row["admin_agree"];
                    echo "<h3>Body celkom :" . $row["points"] . "</h3>";
                } else {
                    header("Location: http://147.175.121.210:8117/webte2/uloha2/pointsOfTeams.php");
                }

                echo "<table class=\"table\">
                <thead class=\"thead-light\">
                <tr>
                    <th scope=\"col\">Email</th>
                    <th scope=\"col\">Meno</th>
                    <th scope=\"col\">Body</th>
                    <th scope=\"col\">Potvrdenie</th>

                </tr>
                </thead>
                <tbody>";

                //Selekt na zistenie informacii o time
                $SelectValuesOfTeam = "SELECT task2_students.mail,task2_students.name,task2_students_subject.body,task2_students_subject.agree FROM task2_students INNER JOIN task2_students_subject on task2_students.id=task2_students_subject.id_student INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id where task2_subject.subject='" . $_SESSION['Subject'] . "' AND task2_subject.year='" . $_SESSION['Year'] . "' AND task2_students_subject.team='" . $_GET['team'] . "'";
                $resultAllInfo = $conn->query($SelectValuesOfTeam);
                if ($resultAllInfo->num_rows > 0) {
                    while ($row = $resultAllInfo->fetch_assoc()) {
                        echo "<tr><td>" . $row["mail"] . "</td><td>" . $row["name"] . "</td><td>" . $row["body"] . "</td>";
                        if ($row["agree"] > 0) {
                            echo "<td><img src=\"files/like.png\" alt=\"\" style=\"width:30px; height:auto;\"></td></tr>";
                        } elseif ($row["agree"] < 0) {
                            echo "<td><img src=\"files/dislike.png\" alt=\"\" style=\"width:30px; height:auto;\"></td></tr>";
                        } else {
                            $someoneDisagre=false;
                            echo "<td></td></tr>";
                        }
                    }
                }
                echo " </tbody>
                      </table>";
            }


            if( (!$buttons==1 || !$buttons==-1) && $someoneDisagre ){
                echo "<a class=\"btn btn-outline-info\" href=\"http://147.175.121.210:8117/webte2/uloha2/teamsPoints.php\" role=\"button\">Späť <img src=\"files/back.jpg\" alt=\"\" style=\"width:26px; height:auto;\"></a>";
                echo "<a class=\"btn btn-outline-success\" href=\"http://147.175.121.210:8117/webte2/uloha2/pointsOfTeams.php?team=".$teamSelected."&agree=yes\" name='suhlas' id='suhlas' role=\"button\">Suhlasím <img src=\"files/like.png\" alt=\"\" style=\"width:30px; height:auto;\"></a>";
                echo "<a class=\"btn btn-outline-danger\"  href=\"http://147.175.121.210:8117/webte2/uloha2/pointsOfTeams.php?team=".$teamSelected."&agree=no\" name='nesuhlas' role=\"button\">Nesuhlasím <img src=\"files/dislike.png\" alt=\"\" style=\"width:30px; height:auto;\"></a>";

            }
            elseif($buttons==1 ){
                echo "<p><font color=\"green\">Admin súhlasi s prerozdelenim bodov <img src=\"files/like.png\" alt=\"\" style=\"width:30px; height:auto;\"></font></p>";
                echo "<a class=\"btn btn-outline-info\" href=\"http://147.175.121.210:8117/webte2/uloha2/teamsPoints.php\" role=\"button\">Späť <img src=\"files/back.jpg\" alt=\"\" style=\"width:26px; height:auto;\"></a>";
            }
            elseif($buttons==-1){
                echo "<p><font color=\"red\">Admin nesúhlasi s prerozdelenim bodov <img src=\"files/dislike.png\" alt=\"\" style=\"width:30px; height:auto;\"></font></p>";
                echo "<a class=\"btn btn-outline-info\" href=\"http://147.175.121.210:8117/webte2/uloha2/teamsPoints.php\" role=\"button\">Späť <img src=\"files/back.jpg\" alt=\"\" style=\"width:26px; height:auto;\"></a>";
            }
            else{
                echo "<a class=\"btn btn-outline-info\" href=\"http://147.175.121.210:8117/webte2/uloha2/teamsPoints.php\" role=\"button\">Späť <img src=\"files/back.jpg\" alt=\"\" style=\"width:26px; height:auto;\"></a>";
            }


        } else {
            header("Location: http://147.175.121.210:8117/webte2/uloha2/teamsPoints.php");
        }
        ?>
    </div>
</div>

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
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reallocation</title>
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
                        <a class="dropdown-item" href="pointsOfTeams.php?lang=sk&team=3">Slovak</a>
                        <a class="dropdown-item" href="pointsOfTeams.php?lang=en&team=3">English</a>
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
        <?php

        include_once("../config.php");
        $conn = new mysqli($serverName, $userName, $password, $dbName);
        if ($conn->connect_error) {
            die("Connection to MySQL server failed. " . $conn->connect_error);
        }
        $stmt = $conn->prepare("SET NAMES 'utf8';");
        $stmt->execute();
        session_start();

        $someoneDisagre = true;
        if (isset($_GET['agree'])) {
            if ($_GET['agree'] == "yes"){
                $sqlUpdate = "UPDATE task2_team_points SET admin_agree = '1' where task2_team_points.subject='" . $_SESSION['Subject'] . "' AND task2_team_points.year='" . $_SESSION['Year'] . "' AND task2_team_points.team='" . $_GET['team'] . "'";
            }
            else {
                $sqlUpdate = "UPDATE task2_team_points SET admin_agree = '-1' where task2_team_points.subject='" . $_SESSION['Subject'] . "' AND task2_team_points.year='" . $_SESSION['Year'] . "' AND task2_team_points.team='" . $_GET['team'] . "'";
            }
            if ($conn->query($sqlUpdate) === TRUE) {
            } else {
                echo "Error" . $conn->error;
            }
//            header("Location: http://147.175.121.210:8117/webte2/uloha2/pointsOfTeams.php?team=1");
        }


        if (isset($_GET['team'])) {
            echo "<h2>Group : " . $_GET['team'] . ". team</h2>";
            $teamSelected=$_GET['team'];

            if (isset($_SESSION['Subject']) && !empty($_SESSION['Subject'])) {

                //Selekt na zistenie poctu bodov
                $selectPointsFromTeamProject = "SELECT task2_team_points.team,task2_team_points.points,task2_team_points.admin_agree FROM task2_team_points where task2_team_points.subject='" . $_SESSION['Subject'] . "' AND task2_team_points.year='" . $_SESSION['Year'] . "' AND task2_team_points.team='" . $_GET['team'] . "'";
                $result = $conn->query($selectPointsFromTeamProject);
                $buttons=null;
                if ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    $buttons=$row["admin_agree"];
                    echo "<h3>Total points :" . $row["points"] . "</h3>";
                } else {
                    header("Location: http://147.175.121.210:8117/webte2/uloha2/teamsPoints.php");
                }

                echo "<table class=\"table\">
                <thead class=\"thead-light\">
                <tr>
                    <th scope=\"col\">Email</th>
                    <th scope=\"col\">Name</th>
                    <th scope=\"col\">Points</th>
                    <th scope=\"col\">Confirmations</th>

                </tr>
                </thead>
                <tbody>";

                //Selekt na zistenie informacii o time
                $SelectValuesOfTeam = "SELECT task2_students.mail,task2_students.name,task2_students_subject.body,task2_students_subject.agree FROM task2_students INNER JOIN task2_students_subject on task2_students.id=task2_students_subject.id_student INNER JOIN task2_subject ON task2_students_subject.id_subject=task2_subject.id where task2_subject.subject='" . $_SESSION['Subject'] . "' AND task2_subject.year='" . $_SESSION['Year'] . "' AND task2_students_subject.team='" . $_GET['team'] . "'";
                $resultAllInfo = $conn->query($SelectValuesOfTeam);
                if ($resultAllInfo->num_rows > 0) {
                    while ($row = $resultAllInfo->fetch_assoc()) {
                        echo "<tr><td>" . $row["mail"] . "</td><td>" . $row["name"] . "</td><td>" . $row["body"] . "</td>";
                        if ($row["agree"] > 0) {
                            echo "<td><img src=\"files/like.png\" alt=\"\" style=\"width:30px; height:auto;\"></td></tr>";
                        } elseif ($row["agree"] < 0) {
                            echo "<td><img src=\"files/dislike.png\" alt=\"\" style=\"width:30px; height:auto;\"></td></tr>";
                        } else {
                            $someoneDisagre=false;
                            echo "<td></td></tr>";
                        }
                    }
                }
                echo " </tbody>
                      </table>";
            }


            if( (!$buttons==1 || !$buttons==-1) && $someoneDisagre ){
                echo "<a class=\"btn btn-outline-info\" href=\"http://147.175.121.210:8117/webte2/uloha2/teamsPoints.php\" role=\"button\">Back <img src=\"files/back.jpg\" alt=\"\" style=\"width:26px; height:auto;\"></a>";
                echo "<a class=\"btn btn-outline-success\" href=\"http://147.175.121.210:8117/webte2/uloha2/pointsOfTeams.php?team=".$teamSelected."&agree=yes\" name='suhlas' id='suhlas' role=\"button\">Agree <img src=\"files/like.png\" alt=\"\" style=\"width:30px; height:auto;\"></a>";
                echo "<a class=\"btn btn-outline-danger\"  href=\"http://147.175.121.210:8117/webte2/uloha2/pointsOfTeams.php?team=".$teamSelected."&agree=no\" name='nesuhlas' role=\"button\">Disagree <img src=\"files/dislike.png\" alt=\"\" style=\"width:30px; height:auto;\"></a>";

            }
            elseif($buttons==1 ){
                echo "<p><font color=\"green\">Admin agree with the students devided points <img src=\"files/like.png\" alt=\"\" style=\"width:30px; height:auto;\"></font></p>";
                echo "<a class=\"btn btn-outline-info\" href=\"http://147.175.121.210:8117/webte2/uloha2/teamsPoints.php\" role=\"button\">Back <img src=\"files/back.jpg\" alt=\"\" style=\"width:26px; height:auto;\"></a>";
            }
            elseif($buttons==-1){
                echo "<p><font color=\"red\">Admin disagree with the students devided points <img src=\"files/dislike.png\" alt=\"\" style=\"width:30px; height:auto;\"></font></p>";
                echo "<a class=\"btn btn-outline-info\" href=\"http://147.175.121.210:8117/webte2/uloha2/teamsPoints.php\" role=\"button\">Back <img src=\"files/back.jpg\" alt=\"\" style=\"width:26px; height:auto;\"></a>";
            }
            else{
                echo "<a class=\"btn btn-outline-info\" href=\"http://147.175.121.210:8117/webte2/uloha2/teamsPoints.php\" role=\"button\">Back <img src=\"files/back.jpg\" alt=\"\" style=\"width:26px; height:auto;\"></a>";
            }


        } else {
            header("Location: http://147.175.121.210:8117/webte2/uloha2/teamsPoints.php");
        }
        ?>
    </div>
</div>

<footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
    <span class="text-white pd-top">Developed by : LR, DV, MM, SR, MR</span>
</footer>
</body>
</html>
    <?php

}

?>