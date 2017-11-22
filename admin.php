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

}