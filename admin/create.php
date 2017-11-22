<?php
/**
 * Created by PhpStorm.
 * User: vladp
 * Date: 10.10.2017
 * Time: 15:00
 */




$id = $_GET['cat_id'];


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

        <form action="app/post_handler.php" enctype="multipart/form-data" method="post" class="form">
            <label for="name">Название</label>
            <input type="text" name="name" required id="name">
            <label for="desc">Описание</label>
            <input type="text" name="description" required id="desc">
            <label for="price">Цена</label>
            <input type="number" name="price" required id="price">

            <label for="img">Ссылка на фото</label>
            <input type="url" name="image_url" id="img">

            <label for="img_upload">Либо прикрепить фото</label>
            <input type="file" name="image_upload" id="img_upload">

            <label for="present">Наличие (1 - есть, 0 - нет)</label>
            <select class="form-control" name="present" id="present" required>
                <option value="1">есть</option>
                <option value="0">нет</option>
            </select>
<!--            <label for="number">Номер товара:</label>-->
<!--            <input type="number" name="number" required id="number">-->

            <input type="hidden" name="category_id" value="<?php echo $id;?>">
            <input type="hidden" name="method" value="new">
            <button class="btn btn-lg btn-accept btn-block" type="submit">Создать</button>
        </form>

    </div>
    <a href="https://bbstore.ru/admin/category.php?id=<?php echo $id;?>" class="btn btn-lg btn-block">Назад</a>
    </body>
    </html>

<?php



?>