<?php
/**
 * Created by PhpStorm.
 * User: Влад
 * Date: 27.10.2017
 * Time: 13:13
 */

namespace bb_store;



class tmp_lot extends database {


    function __construct(){
        parent::__construct();
    }

    public function CheckIsAlreadyExist($user_telegram){
        $queryString = "SELECT * FROM `tmp_lots` WHERE `user_tlg` = '{$user_telegram}'";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){
            $pdoData = $pdoResult->fetchAll(\PDO::FETCH_ASSOC);
            return $this->returnTrue($pdoData);
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    public function DeleteTmp($user_telegram){
        $queryString = "DELETE FROM `tmp_lots` WHERE `user_tlg` = '{$user_telegram}'";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){
            return $this->returnTrue(null);
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    public function UploadAdditionalPhoto_tmp($photo_url, $telegram){
        $queryString = "SELECT `id` FROM `tmp_lots` WHERE `user_tlg` = '{$telegram}'";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult) {
            $pdoData = $pdoResult->fetchAll(\PDO::FETCH_ASSOC);
            $t_lot_id = $pdoData[0]['id'];

            //updating...
            $queryString2 = "INSERT INTO `tmp_images` (`url`, `tmp_lot_id`) VALUES ('{$photo_url}', '{$t_lot_id}')";
            $pdoQuery2 = $this->DB->prepare($queryString2);
            $pdoResult2 = $this->DB->query($pdoQuery2->queryString);
            if($pdoResult2){
                return $this->returnTrue(array(
                    "id_img" => $this->DB->lastInsertId(),
                    "t_lot_id" => $t_lot_id
                ));
            }
            else {
                return $this->returnFalse($pdoResult2->errorInfo());
            }
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    public function CreateTmpLot($user_telegram){
        $queryString = "INSERT INTO `tmp_lots` (`user_tlg`) VALUES ('{$user_telegram}')";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){
            return $this->returnTrue(array(
                "id" => $this->DB->lastInsertId()
            ));
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    public function EditTmpLot($user_telegram, $col, $value){
        $queryString = "UPDATE `tmp_lots` SET ";
        switch ($col){

            case "name":
                $queryString .= "`name` = :nv WHERE `user_tlg` = :ut";
                break;
//            case "number":
//                $queryString .= "`number` = :nv WHERE `user_tlg` = :ut";
//                break;
            case "description":
                $queryString .= "`description` = :nv WHERE `user_tlg` = :ut";
                break;
            case "image":
                $queryString .= "`image_url` = :nv WHERE `user_tlg` = :ut";
                break;
            case "price":
                $queryString .= "`price` = :nv WHERE `user_tlg` = :ut";
                break;
            case "present":
                $queryString .= "`present` = :nv WHERE `user_tlg` = :ut";
                break;

            case "category":
                $queryString .= "`category_id` = :nv WHERE `user_tlg` = :ut";
                break;
        }

//        echo $queryString;
        $pdoQuery = $this->DB->prepare($queryString);
        $params = array(
            "nv" => $value,
            "ut" => $user_telegram
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

    private function CheckForAdditionalPhotos_tmp($lot_id_tmp, $lot_id){
        $queryString = "SELECT * FROM `tmp_images` WHERE `tmp_lot_id` = '{$lot_id_tmp}'";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){
            $pdoData = $pdoResult->fetchAll(\PDO::FETCH_ASSOC);
            if(count($pdoData) > 0){
                $flag = true;
                $error = null;
                foreach ($pdoData as $data){
                    $url = $data['url'];
                    $queryString2 = "INSERT INTO `images` (`url`, `lot_id`) VALUES ('{$url}', '{$lot_id}')";
                    $pdoQuery2 = $this->DB->prepare($queryString2);
                    $pdoResult2 = $this->DB->query($pdoQuery2->queryString);
                    if(!$pdoResult2){
                        $flag = false;
                        $error = $pdoResult2->errorInfo();
                    }
                }

                if($flag){
                    return $this->returnTrue(array(
                        "count" => count($pdoData)
                    ));
                }
                else {
                    return $this->returnFalse($error);
                }
            }
            else {
                $this->returnTrue(null);
            }
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    private function ClearAdditionalPhotos_tmp($lot_id_tmp){
        $queryString = "DELETE FROM `tmp_images` WHERE `tmp_lot_id` = '{$lot_id_tmp}'";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){
            return $this->returnTrue(null);
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    public function Transfer($user_tlg){
        $queryString = "SELECT * FROM `tmp_lots` WHERE `user_tlg` = '{$user_tlg}'";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){
            $pdoData = $pdoResult->fetchAll(\PDO::FETCH_ASSOC);
            $lot_id = $pdoData[0]['id'];
            $ret = $this->CreateLot_up($pdoData[0], urldecode($pdoData[0]['image_url']) ,$pdoData[0]['category_id']);
            $ch_ph = $this->CheckForAdditionalPhotos_tmp($lot_id, $ret['data']['id']);
            $cl_ph = $this->ClearAdditionalPhotos_tmp($lot_id);

            $queryString2 = "DELETE FROM `tmp_lots` WHERE `id` = '{$lot_id}'";
            $pdoQuery2 = $this->DB->prepare($queryString2);
            $pdoResult2 = $this->DB->query($pdoQuery2->queryString);
            if($pdoResult2){
                return array(
                    "create_lot" => $ret,
                    "check_photo_and_transfer" => $ch_ph,
                    "clear_photo" => $cl_ph
                );
            }
            return null;

        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

}