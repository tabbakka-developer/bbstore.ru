<?php
/**
 * Created by PhpStorm.
 * User: vladp
 * Date: 09.10.2017
 * Time: 0:53
 */

namespace bb_store;


use PDO;

class database
{
    protected $dbn = 'bbstore_db0';
    protected $dbh = 'localhost';
    protected $dbr = '3306';
    protected $dbu = 'bbstore_db0';
    protected $dbp = 'Y9C4GZnWEcBDMzr';
    protected $DB;

    function __construct()
    {
        $this->DB = new PDO('mysql:host=' . $this->dbh . ';port=' . $this->dbr . ';dbname=' . $this->dbn, $this->dbu, $this->dbp);
        $this->DB->exec('SET CHARACTER SET utf8');
        $this->DB->exec('SET NAMES utf8mb4');
    }

    public function GetCategoryById($id){
        $queryString = "SELECT * FROM `categories` WHERE `id` = '{$id}'";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){
            $pdoData = $pdoResult->fetchAll(PDO::FETCH_ASSOC);
            return $this->returnTrue($pdoData);
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    public function GetAllLotPhoto($lot_id){
        $queryString = "SELECT * FROM `images` WHERE `lot_id` = '{$lot_id}'";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){
            $pdoData = $pdoResult->fetchAll(PDO::FETCH_ASSOC);
            if(count($pdoData) > 0){
                return $this->returnTrue($pdoData);
            }
            else {
                return $this->returnFalse("no more photo");
            }
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }

    }

