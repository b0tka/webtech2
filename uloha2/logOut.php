<?php
/**
 * Created by PhpStorm.
 * User: marin
 * Date: 19.5.2019
 * Time: 12:46
 */

session_start();
session_destroy();
header("Location: http://147.175.121.210:8117/webte2/uloha2/index.php?log-out=success");
die();
