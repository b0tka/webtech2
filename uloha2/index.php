<?php
if(isset($_GET['lang'])) {
    setcookie('lang', htmlspecialchars($_GET['lang']), strtotime("tomorrow"), '/');
    header("Location:index.php");
}

if(($_COOKIE['lang'] == 'en') or (!isset($_COOKIE['lang']))) {
    ?>

    <!DOCTYPE html>
    <html lang="sk">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Task 1</title>
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
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                            Jazyk
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="index.php?lang=sk">Slovenský</a>
                            <a class="dropdown-item" href="index.php?lang=en">Anglický</a>
                        </div>
                    </li>
                </ul>

            </div>
        </nav>
    </header>

    <div class="container-fluid root-container mt-3">
        <main>
            <div class="container mt-5 px-5">
                <!--        core website-->
                <h2 class="index-subtitle text-center">Login as student</h2>
                <?php
                if (isset($_GET) && ($_GET['error'] === "invalid-user")) {
                    ?>
                    <div class="alert alert-danger">
                        <strong>Login has been refused!</strong> Bad login or password.
                    </div>
                    <?php
                }
                ?>
                <form action="logUser.php" method="post">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="login">Name</label>
                            <input name="login" class="form-control" id="login" placeholder="Enter login">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="password">Password</label>
                            <input name="password" type="password" class="form-control" id="password"
                                   placeholder="Password">
                        </div>
                    </div>

                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="type-of-login" value="ldap"
                               checked="checked">
                        <label class="form-check-label" for="exampleCheck1">LDAP student</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="type-of-login" value="local">
                        <label class="form-check-label" for="exampleCheck1">Local log in student</label>
                    </div>
                    <div class=" text-center">
                        <button type="submit" class="btn btn-primary student-login-button">Submit</button>
                    </div>
                </form>
                <h2 class="index-subtitle text-center">Login as administrator</h2>
                <form action="../general.admin/login.php">
                    <div class=" text-center">
                        <button type="submit" class="btn btn-primary admin-login-button center-block">Submit</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
        <span class="text-white pd-top">Developed by : LR, DV, MM, SR, MR</span>
    </footer>
    </body>
    </html>
    <?php
} elseif($_COOKIE['lang'] == 'sk') {
    ?>
    <!DOCTYPE html>
    <html lang="sk">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Úloha 1</title>
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
                            <a class="dropdown-item" href="index.php?lang=sk">Slovenský</a>
                            <a class="dropdown-item" href="index.php?lang=en">Anglický</a>
                        </div>
                    </li>
                </ul>

            </div>
        </nav>
    </header>

    <div class="container-fluid root-container mt-3">
        <main>
            <div class="container mt-5 px-5">
                <!--        core website-->
                <h2 class="index-subtitle text-center">Prihlásiť sa ako študent</h2>
                <?php
                if (isset($_GET) && ($_GET['error'] === "invalid-user")) {
                    ?>
                    <div class="alert alert-danger">
                        <strong>Prihlásenie bolo odmietnuté</strong> Zlé heslo alebo login
                    </div>
                    <?php
                }
                ?>
                <form action="logUser.php" method="post">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="login">Meno</label>
                            <input name="login" class="form-control" id="login" placeholder="Vlož meno">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="password">Heslo</label>
                            <input name="password" type="password" class="form-control" id="password"
                                   placeholder="Heslo">
                        </div>
                    </div>

                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="type-of-login" value="ldap"
                               checked="checked">
                        <label class="form-check-label" for="exampleCheck1">LDAP študent</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="type-of-login" value="local">
                        <label class="form-check-label" for="exampleCheck1">Lokálne prihlásenie</label>
                    </div>
                    <div class=" text-center">
                        <button type="submit" class="btn btn-primary student-login-button">Potvrď</button>
                    </div>
                </form>
                <h2 class="index-subtitle text-center">Prihláste sa ako administrátor</h2>
                <form action="../general.admin/login.php">
                    <div class=" text-center">
                        <button type="submit" class="btn btn-primary admin-login-button center-block">Potvrdte</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <footer class="footer text-center fixed-bottom navbar-custom" style="height: 50px;">
        <span class="text-white pd-top">Vývojári : LR, DV, MM, SR, MR</span>
    </footer>
    </body>
    </html>

<?php }
