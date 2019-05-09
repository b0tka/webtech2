<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Framework</title>
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
    header("Location:http://147.175.121.210:8117/webte2/general.admin/login.php");
}
?>
<header>
    <nav class="navbar navbar-light navbar-custom navbar-expand-lg">
        <a class="navbar-brand" href="../general.admin/index.php">
            <img src="../general.admin/admin.png" width="100" height="55" alt="admin">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="./login.php">Úloha 1</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./history.php">Úloha 2</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../uloha3/admin-index.php">Úloha 3</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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

<div class="container mt-3">
    <div class="container-fluid">
        <div class="col-sm-12 text-center" style="margin-top: 10%">
            <a href="admin-index.php" class="btn btn-primary">Nahraj CSV</a>
        </div>
    </div>
</div>


<footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
    <span class="text-white pd-top">Developed by : LR, DV, MM, SR, MR</span>
</footer>
</body>
</html>