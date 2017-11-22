<?php
/**
 * Created by PhpStorm.
 * User: vladp
 * Date: 10.10.2017
 * Time: 12:57
 */
session_start();
if(isset($_SESSION['token'])){
    header("Location: main.php");
}
else {
    session_destroy();
    header("Location: login.php");
}