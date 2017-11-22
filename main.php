<?php
/**
 * Created by PhpStorm.
 * User: vladp
 * Date: 09.10.2017
 * Time: 0:52
 */
require_once "bot.php";

use bb_store\bot;

$json = file_get_contents('php://input');
$token = "496323376:AAG8ZWlXI01v1pai_IPYmYnSfVAdm6Y78nc";

$bot = new bot($token);
$bot->ParseQuery($json);

$bot->SendMediaGroup();
