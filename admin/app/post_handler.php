<?php
/**
 * Created by PhpStorm.
 * User: vladp
 * Date: 10.10.2017
 * Time: 13:10
 */

require_once "../../database.php";

use bb_store\database;

//print_r($_POST);


if(isset($_POST['method'])){

    switch (true){

        case $_POST['method'] === "auth":

            $login = $_POST['login'];
            $pwd = $_POST['password'];

            if(($login == "admin")&&($pwd == "Bbstore")){
                session_start();
                $_SESSION['token'] = "123qwe123asd123zxc";
                header("Location: ../main.php");
            }

            break;

        case $_POST['method'] === "edit":
            if($_POST['obj'] == "tovar"){

                $db = new database();

                $col = $_POST['col'];
                $new_val = $_POST[$col];
                $id = $_POST['id'];

                if($col == "img"){
                    $new_val = urlencode($new_val);
                }

//                print_r($_POST);

                $response = $db->EditLot($col, $new_val, $id);
                if($response['result'] == true){
//                    sleep(1);
                    header("Location: ../tovar.php?id=$id");
                }
                else {
                    echo "Ошибка!<br>";
                    print_r($response['error']);
                }
//                print_r($response);
            }
            else {

            }
            break;

        case $_POST['method'] === "delete":
//            print_r($_POST);
            if($_POST['obj'] == "tovar"){
                $db = new database();
                $id = $_POST['id'];
                $response = $db->DeleteLot($id);
                if($response['result'] == true){
//                    sleep(1);
                    header("Location: ../main.php");
                }
                else {
                    echo "Ошибка!<br>";
                    print_r($response['error']);
                }
            }
            elseif($_POST['obj'] == "category"){
                $db = new database();
                $id = $_POST['id'];
                $response = $db->DeleteCategory($id);
                if($response['result'] == true){
//                    sleep(1);
                    header("Location: ../main.php");
                }
                else {
                    echo "Ошибка!<br>";
                    print_r($response['error']);
                }
            }
            elseif ($_POST['obj'] === "photo"){
                $db = new database();
                $id = $_POST['id'];
                $url = $_POST['url'];
                $response = $db->DeleteAdditionalPhoto($id);
                if($response['result'] == true){
//                    sleep(1);
                    header("Location: ../".urldecode($url));
                }
                else {
                    echo "Ошибка!<br>";
                    print_r($response['error']);
                }
            }
            break;

        case $_POST['method'] === "new":
            $db = new database();
            $response = [];
            if(isset($_FILES['image_upload'])){
                $file = $_FILES['image_upload'];
                $uploaddir = '/var/www/bbstore.ru/html/images/';
                $uploadfile = $uploaddir . basename($file['name']);
                if (move_uploaded_file($file['tmp_name'], $uploadfile)) {
                    $sql_file = "https://bbstore.ru/images/" . basename($file['name']);
                    $db = new database();
                    $response = $db->CreateLot_up($_POST, $sql_file, $_POST['category_id']);
                }
                else {
                    echo "Не удалось сохранить файл. Ошибка доступа!<br><br>";
                }
            }
            else{
                $response = $db->CreateLot($_POST, $_POST['category_id']);
            }
            if($response['result'] == true){
                $lot_id = $response['data']['id'];
                header("Location: ../tovar.php?id=$lot_id");
            }
            else {
                echo "Ошибка!<br>";
                print_r($response['error']);
            }
            break;

        case $_POST['method'] === "cat_cr":
            $db = new database();
            $response = $db->CreateCategory($_POST['name']);
            if($response['result'] == true){
                $cat_id = $response['data']['id'];
//                print_r($response);
                header("Location: ../category.php?id=$cat_id");
            }
            else {
                echo "Ошибка!<br>";
                print_r($response['error']);
            }
            break;

        case $_POST['method'] === "upload_photo":
            $db = new database();
            $url = $_POST['url'];
            $lot_id = $_POST['lot_id'];
            $response = $db->UploadAdditionalPhoto($url, $lot_id);
            if($response['result'] == true){
//                print_r($_POST);
//                print_r($response);
//                $cat_id = $response['data']['id'];
//                print_r($response);
                header("Location: ../tovar.php?id=$lot_id");
            }
            else {
                echo "Ошибка!<br>";
                print_r($response['error']);
            }
            break;

        case $_POST['method'] === "upload_photo_img":
            $file = $_FILES['image'];
            $uploaddir = '/var/www/bbstore.ru/html/images/';
            $uploadfile = $uploaddir . basename($file['name']);

//            print_r($_POST);
//            echo "<br><br>";
//            print_r($_FILES);
//            echo "<br><br>";

            if (move_uploaded_file($file['tmp_name'], $uploadfile)) {
//                echo "file transfer OK <br><br>";
                $sql_file = "https://bbstore.ru/images/" . basename($file['name']);
//                echo $sql_file."<br><br>";
                $db = new database();
                $lot_id = $_POST['lot_id'];
                $response = $db->UploadAdditionalPhoto($sql_file, $lot_id);
//                print_r($response);
                if($response['result'] == true){
                    header("Location: ../tovar.php?id=$lot_id");
                }
                else {
                    echo "Ошибка!<br>";
                    print_r($response['error']);
                }
                break;
            }
            else {
                echo "Не удалось сохранить файл. Ошибка доступа!<br><br>";
            }
            break;

        case $_POST['method'] === "upload_document":
            print_r($_POST);
            echo "<br>";
            if(isset($_FILES['database_file'])){
                echo "<br>true<br>";
                print_r($_FILES);

                if($_FILES['error'] == 0){
                    //save file and run Python script
                    $uploaddir = '/var/www/bbstore.ru/html/spreadsheets/';
//                    $uploadfile = $uploaddir . basename($_FILES['database_file']['name']);
                    $data_type = explode(".", $_FILES['database_file']['name'])[1];
                    $uploadfile = $uploaddir . "tmp." . $data_type;
                    if(move_uploaded_file($_FILES['database_file']['tmp_name'], $uploadfile)){
                        echo "<br>Файл успешно сохранен!<br>Начинаем запуск Python обработчика....";
                        if($_POST['database_insert_type'] === "Add"){
                            exec("python3.6 /var/www/bbstore.ru/html/data_worker/main.py a");
                        }
                        else {
                            exec("python3.6 /var/www/bbstore.ru/html/data_worker/main.py c");
                        }
                    }
                    else {
                        echo "<br>Ошибка сохранения файла......<br>";
                    }
                }
                else {
                    echo "<br>";
                    echo $_FILES['error'];
                    echo "<br>";
                }
            }
            else {
                echo "<br>Ошибка загрузки файла.....<br>";
            }
            break;
    }

}