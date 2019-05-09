<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php

    if (isset($_POST['login']) and isset($_POST['name']) and isset($_POST['password'])) {

        // tu bude nejake overovanie mena a hesla pre admina

        setcookie('isAdmin', 'admin', strtotime("tomorrow"), '/');
        header('Location: index.php');
    }



    if(isset($_COOKIE['isAdmin']) and $_COOKIE['isAdmin'] === 'admin') {
        header('Location:index.php');
    } else {
        ?>
        <div class="container text-center" style="margin-top: 10%">
            <h2>Prihlásenie sa ako Administrátor</h2>
            <!-- Button to Open the Modal -->
            <button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#myModal" id="modal">
                Prihlásiť
            </button>

            <!-- The Modal -->
            <div class="modal fade" id="myModal">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">Admin</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <!-- Modal body -->
                        <form class="form-horizontal" action="./login.php" method="post">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-12" for="place">Meno:</label>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" required placeholder="Zadajte meno" name="name">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-sm-12" for="discipline">Heslo:</label>
                                            <div class="col-sm-12">
                                                <input type="password" class="form-control" required name="password">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="modal-footer">
                                <input type="submit" name="login" class="btn btn-secondary"  value="Potvrdiť">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            (function () {
                $('#modal').click();
            })();
        </script>
        <?php
    }
?>
</body>
</html>