<?php
/**
 * Created by PhpStorm.
 * User: vladp
 * Date: 10.10.2017
 * Time: 12:57
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

?>


<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Главная</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

</head>

<?php

$categories = $db->GetCategories();
if($categories['result'] == true) {

    $cnt = count($categories['data']);
    ?>
    <body>
    <div class="container">

        <br>
        <br>
        <div class="col-md-12">
            Полученно <?php echo $cnt; ?> категорий!
        </div>
        <br>
        <a href="upload_document.php" class="btn btn-lg btn-block">Загрузить таблицу</a>
        <hr>
        <a href="create_category.php" class="btn btn-lg btn-block">Создать категорию</a>
        <hr>
        <a href="category.php" class="btn btn-lg btn-block">Список всех товаров</a>
        <?php

        foreach ($categories['data'] as $category){
            echo
                "<div class='col-md-3'>".
                "Название: ".$category['name']."<br>".
                "<a href='category.php?id=".$category['id']."'>Открыть</a>"."<br>".
                "</div>";
            echo "<hr>";
        }

        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
            integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"
            integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1"
            crossorigin="anonymous"></script>
    </body>

    <?php

}
else {

    ?>

    <body>
        <div class="container">
            <h3>Ошибка получения данных!</h3>
            <?php

                print_r($categories['error']);

            ?>
        </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
            integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"
            integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1"
            crossorigin="anonymous"></script>
    </body>

<?php

}

?>
</html>
