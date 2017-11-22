<?php
/**
 * Created by PhpStorm.
 * User: Влад
 * Date: 05.11.2017
 * Time: 14:11
 */


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

    <form class="form" method="post" enctype="multipart/form-data" action="app/post_handler.php">
        <label for="input_file">Файл таблицы:</label>
        <input type="file" id="input_file" name="database_file">
        <br>
        <hr>
        <label for="db_add">Добавить</label>
        <input type="radio" name="database_insert_type" id="db_add" value="Add" checked>
        <br>
        <hr>
        <label for="db_ref">Обновить</label>
        <input type="radio" name="database_insert_type" id="db_ref" value="Refresh">
        <br>
        <input type="hidden" name="method" value="upload_document">
        <input type="submit" class="btn btn-lg btn-primary btn-block" value="Загрузить">
    </form>
</div>
</body>
</html>



