<?php
/**
 * Created by PhpStorm.
 * User: vladp
 * Date: 10.10.2017
 * Time: 14:09
 */


$obj = $_GET['obj'];
$id = $_GET['id'];
$col = $_GET['col'];

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

switch ($obj){

    case "tovar":

        switch ($col){

            case "name":
                ?>

                <form class="form" action="app/post_handler.php" method="post">
                    <label for="red">Новое значение:</label>
                    <input type="text" name="name" id="red" required>
                    <input type="hidden" name="col" value="name">
                    <input type="hidden" name="id" value="<?php echo $id;?>">
                    <input type="hidden" name="obj" value="<?php echo $obj;?>">
                    <input type="hidden" name="method" value="edit">

                    <button class="btn btn-lg btn-primary btn-block" type="submit">Изменить</button>
                </form>

                <?php
                break;

            case "desc":
                ?>

                <form class="form" action="app/post_handler.php" method="post">
                    <label for="red">Новое значение:</label>
                    <input type="text" name="desc" id="red" required>
                    <input type="hidden" name="col" value="desc">
                    <input type="hidden" name="id" value="<?php echo $id;?>">
                    <input type="hidden" name="obj" value="<?php echo $obj;?>">
                    <input type="hidden" name="method" value="edit">

                    <button class="btn btn-lg btn-primary btn-block" type="submit">Изменить</button>
                </form>

                <?php
                break;

            case "img":
                ?>

                <form class="form" action="app/post_handler.php" method="post">
                    <label for="red">Новое значение:</label>
                    <input type="text" name="img" id="red" required>
                    <input type="hidden" name="col" value="img">
                    <input type="hidden" name="id" value="<?php echo $id;?>">
                    <input type="hidden" name="obj" value="<?php echo $obj;?>">
                    <input type="hidden" name="method" value="edit">

                    <button class="btn btn-lg btn-primary btn-block" type="submit">Изменить</button>
                </form>

                <?php
                break;

            case "price":
                ?>

                <form class="form" action="app/post_handler.php" method="post">
                    <label for="red">Новое значение:</label>
                    <input type="text" name="price" id="red" required>
                    <input type="hidden" name="col" value="price">
                    <input type="hidden" name="id" value="<?php echo $id;?>">
                    <input type="hidden" name="obj" value="<?php echo $obj;?>">
                    <input type="hidden" name="method" value="edit">

                    <button class="btn btn-lg btn-primary btn-block" type="submit">Изменить</button>
                </form>

                <?php
                break;

            case "present":
                ?>

                <form class="form" action="app/post_handler.php" method="post">
                    <label for="red">Новое значение:</label>
                    <select class="form-control" id="red" name="present" required>
                        <option value="1">есть</option>
                        <option value="0">нет</option>
                    </select>
                    <input type="hidden" name="col" value="present">
                    <input type="hidden" name="id" value="<?php echo $id;?>">
                    <input type="hidden" name="obj" value="<?php echo $obj;?>">
                    <input type="hidden" name="method" value="edit">

                    <button class="btn btn-lg btn-primary btn-block" type="submit">Изменить</button>
                </form>

                <?php
                break;
        }

        break;

    case "category":
        break;

}
?>
            </div>
