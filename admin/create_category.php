<?php
/**
 * Created by PhpStorm.
 * User: Влад
 * Date: 19.10.2017
 * Time: 18:36
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

    <form action="app/post_handler.php" method="post" class="form">
        <label for="name">Название</label>
        <input type="text" name="name" required id="name">
        <input type="hidden" name="method" value="cat_cr">
        <button class="btn btn-lg btn-accept btn-block" type="submit">Создать</button>
    </form>

</div>
<a href="https://bbstore.ru/admin/main.php" class="btn btn-lg btn-block">Назад</a>
</body>
</html>