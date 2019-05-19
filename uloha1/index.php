<?php
session_start();
if((isset($_SESSION['uloha1_user']))) {
    header("Location:http://147.175.121.210:8117/webte2/uloha1/client-index.php");
}

if(isset($_GET['lang'])) {
    setcookie('lang', htmlspecialchars($_GET['lang']), strtotime("tomorrow"), '/');
    header("Location:index.php");
}

if(($_COOKIE['lang'] == 'sk') or (!isset($_COOKIE['lang']))) {

?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Úloha 1</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
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
                    <a class="nav-link" href="../uloha1/index.php">Úloha 1</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../uloha2/index.php">Úloha 2</a>
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
            <span class="navbar-text text-right text-white">
<!--                        Používateľ : --><?php //echo $_SESSION['uloha1_username']; ?>
                </span>
<!--            <a href="logout.php"><i class="material-icons nav-icon pt-2">exit_to_app</i></a>-->
        </div>
    </nav>
</header>

<div class="container-fluid root-container mt-3">
    <main>
        <div class="container mt-5 px-5">
            <!--        core website-->
            <h2>Prihlásiť sa ako študent</h2>
            <?php
            if (isset($_GET) && ($_GET['error'] === "invalid-user")) {
                ?>
                <div class="alert alert-danger">
                    <strong>Prihlásenie nebolo úspešné!</strong> Nesprávne používateľské meno alebo heslo.
                </div>
                <?php
            }
            ?>
            <form action="ldapLogin.php" method="post">
                <div class="form-group">
                    <label for="login">Meno</label>
                    <input name="login" class="form-control" id="login" placeholder="Zadaj meno">
                </div>
                <div class="form-group">
                    <label for="password">Heslo</label>
                    <input name="password" type="password" class="form-control" id="password" placeholder="Zadaj heslo">
                </div>

                <button type="submit" class="btn btn-primary">Prihlásiť</button>
            </form>
            <h2>Prihlásiť ako administrátor</h2>
            <form action="../general.admin/login.php">
                <button type="submit" class="btn btn-primary">Prihlásiť</button>

            </form>
        </div>
    </main>
</div>

<footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
    <span class="text-white pd-top">Vývojári : LR, DV, MM, SR, MR</span>
</footer>
<?php
} elseif($_COOKIE['lang'] == 'en') {
    ?>
<!--<!DOCTYPE html>-->
<!--<html lang="sk">-->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Task 1</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
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
            <span class="navbar-text text-right text-white">
<!--                        Username : --><?php //echo $_SESSION['uloha1_username']; ?>
                </span>
<!--            <a href="logout.php"><i class="material-icons nav-icon pt-2">exit_to_app</i></a>-->
        </div>
    </nav>
</header>

<div class="container-fluid root-container mt-3">
    <main>
        <div class="container mt-5 px-5">
            <!--        core website-->
            <h2>Login as student</h2>
            <?php
            if (isset($_GET) && ($_GET['error'] === "invalid-user")) {
                ?>
                <div class="alert alert-danger">
                    <strong>Login has been refused!</strong> Bad login or password.
                </div>
                <?php
            }
            ?>
            <form action="ldapLogin.php" method="post">
                <div class="form-group">
                    <label for="login">Name</label>
                    <input name="login" class="form-control" id="login" placeholder="Enter login">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input name="password" type="password" class="form-control" id="password" placeholder="Password">
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <h2>Login as administrator</h2>
            <form action="../general.admin/login.php">
                <button type="submit" class="btn btn-primary">Submit</button>

            </form>
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
