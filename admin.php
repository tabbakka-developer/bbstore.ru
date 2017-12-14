<?php
/**
 * Created by PhpStorm.
 * User: Влад
 * Date: 31.10.2017
 * Time: 11:41
 */

namespace bb_store;


class admin extends database {

    public function __construct(){
        parent::__construct();
    }

    public function AddNewAdmin($telegram){
        $queryString =
            "INSERT INTO `admins` (`telegram`) VALUES (:tg)";

        $params = array(
            "tg" => $telegram
        );

        $pdoQuery = $this->DB->prepare($queryString);
        $pdoQuery->execute($params);
        if($pdoQuery){
            return $this->returnTrue(array(
                "id" => $this->DB->lastInsertId()
            ));
        }
        else {
            return $this->returnFalse($pdoQuery->errorInfo());
        }
    }

    public function DeleteFromAdmins($telegram){
        $queryString = "DELETE FROM `admins` WHERE `telegram` = '{$telegram}'";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){
//            $pdoData = $pdoResult->fetchAll(\PDO::FETCH_ASSOC);
            return $this->returnTrue(null);
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    public function GetAdmin($telegram){
        $queryString = "SELECT * FROM `admins` WHERE `telegram` = '{$telegram}'";
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

    public function GetFirstName($telegram){
        $queryString = "SELECT `first_name` FROM `admins` WHERE `telegram` = '{$telegram}'";
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

    public function SetFirstName($telegram, $first_name){
        $queryString = "UPDATE `admins` SET `first_name` = '{$first_name}' WHERE `telegram` = '{$telegram}'";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){
            return $this->returnTrue(null);
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    public function GetLastName($telegram){
        $queryString = "SELECT `last_name` FROM `admins` WHERE `telegram` = '{$telegram}'";
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

    public function SetLastName($telegram, $last_name){
        $queryString = "UPDATE `admins` SET `last_name` = '{$last_name}' WHERE `telegram` = '{$telegram}'";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){
            return $this->returnTrue(null);
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    /**
     * @param $telegram integer
     * @return array
     */
    public function GetUserName($telegram){
        $queryString = "SELECT `username` FROM `admins` WHERE `telegram` = '{$telegram}'";
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

    /**
     * @param $telegram integer
     * @param $username string
     * @return array
     */
    public function SetUserName($telegram, $username){
        $queryString = "UPDATE `admins` SET `username` = '{$username}' WHERE `telegram` = '{$telegram}'";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){
            return $this->returnTrue(null);
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }
    /**
     * @return array
     */
    public function GetAllAdmins(){
        $queryString = "SELECT * FROM `admins`";
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

    /**
     * @param $telegram integer
     * @return array
     * @deprecated
     */
    public function GetLastAction($telegram){
        $queryString = "SELECT `action` FROM `admins` WHERE `telegram` = '{$telegram}'";
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

    /**
     * @param $action string
     * @param $telegram integer
     * @return array
     * @deprecated
     */
    public function SetLastAction($action, $telegram){
        $queryString = "UPDATE `admins` SET `action` = '{$action}' WHERE `telegram` = '{$telegram}'";
        $pdoQuery = $this->DB->prepare($queryString);
        $pdoResult = $this->DB->query($pdoQuery->queryString);
        if($pdoResult){
            return $this->returnTrue(null);
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    /**
     * @param $media_group_id integer Media Group ID from Telegram Message
     * @param $telegram integer Uniq ID from Telegram
     * @return array
     */
    public function SetMediaGroupID($media_group_id, $telegram){
        $queryString = "UPDATE `admins` SET `media_group_id` = :media WHERE `telegram` = :telegram";
        $pdoQuery = $this->DB->prepare($queryString);
//        $pdoResult = $this->DB->query($pdoQuery->queryString);

        $params = array(
            "media" => $media_group_id,
            "telegram" => $telegram
        );
        $pdoResult = $pdoQuery->execute($params);

        if($pdoResult){
            return $this->returnTrue(array("query" => $pdoQuery->queryString));
        }
        else {
            return $this->returnFalse($pdoResult->errorInfo());
        }
    }

    /**
     * @param $telegram integer Uniq ID from Telegram
     * @return array
     */
    public function GetMediaGroupID($telegram){
        $queryString = "SELECT `media_group_id` FROM `admins` WHERE `telegram` = '{$telegram}'";
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

}