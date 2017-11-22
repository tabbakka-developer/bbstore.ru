<?php
/**
 * Created by PhpStorm.
 * User: vladp
 * Date: 10.10.2017
 * Time: 13:52
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

if(isset($_GET['id'])){
    $tovar = $db->GetLotById($_GET['id']);
    if($tovar['result'] == true){
        $name = $tovar['data'][0]['name'];
        $img = $tovar['data'][0]['image_url'];
        $description = $tovar['data'][0]['description'];
        $price = $tovar['data'][0]['price'];
        $number = $tovar['data'][0]['number'];
        $present = $tovar['data'][0]['present'];

        $cat_id = $tovar['data'][0]['category_id'];
//        $id = $tovar['id'];


        $pr_str = "";
        if($present){
            $pr_str = "есть";
        }
        else {
            $pr_str = "нет";
        }

        ?>

        <html>
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <title><?php echo $name;?></title>
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

            </head>
            <body>

            <div class="container">
                <br>
                <h3>Товар #<?php echo $number;?></h3>
                <br>
                <?php
                    echo
                        "Название: ".$name." <- <a href='change.php?obj=tovar&id=".$_GET['id']."&col=name'>изменить</a><br>".
                        "Описание: ".$description." <- <a href='change.php?obj=tovar&id=".$_GET['id']."&col=desc'>изменить</a><br>".
                        "Фото:  <- <a href='change.php?obj=tovar&id=".$_GET['id']."&col=img'>изменить</a><br>".
                        "Цена: ".$price." <- <a href='change.php?obj=tovar&id=".$_GET['id']."&col=price'>изменить</a><br>".
                        "Наличие: ".$pr_str." <- <a href='change.php?obj=tovar&id=".$_GET['id']."&col=present'>изменить</a><br>";



                    echo "<br><hr><hr><br>";


                    //todo: check for additional photos

                    $photos = $db->GetAllLotPhoto($_GET['id']);
                    if($photos['result'] === true){
                        $photoData = $photos['data'];
                        foreach ($photoData as $ph){
                            $id = $ph['id'];
                            $url = $ph['url'];

                            $www = urldecode("tovar.php?id=".$_GET['id']);

                            echo "<div>";
                            echo "<img src='".urldecode($url)."' width='300px'/><br>";
                            echo "<a href='delete.php?obj=photo&id=".$id."&url=".$www."'>Удалить фото</a>";
                            echo "</div>";

                        }
                    }
                    else {
                        echo "<p>! Нет дополнительных фото !</p>";
                    }

                    echo "<br><hr><hr><br>";

                    echo
                        "<form method='post' enctype=\"multipart/form-data\" action='app/post_handler.php'><input class='form-control' placeholder='Прикрепить фото' type='file' name='image'>".
                        "<input type='hidden' name='method' value='upload_photo_img'>".
                        "<input type='hidden' name='lot_id' value='".$_GET['id']."'>".
                        "<button class=\"btn btn-lg btn-accept btn-block\" type=\"submit\">Добавить фото</button>".
                        "</form>";

                    echo
                        "<br>";

                    echo
                        "<form method='post' action='app/post_handler.php'><input class='form-control' placeholder='Ссылка на фото' type='url' name='url'>".
                        "<input type='hidden' name='method' value='upload_photo'>".
                        "<input type='hidden' name='lot_id' value='".$_GET['id']."'>".
                        "<button class=\"btn btn-lg btn-accept btn-block\" type=\"submit\">Добавить фото</button>".
                        "</form>";
                    //todo: --end--

                    echo "<br><hr><hr><br>";

                    echo "<a href='delete.php?obj=tovar&id=".$_GET['id']."'>Удалить товар</a><br>";
                    echo "<a href='category.php?id=".$cat_id."'>Назад</a>";
                ?>
            </div>

            </body>
        </html>

        <?php
    }
    else {
        //
    }
}