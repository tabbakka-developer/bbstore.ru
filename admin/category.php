<?php
/**
 * Created by PhpStorm.
 * User: vladp
 * Date: 10.10.2017
 * Time: 13:26
 */

//session_start();
//
//if(!isset($_SESSION['token'])){
//    header("Location: index.php");
//}
//else {
//    session_destroy();
//}

require_once "../database.php";

use bb_store\database;

$db = new database();

$cat_name = null;

if(isset($_GET['id'])){
    $category = $db->GetCategoryById($_GET['id']);
    $lots = $db->GetLotsFromCategory($_GET['id'], true);

    if($category['result'] == true){
        $cat_name = $category['data'][0]['name'];

        ?>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <title><?php echo $cat_name;?></title>
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

        </head>
        <body>
<?php


        if($lots['result'] == true){

            echo "<br><br><a href='create.php?cat_id=".$_GET['id']."' class=\"btn btn-lg btn-block\">Новый лот</a>";
            echo "<br><br><a href='main.php' class=\"btn btn-lg btn-block\">Назад</a>";
            echo "<br><br><a href='delete.php?id=".$_GET['id']."&obj=category' class=\"btn btn-lg btn-block btn-danger\">Удалить категорию</a>";

            echo "<hr><hr>";
            foreach ($lots['data'] as $tovar){
                $img = $tovar['image_url'];
                $name = $tovar['name'];
                $description = $tovar['description'];
                $price = $tovar['price'];
                $number = $tovar['id'];
                $present = $tovar['present'];
                $id = $tovar['id'];

                $pr_str = "";
                if($present){
                    $pr_str = "есть";
                }
                else {
                    $pr_str = "нет";
                }
/*
 * "Цена: ".$price." рублей"." <- <a href='change.php?obj=tovar&id=".$id."&col=price'>изменить</a><br>".
 * "Наличие: ".$pr_str." <- <a href='change.php?obj=tovar&id=".$id."&col=present'>изменить</a><br>".
 */
                echo
                    "<div class='col-md-3'>".
                    "Товар #".$number."<br>".
                    "Название: ".$name."<br>".
                    "Описание: ".$description."<br>".

                    "<form action='app/post_handler.php' method='post'><label for='input_price'>Цена: </label><input class='form-control' id='input_price' name='price' type='number' placeholder='".$price."'>".
                    "<input type='hidden' name='id' value='".$id."'>".
                    "<input type='hidden' name='method' value='edit'>".
                    "<input type='hidden' name='obj' value='tovar'>".
                    "<input type='hidden' name='col' value='price'>".
                    "<br><button class='btn btn-lg btn-success btn-block' type='submit'>Изменить</button></form><br>".

                    "<form action='app/post_handler.php' method='post'><label for='input_present'>Наличие: </label><select class='form-control' id='input_price' name='present'>";
                if($pr_str === "есть"){
                    echo
                        "<option selected>".$pr_str."</option>".
                        "<option value='0'>нет</option>".
                        "</select>".
                        "<input type='hidden' name='id' value='".$id."'>".
                        "<input type='hidden' name='method' value='edit'>".
                        "<input type='hidden' name='obj' value='tovar'>".
                        "<input type='hidden' name='col' value='present'>".
                        "<br><button class='btn btn-lg btn-success btn-block' type='submit'>Изменить</button></form><br>".

                        "<img src='".urldecode($img)."' width='300px'><br>".
                        "<a href='tovar.php?id=".$id."'>Открыть</a>"."<br>".
                        "</div>";
                }
                else {
                    echo
                        "<option selected>".$pr_str."</option>".
                        "<option value='1'>есть</option>".
                        "</select>".
                        "<input type='hidden' name='id' value='".$id."'>".
                        "<input type='hidden' name='method' value='edit'>".
                        "<input type='hidden' name='obj' value='tovar'>".
                        "<input type='hidden' name='col' value='present'>".
                        "<br><button class='btn btn-lg btn-success btn-block' type='submit'>Изменить</button></form><br>".

                        "<img src='".urldecode($img)."' width='300px'><br>".
                        "<a href='tovar.php?id=".$id."'>Открыть</a>"."<br>".
                        "</div>";
                }

                echo "<hr>";
            }
        }
    }
    else {
        header("Location main.php");
    }

}
else {
//    header("Location main.php");
    //load all stuff
    ?>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <title>Все товары</title>
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

        </head>
        <body>
<?php

    $lots = $db->GetAllLots(true);
    if($lots['result'] == true){

//        echo "<br><br><a href='create.php?cat_id=".$_GET['id']."' class=\"btn btn-lg btn-block\">Новый лот</a>";
        echo "<br><br><a href='main.php' class=\"btn btn-lg btn-block\">Назад</a>";
//        echo "<br><br><a href='delete.php?id=".$_GET['id']."&obj=category' class=\"btn btn-lg btn-block btn-danger\">Удалить категорию</a>";

        echo "<hr><hr>";
        foreach ($lots['data'] as $tovar){
            $img = $tovar['image_url'];
            $name = $tovar['name'];
            $description = $tovar['description'];
            $price = $tovar['price'];
            $number = $tovar['id'];
            $present = $tovar['present'];
            $id = $tovar['id'];

            $pr_str = "";
            if($present){
                $pr_str = "есть";
            }
            else {
                $pr_str = "нет";
            }

            echo
                "<div class='col-md-3'>".
                "Товар #".$number."<br>".
                "Название: ".$name."<br>".
                "Описание: ".$description."<br>".
                "Цена: ".$price." рублей"." <- <a href='change.php?obj=tovar&id=".$id."&col=price'>изменить</a><br>".
                "Наличие: ".$pr_str." <- <a href='change.php?obj=tovar&id=".$id."&col=present'>изменить</a><br>".
                "<img src='".urldecode($img)."' width='300px'><br>".
                "<a href='tovar.php?id=".$id."'>Открыть</a>"."<br>".
                "</div>";
            echo "<hr>";
        }
    }
}

?>

        </body>
        </html>


