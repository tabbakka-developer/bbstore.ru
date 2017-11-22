<?php
/**
 * Created by PhpStorm.
 * User: vladp
 * Date: 10.10.2017
 * Time: 14:51
 */


$obj = $_GET['obj'];
$id = $_GET['id'];


?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Редактор</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

</head>
<body>
<div class="container">
    <br>
    <br>
    <br>

<?php

switch ($obj) {

    case "tovar":
        ?>

        <form class="form" action="app/post_handler.php" method="post">
            <label for="red">Подтвердите удаление:</label>
            <input type="hidden" name="id" value="<?php echo $id;?>">
            <input type="hidden" name="method" value="delete">
            <input type="hidden" name="obj" value="tovar">

            <button class="btn btn-lg btn-danger btn-block" type="submit">Удалить</button>
        </form>

        <?php
        break;

    case "category":
        ?>

        <form class="form" action="app/post_handler.php" method="post">
            <label for="red">Подтвердите удаление:</label>
            <input type="hidden" name="id" value="<?php echo $id;?>">
            <input type="hidden" name="method" value="delete">
            <input type="hidden" name="obj" value="category">

            <button class="btn btn-lg btn-danger btn-block" type="submit">Удалить</button>
        </form>

        <?php
        break;

    case "photo":
        $url = $_GET['url'];
        ?>

        <form class="form" action="app/post_handler.php" method="post">
            <label for="red">Подтвердите удаление:</label>
            <input type="hidden" name="id" value="<?php echo $id;?>">
            <input type="hidden" name="method" value="delete">
            <input type="hidden" name="obj" value="photo">
            <input type="hidden" name="url" value="<?php echo $url;?>">

            <button class="btn btn-lg btn-danger btn-block" type="submit">Удалить</button>
        </form>

        <?php
        break;
}
?>