    public function GetCategories(){
        $queryString = "SELECT * FROM `categories`";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){
            $pdoData = $pdoResult->fetchAll(PDO::FETCH_ASSOC);
            return $this->returnTrue($pdoData);
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    public function CreateCategory($category_name){
        $queryString = "INSERT INTO `categories` (`name`) VALUES (:c_name)";
        $params = array(
            "c_name" => $category_name
        );
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoQuery->execute($params);
        if($pdoQuery){
//            print_r($params);
//            echo "<br>ID:".$this->DB->lastInsertId()."<br>";
//            var_dump($pdoQuery);
            return $this->returnTrue(array(
                "id" => $this->DB->lastInsertId()
            ));
        }
        else {
            return $this->returnFalse($pdoQuery->errorInfo());
        }
    }

    public function DeleteCategory($category_id){
        $queryString = "DELETE FROM `categories` WHERE `id` = '{$category_id}'";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){

            $queryString2 = "DELETE FROM `lots` WHERE `category_id` = '{$category_id}'";
            $pdoQuery2 = $this->DB->prepare($queryString2);
            $pdoResult2 = $this->DB->query($pdoQuery2->queryString);
            if($pdoResult2){
                return $this->returnTrue(null);
            }
            else {
                return $this->returnFalse($pdoResult2->errorInfo());
            }
//            $pdoData = $pdoResult->fetchAll(PDO::FETCH_ASSOC);

        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    public function DeleteAdditionalPhoto($photo_id){
        $queryString = "DELETE FROM `images` WHERE `id` = '{$photo_id}'";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
//        var_dump($pdoResult);
        if($pdoResult){
//            $pdoData = $pdoResult->fetchAll(PDO::FETCH_ASSOC);
            return $this->returnTrue(null);
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    public function UploadAdditionalPhoto($url, $lot_id){
        $url = urlencode($url);
        $queryString = "INSERT INTO `images` (`url`, `lot_id`) VALUES (:url, :lot)";
        $params = array(
            "url" => $url,
            "lot" => $lot_id
        );
//        print_r($params);
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoQuery->execute($params);
        if($pdoQuery){
//            print_r($params);
//            echo "<br>ID:".$this->DB->lastInsertId()."<br>";
//            var_dump($pdoQuery);
            return $this->returnTrue(array(
                "id" => $this->DB->lastInsertId()
            ));
        }
        else {
            return $this->returnFalse($pdoQuery->errorInfo());
        }
    }

    public function GetCategories_notEmpty(){
        $queryString = "SELECT DISTINCT `categories`.`id`, `categories`.`name` FROM `categories` INNER JOIN `lots` ON `categories`.`id` = `lots`.`category_id` WHERE `lots`.`present` = 1";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){
            $pdoData = $pdoResult->fetchAll(PDO::FETCH_ASSOC);
            return $this->returnTrue($pdoData);
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    public function DeleteLot($lot_id){
        echo $lot_id;
        $queryString = "DELETE FROM `lots` WHERE `id` = '{$lot_id}'";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
//        var_dump($pdoResult);
        if($pdoResult){
//            $pdoData = $pdoResult->fetchAll(PDO::FETCH_ASSOC);
            return $this->returnTrue(null);
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    public function CreateLot_up($lot_info, $image_url, $category_id){
//        print_r($lot_info);
        $queryString =
            "INSERT INTO `lots` (`name`, `description`, `image_url`, `present`, `price`, `category_id`)".
            "VALUES (:nm, :des, :img, :pres, :price, :cat_id)";

        $params = array(
            "nm" => $lot_info['name'],
            "des" => $lot_info['description'],
            "img" => urlencode($image_url),
            "pres" => $lot_info['present'],
            "price" => $lot_info['price'],
            "cat_id" => $category_id
        );

        $pdoQuery = $this->DB->prepare($queryString);
        $pdoQuery->execute($params);
        if($pdoQuery){
//            print_r($params);
//            echo "<br>ID:".$this->DB->lastInsertId()."<br>";
//            var_dump($pdoQuery);
            return $this->returnTrue(array(
                "id" => $this->DB->lastInsertId(),
                "error" => $pdoQuery->errorInfo(),
                "income_data" => $lot_info
            ));
        }
        else {
            return $this->returnFalse($pdoQuery->errorInfo());
        }
    }

    public function CreateLot($lot_info, $category_id){
//        print_r($lot_info);
    $queryString =
        "INSERT INTO `lots` (`name`, `description`, `image_url`, `present`, `price`, `category_id`)".
        "VALUES (:num, :nm, :des, :img, :pres, :price, :cat_id)";

    $params = array(
        "nm" => $lot_info['name'],
        "des" => $lot_info['description'],
        "img" => urlencode($lot_info['image_url']),
        "pres" => $lot_info['present'],
        "price" => $lot_info['price'],
        "cat_id" => $category_id
    );

    $pdoQuery = $this->DB->prepare($queryString);
    $pdoQuery->execute($params);
    if($pdoQuery){
//            print_r($params);
//            echo "<br>ID:".$this->DB->lastInsertId()."<br>";
//            var_dump($pdoQuery);
        return $this->returnTrue(array(
            "id" => $this->DB->lastInsertId()
        ));
    }
    else {
        return $this->returnFalse($pdoQuery->errorInfo());
    }
}

    public function EditLot($col, $new_val, $id){
        $queryString = "UPDATE `lots` SET ";
        switch ($col){

            case "name":
                $queryString .= "`name` = :nv WHERE `id` = :id";
                break;
            case "desc":
                $queryString .= "`description` = :nv WHERE `id` = :id";
                break;
            case "img":
                $queryString .= "`image_url` = :nv WHERE `id` = :id";
                break;
            case "price":
                $queryString .= "`price` = :nv WHERE `id` = :id";
                break;
            case "present":
                $queryString .= "`present` = :nv WHERE `id` = :id";
                break;
        }

//        echo $queryString;
        $pdoQuery = $this->DB->prepare($queryString);
        $params = array(
            "nv" => $new_val,
            "id" => $id
        );
        $pdoResult = $pdoQuery->execute($params);
        if($pdoResult){
//            $pdoData = $pdoResult->fetchAll(PDO::FETCH_ASSOC);
            return $this->returnTrue(null);
        }
        else{
            return $this->returnFalse($pdoQuery->errorInfo());
        }
    }

    public function GetLotById($lot_id){
        $queryString = "SELECT * FROM `lots` WHERE `id` = '{$lot_id}'";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){
            $pdoData = $pdoResult->fetchAll(PDO::FETCH_ASSOC);
            return $this->returnTrue($pdoData);
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    public function GetAllLots($all = false){
        $queryString = "";
        if($all){
            $queryString = "SELECT * FROM `lots` WHERE `category_id` <> '15'";
        }
        else{
            $queryString = "SELECT * FROM `lots` WHERE `present` = 1 AND `category_id` <> '15'";
        }
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){
            $pdoData = $pdoResult->fetchAll(PDO::FETCH_ASSOC);
            return $this->returnTrue($pdoData);
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    public function GetLotsFromCategory($cat_id, $all = false){
        $queryString = "";
        if($all){
            $queryString = "SELECT * FROM `lots` WHERE `category_id` = '{$cat_id}'";
        }
        else{
            $queryString = "SELECT * FROM `lots` WHERE `category_id` = '{$cat_id}' AND `present` = 1";
        }
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){
            $pdoData = $pdoResult->fetchAll(PDO::FETCH_ASSOC);
            return $this->returnTrue($pdoData);
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    protected function returnTrue($data){
        return array(
            "result" => true,
            "data" => $data,
            "error" => null
        );
    }

    protected function returnFalse($error){
        return array(
            "result" => false,
            "data" => null,
            "error" => $error
        );
    }

}

