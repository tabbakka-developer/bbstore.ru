<?php
/**
 * Created by PhpStorm.
 * User: vladp
 * Date: 09.10.2017
 * Time: 0:53
 */

namespace bb_store;

require_once "database.php";
require_once "tmp_lots.php";
require_once "admin.php";

class bot
{

    private $sendPhQTo;
    private $sendMsgQTo;
    private $dataBase;
    private $categories;
    private $adm_id_1 = "114202655";
    private $adm_id_2 = "335019483";
    private $developer_id = "219293907";

    private $getFile;
    private $telegram_file;

    private $sendMediaGroup;

    private $inputIsCat = false;

    private $user_telegram;

    function __construct($token)
    {
        $this->sendPhQTo = 'https://api.telegram.org/bot' . $token . '/sendPhoto?';
        $this->sendMsgQTo = 'https://api.telegram.org/bot' . $token . '/sendMessage?parse_mode=HTML';
        $this->getFile = 'https://api.telegram.org/bot' . $token . '/getFile?';
        $this->telegram_file = 'https://api.telegram.org/file/bot' . $token . "/";
        $this->dataBase = new database();
        //
        $this->sendMediaGroup = 'https://api.telegram.org/bot' . $token . '/sendMediaGroup?';

        $this->categories = $this->dataBase->GetCategories_notEmpty();
    }

    private function sendMediaGroup($photo_array, $caption)
    {
        $media = [];

        foreach ($photo_array as $photo) {
            array_push($media, array(
                "type" => "photo",
                "media" => "$photo",
                "caption" => $caption
            ));
        }

        $jmedia = json_encode($media);

        $json = file_get_contents($this->sendMediaGroup . 'chat_id=' . $this->user_telegram . '&media=' . $jmedia);
//        $this->SendMessage($json, $this->developer_id);
        return $json;
    }

    public function ParseQuery($json){
//        $this->SendMessage($json, $this->developer_id);
        $query = json_decode($json, true);
        if(isset($query['message'])){
            if(isset($query['message']['photo'])) {
                $this->getUser($query['message']['from']);
                if($this->checkAdmin()){
                    $ph = $this->downloadPhoto($query);
                    if($ph === false){
                        $this->log_db($this->user_telegram, "text", "photo_false");
                    }
                    else {
                        $this->log_db($this->user_telegram, "text", "photo_true");
                        $admin = new \bb_store\admin();
                        $res = $admin->GetLastAction($this->user_telegram);
                        if($res['result'] == true){
                            if($res['data'][0]['action'] == "add_more_photo_to_tmp"){
                                $this->SendMessage("<i>Обновление дополнительных фото...</i>", $this->user_telegram);
                                $tmpLot = new \bb_store\tmp_lot();
                                $res_up = $tmpLot->UploadAdditionalPhoto_tmp($ph, $this->user_telegram);

                                if($res_up['result'] == true){
                                    $data = $res_up['data'][0];
                                    $id = $data['t_lot_id'];
                                    $admin->SetLastAction("", $this->user_telegram);
                                    $btn_more = array(
                                        "text" => "Опубликовать",
                                        "callback_data" => "/transfer_tmp:".$id
                                    );
                                    $btn_finish = array(
                                        "text" => "Добавить фото",
                                        "callback_data" => "/add_more_photo_to_tmp:".$id
                                    );
                                    $row1 = [$btn_finish];
                                    $row2 = [$btn_more];
                                    $kb = array(
                                        "inline_keyboard" => [$row1, $row2]
                                    );
                                    $rp = json_encode($kb);
                                    $this->SendMessage("Фото успешно добавленно к товару.", $this->user_telegram, $rp);
                                }
                                else {
                                    $this->SendMessage("Ошибка!", $this->user_telegram);
                                }
                            }
                            else {
                                $tmpLot = new \bb_store\tmp_lot();
                                $res = $tmpLot->CheckIsAlreadyExist($this->user_telegram);
                                if($res['result'] === true){
                                    if(count($res['data']) > 0){
                                        $e_r = $tmpLot->EditTmpLot($this->user_telegram, "image", urlencode($ph));
                                        if($e_r['result'] === true){
                                            $this->SendMessage("Фото успешно добавленно к товару", $this->user_telegram);
                                            $this->openTmpLot();
                                        }
                                        else {
                                            $this->SendMessage("Ошибка добавления фото к товару!", $this->user_telegram);
                                            $this->SendMessage(print_r($e_r, true), $this->user_telegram);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                else {
                    $this->SendMessage("Загрузка фото разрешена только администрации!", $this->user_telegram);
                }

            }
            if(isset($query['message']['text'])){
                //text income
                $this->getUser($query['message']['from']);
                $this->parseText($query['message']['text']);
            }
        }
        else {
            if(isset($query['callback_query'])){
                $this->getUser($query['callback_query']['from']);
                $this->parseCallback($query['callback_query']['data']);
            }
            else {
                //broken
                exit();
            }
        }
    }

    private function openTmpLot(){
        $tmpLot = new \bb_store\tmp_lot();
        $result = $tmpLot->CheckIsAlreadyExist($this->user_telegram);
        if($result['result'] === true) {
            if (count($result['data']) > 0) {

                $cat_id = $result['data'][0]['category_id'];
                $id = $result['data'][0]['id'];

                $c_r = $this->dataBase->GetCategoryById($cat_id);
                if($c_r['result']) {
                    $msg = "";
                    $msg .= "<b>Товар: </b><i>" . $result['data'][0]['name'] . "</i>\n";

                    $msg .= "<b>Номер: </b><i>" . $result['data'][0]['id'] . "</i>\n";

                    $msg .= "<b>Описание: </b><i>" . $result['data'][0]['description'] . "</i>\n";

                    $msg .= "<b>Цена: </b><i>" . $result['data'][0]['price'] . "</i>\n";

                    $msg .= "<b>Категория: </b><i>" . $c_r['data'][0]['name'] . "</i>\n";

                    $pr = "есть";
                    if ($result['data'][0]['present'] == true) {
                        $pr = "есть";
                    } else {
                        $pr = "нет";
                    }

                    $msg .= "<b>Наличие: </b><i>" . $pr . "</i>\n";
                    $msg .= "<b>Фото: </b>" . urldecode($result['data'][0]['image_url']) . "\n";

                    $btn = array(
                        "text" => "Опубликовать",
                        "callback_data" => "/transfer_tmp:".$id
                    );

                    $btn_more_ph = array(
                        "text" => "Добавить фото",
                        "callback_data" => "/add_more_photo_to_tmp:".$id
                    );

                    $row = [$btn];
                    $row2 = [$btn_more_ph];
                    $kb = array(
                        "inline_keyboard" => [$row, $row2]
                    );
                    $rp = json_encode($kb);
                    $this->SendMessage($msg, $this->user_telegram, $rp);
                }
                else {

                }
            }
        }
        else {
            $this->SendMessage("Ошибка!", $this->user_telegram);
            $this->SendMessage(print_r($result, true), $this->user_telegram);
        }
    }

    private function downloadPhoto($query){
        $this->getUser($query['message']['from']);
//                $this->SendMessage(print_r($query, true))

        $sizes = count($query['message']['photo']);
        $best_size = $query['message']['photo'][$sizes - 1];
        $file_id = $best_size['file_id'];
        $file_info = file_get_contents($this->getFile . 'file_id=' . $file_id);

        $file_info = json_decode($file_info, true);

//                $this->SendMessage(print_r($query, true), $this->user_telegram);
//                $this->SendMessage($this->getFile .  'file_id=' . $file_id,$this->user_telegram);
//                $this->SendMessage(print_r($file_info, true), $this->user_telegram);

        if ($file_info['ok'] == true) {
            $result = $file_info['result'];
            $file_path = $result['file_path'];
            $s_r = file_put_contents("/var/www/bbstore.ru/html/images/$file_id.jpg", fopen($this->telegram_file . $file_path, 'r'));

//                    $this->SendMessage($this->telegram_file . $file_path, $this->user_telegram);

            if($s_r === false){
                $this->SendMessage("Ошибка загрузки фото на сервер!", $this->user_telegram);
                $this->SendMessage($s_r, $this->user_telegram);
                return false;
            }
            else {
                $fp = "https://bbstore.ru/images/" . $file_id . ".jpg";
//                $this->SendPhoto("тест отправки фото в ответ с нашего сервера", $this->user_telegram, $fp);
                return $fp;
            }
        }
        else {
            $this->SendMessage("Ошибка получения фото :(", $this->user_telegram);
            return false;
        }
    }

    private function parseText($message){

        $this->log_db($this->user_telegram, "text", $message);

        switch (true){

            case $message == "/start":
//                $this->SendMessage(print_r($this->categories, true), $this->user_telegram);
                $this->MainMenu();
                break;

            case $message === "Все товары":
                $this->showAllTovars();
                break;

            case $message === "/get_user_id":
                $this->SendMessage("<b>Ваш уникальный номер: </b><i>".$this->user_telegram."</i>", $this->user_telegram);
                break;

            case $message === "/admin":
                $this->showAdminMenu();
                break;

            case $message === "/help":
                $this->SendMessage("<b>Меню помощи:</b>\n\n"."<i>В разработке...</i>", $this->user_telegram);
                break;

            default:
                $this->compareIncomeWithCategries($message);
                if($this->inputIsCat == false){
                    $this->addColumnToTmp($message);
                }
                break;
        }
    }

    private function showAdminMenu(){
        if($this->checkAdmin()){
            $message = "<b>Меню администрирования:</b>";
            $btn_create_lot = array(
                "text" => "Добавить товар",
                "callback_data" => "/new_lot"
            );
            $row = [$btn_create_lot];
            $kb = array(
                "inline_keyboard" => [$row]
            );
            $rp = json_encode($kb);
            $this->SendMessage($message, $this->user_telegram, $rp);
        }
        else {
            $this->SendMessage("Вы не являетесь администратором", $this->user_telegram);
        }
    }

    private function addColumnToTmp($message){
        if($this->checkAdmin()){
            $tmpLot = new tmp_lot();
            $ans = $tmpLot->CheckIsAlreadyExist($this->user_telegram);
            if($ans['result'] == true) {
                if (count($ans['data']) > 0) {
                    //есть какой товар редактировать
//                    $this->SendMessage(print_r($ans, true), $this->user_telegram);

                    if($ans['data'][0]['name'] == ""){
//                        $this->SendMessage("Edit_name:...", $this->user_telegram);
                        /**
                         * Edit_name part
                         */
                        $result = $tmpLot->EditTmpLot($this->user_telegram, "name", $message);
                        if($result['result'] == true){
                            $this->SendMessage("Название успешно сохранено!\nВведите <i>описание </i> товара:", $this->user_telegram);
                        }
                        else {
                            $this->SendMessage("Ошибка!", $this->user_telegram);
                            $this->SendMessage(print_r($result, true), $this->user_telegram);
                        }
                    }
//                    elseif ($ans['data'][0]['number'] == ""){
////                        $this->SendMessage("Edit_number:...", $this->user_telegram);
//                        /**
//                         * Edit_number part
//                         */
//                        $result = $tmpLot->EditTmpLot($this->user_telegram, "number", $message);
//                        if($result['result'] == true){
//                            $this->SendMessage("Номер успешно сохранен!\nВведите <i>описание </i> товара:", $this->user_telegram);
//                        }
//                        else {
//                            $this->SendMessage("Ошибка!", $this->user_telegram);
//                            $this->SendMessage(print_r($result, true), $this->user_telegram);
//                        }
//                    }
                    elseif ($ans['data'][0]['description'] == ""){
//                        $this->SendMessage("Edit_description:...", $this->user_telegram);
                        /**
                         * Edit description part
                         */
                        $result = $tmpLot->EditTmpLot($this->user_telegram, "description", $message);
                        if($result['result'] == true){
                            $this->SendMessage("Описание успешно сохранено!\nВведите <i>цену </i> товара:", $this->user_telegram);
                        }
                        else {
                            $this->SendMessage("Ошибка!", $this->user_telegram);
                            $this->SendMessage(print_r($result, true), $this->user_telegram);
                        }
                    }
                    elseif ($ans['data'][0]['price'] == ""){
//                        $this->SendMessage("Edit_price:...", $this->user_telegram);
                        /**
                         * Edit_price part
                         */
                        $result = $tmpLot->EditTmpLot($this->user_telegram, "price", $message);
                        if($result['result'] == true){
                            $btn_pr = array(
                                "text" => "Есть",
                                "callback_data" => "/tmp_tov_present"
                            );

                            $btn_ab = array(
                                "text" => "Нет",
                                "callback_data" => "/tmp_tov_absent"
                            );

                            $row1 = [$btn_pr];
                            $row2 = [$btn_ab];

                            $kb = array(
                                "inline_keyboard" => [$row1, $row2]
                            );

                            $rp = json_encode($kb);
                            $this->SendMessage("Цена успешна сохранена!\nУкажите <i>наличие </i> товара (есть/нет):", $this->user_telegram, $rp);
                        }
                        else {
                            $this->SendMessage("Ошибка!", $this->user_telegram);
                            $this->SendMessage(print_r($result, true), $this->user_telegram);
                        }
                    }
//                    elseif ($ans['data'][0]['present'] == ""){
////                        $this->SendMessage("Edit_present:...", $this->user_telegram);
//                        /**
//                         * Edit_present part
//                         */
//
//
//
//                        //old
//                        if(($message === "Есть")||($message === "есть")||($message === "ЕСТЬ")){
//                            $message = true;
//                        }
//                        elseif (($message === "Нет")||($message === "нет")||($message === "НЕТ")){
//                            $message = false;
//                        }
//                        else {
//                            $this->SendMessage("<b>Команда не распознана!</b>", $this->user_telegram);
//                        }
//                        $result = $tmpLot->EditTmpLot($this->user_telegram, "present", $message);
//                        if($result['result'] == true){
//                            $this->SendMessage("Наличие успешно сохранено!", $this->user_telegram);
//                            /**
//                             * Edit_category part
//                             */
//                            $categories = $this->dataBase->GetCategories();
//                            if($categories['result'] === true){
//                                if(count($categories['data']) > 0){
//                                    $rows = [];
//                                    foreach ($categories['data'] as $category){
//                                        $c_name = $category['name'];
//                                        $c_id = $category['id'];
//                                        $btn = array(
//                                            "text" => $c_name,
//                                            "callback_data" => "/tmp_lot_set_category:".$c_id
//                                        );
//                                        $t_row = [$btn];
//                                        array_push($rows, $t_row);
//                                    }
//
//                                    $keyboard = array(
//                                        "inline_keyboard" => $rows
//                                    );
//                                    $rp = json_encode($keyboard);
//                                    $this->SendMessage("Укажите <i>категорию </i> товара:", $this->user_telegram, $rp);
//                                }
//                                else {
//                                    $this->SendMessage("<b>Получено 0 категорий!</b>", $this->user_telegram);
//                                }
//                            }
//                            else {
//                                $this->SendMessage("<b>Ошибка получения категорий!</b>", $this->user_telegram);
//                            }
//                        }
//                        else {
//                            $this->SendMessage("Ошибка!", $this->user_telegram);
//                            $this->SendMessage(print_r($result, true), $this->user_telegram);
//                        }
//                    }

//                    elseif ($ans['data'][0]['category_id'] == ""){
////                        $this->SendMessage("Edit_category:...", $this->user_telegram);
//
//                    }

                    elseif ($ans['data'][0]['image_url'] == ""){
                        $this->SendMessage("Edit_image:...", $this->user_telegram);
                        /**
                         * Edit_image part ####not here, need logic for getting photo from upload
                         */
                    }
//                    $tmpLot->EditTmLot($this->user_telegram, "name")
                }
            }
        }
    }

    private function compareIncomeWithCategries($message){
        foreach ($this->categories['data'] as $category){
            if($message === $category['name']){
                $this->showLotsFromCategory($category['name'], $category['id']);
                $this->inputIsCat = true;
                break;
            }
        }
    }

    private function showTovar($tovar){
        $img = $tovar['image_url'];
        $name = $tovar['name'];
        $description = $tovar['description'];
        $price = $tovar['price'];
        $number = $tovar['id'];
        $id = $tovar['id'];

        $message =
            "Товар #".$number."\n".
            $name."\n".
            $description."\n".
            "Цена: ".$price." рублей";

        $btn = array(
            "url" => "https://t.me/a_tyncherova",
            "text" => "Заказать"
        );

        $allPhoto = $this->dataBase->GetAllLotPhoto($id);
        $row = [];
        $keyboard = [];
        $edit_row = [];
        if($allPhoto['result'] === true){
//            $btn_more = array(
//                "text" => "Больше фото",
//                "callback_data" => "/more_photo:".$id
//            );
            $ph_arr = [urldecode($img)];
            foreach ($allPhoto['data'] as $ph) {
                array_push($ph_arr, urldecode($ph['url']));
            }
            $row = [$btn];
            if($this->checkAdmin()){
                $btn_change_present = array(
                    "text" => "Изменить наличие",
                    "callback_data" => "/switch_present:".$id
                );

                $btn_change_price = array(
                    "text" => "Изменить цену",
                    "callback_data" => "/change_price:".$id
                );

                $edit_row = [$btn_change_present, $btn_change_price];
                $keyboard = array(
                    "inline_keyboard" => [$row, $edit_row]
                );
            }
            else{
                $keyboard = array(
                    "inline_keyboard" => [$row]
                );
            }
            $replyMarkup = json_encode($keyboard);

            $this->sendMediaGroup($ph_arr, "Товар №".$number."\n".$name."\n".$description."\n"."Цена: ".$price." рублей");
            $this->SendMessage($message, $this->user_telegram, $replyMarkup);
        }
        else{
            $row = [$btn];
            if($this->checkAdmin()){
                $btn_change_present = array(
                    "text" => "Изменить наличие",
                    "callback_data" => "/switch_present:".$id
                );

                $btn_change_price = array(
                    "text" => "Изменить цену",
                    "callback_data" => "/change_price:".$id
                );

                $edit_row = [$btn_change_present, $btn_change_price];
                $keyboard = array(
                    "inline_keyboard" => [$row, $edit_row]
                );
            }
            else {
                $keyboard = array(
                    "inline_keyboard" => [$row]
                );
            }
            $replyMarkup = json_encode($keyboard);
            $this->SendPhoto($message, $this->user_telegram, $img, $replyMarkup);
        }
    }

    private function checkAdmin(){
        if (($this->user_telegram == $this->adm_id_1) || ($this->user_telegram == $this->adm_id_2) || ($this->user_telegram == $this->developer_id)) {
            return true;
        }
        else {
            return false;
        }
    }

    private function showOtzivi($tovar){
        $img = $tovar['image_url'];
        $description = $tovar['description'];
        $id = $tovar['id'];

        $message =
            $description."\n";

            $btn = array(
                "url" => "https://t.me/a_tyncherova",
                "text" => "Напишите нам"
            );



        $row = [$btn];
        $keyboard = array(
            "inline_keyboard" => [$row]
        );


        $replyMarkup = json_encode($keyboard);

//        $this->SendMessage($message, $this->user_telegram);
        $this->SendPhoto($message, $this->user_telegram, $img, $replyMarkup);
    }

    private function showAllTovars(){
        $this->SendMessage("<b>Выбранна категория:</b> <i>Все товары</i>", $this->user_telegram);
        $lots = $this->dataBase->GetAllLots();
        if($lots['result'] == true){
            if(count($lots['data']) > 0){
                foreach ($lots['data'] as $tovar){
                    $this->showTovar($tovar);
                }
            }
            else{
                $message = "<b>В данной категории отстутствуют товары!</b>";
                $this->SendMessage($message, $this->user_telegram);
            }
        }
        else{
            $errInf = print_r($lots['error'], true);
            $message = "Ошибка подключения к базе данных!";
            $this->SendMessage($message, $this->user_telegram);
        }
    }

    private function showLotsFromCategory($category_name, $category_id){
        $this->SendMessage("<b>Выбранна категория:</b> <i>".$category_name."</i>", $this->user_telegram);
        $lots = $this->dataBase->GetLotsFromCategory($category_id);
        if($lots['result'] == true){
            if(count($lots['data']) > 0){
                foreach ($lots['data'] as $tovar){
                    if($category_name === "Отзывы"){
                        $this->showOtzivi($tovar);
                    }
                    else {
                        $this->showTovar($tovar);
                    }
                }
            }
            else{
                $message = "<b>В данной категории отстутствуют товары!</b>";
                $this->SendMessage($message, $this->user_telegram);
            }
        }
        else{
            $errInf = print_r($lots['error'], true);
            $message = "Ошибка подключения к базе данных!";
            $this->SendMessage($message, $this->user_telegram);
        }
    }

    private function parseCallback($query){

        $this->log_db($this->user_telegram, "callback_query", $query);

        switch (true){

            case strpos($query, "more_photo:"):
//                $this->SendMessage($query, $this->user_telegram);
                $lot_id = explode(":", $query)[1];
                $this->showAllLotPhoto($lot_id);
                break;

            case strpos($query, "switch_present:"):
                $lot_id = explode(":", $query)[1];
                $this->switchPresent($lot_id);
                break;

            case strpos($query, "new_lot"):
                $this->newLotHandler();
                break;

            case strpos($query, "tmp_tov_present"):
                $tmpLot = new \bb_store\tmp_lot();
                $result = $tmpLot->EditTmpLot($this->user_telegram, "present", true);
                if($result['result'] == true){
                    $this->SendMessage("Наличие успешно сохранено!", $this->user_telegram);
                    /**
                     * Edit_category part
                     */
                    $categories = $this->dataBase->GetCategories();
                    if($categories['result'] === true){
                        if(count($categories['data']) > 0){
                            $rows = [];
                            foreach ($categories['data'] as $category){
                                $c_name = $category['name'];
                                $c_id = $category['id'];
                                $btn = array(
                                    "text" => $c_name,
                                    "callback_data" => "/tmp_lot_set_category:".$c_id
                                );
                                $t_row = [$btn];
                                array_push($rows, $t_row);
                            }

                            $keyboard = array(
                                "inline_keyboard" => $rows
                            );
                            $rp = json_encode($keyboard);
                            $this->SendMessage("Укажите <i>категорию </i> товара:", $this->user_telegram, $rp);
                        }
                        else {
                            $this->SendMessage("<b>Получено 0 категорий!</b>", $this->user_telegram);
                        }
                    }
                    else {
                        $this->SendMessage("<b>Ошибка получения категорий!</b>", $this->user_telegram);
                    }
                }
                else {
                    $this->SendMessage("Ошибка!", $this->user_telegram);
                    $this->SendMessage(print_r($result, true), $this->user_telegram);
                }
                break;

            case strpos($query, "tmp_tov_absent"):
                $tmpLot = new \bb_store\tmp_lot();
                $result = $tmpLot->EditTmpLot($this->user_telegram, "present", 0);
                if($result['result'] == true){
                    $this->SendMessage("Наличие успешно сохранено!", $this->user_telegram);
                    /**
                     * Edit_category part
                     */
                    $categories = $this->dataBase->GetCategories();
                    if($categories['result'] === true){
                        if(count($categories['data']) > 0){
                            $rows = [];
                            foreach ($categories['data'] as $category){
                                $c_name = $category['name'];
                                $c_id = $category['id'];
                                $btn = array(
                                    "text" => $c_name,
                                    "callback_data" => "/tmp_lot_set_category:".$c_id
                                );
                                $t_row = [$btn];
                                array_push($rows, $t_row);
                            }

                            $keyboard = array(
                                "inline_keyboard" => $rows
                            );
                            $rp = json_encode($keyboard);
                            $this->SendMessage("Укажите <i>категорию </i> товара:", $this->user_telegram, $rp);
                        }
                        else {
                            $this->SendMessage("<b>Получено 0 категорий!</b>", $this->user_telegram);
                        }
                    }
                    else {
                        $this->SendMessage("<b>Ошибка получения категорий!</b>", $this->user_telegram);
                    }
                }
                else {
                    $this->SendMessage("Ошибка!", $this->user_telegram);
                    $this->SendMessage(print_r($result, true), $this->user_telegram);
                }
                break;

            case strpos($query, "add_more_photo_to_tmp:"):
                $t_lot_id = explode(":", $query)[1];
                $admin = new \bb_store\admin();
                $res = $admin->SetLastAction("add_more_photo_to_tmp", $this->user_telegram);
                if($res['result'] == true){
                    $this->SendMessage("Прикрепите фото: ", $this->user_telegram);
                }
                break;

            case strpos($query, "transfer_tmp"):
                $lot_id = explode(":", $query)[1];
                $tmpLot = new \bb_store\tmp_lot();
                $result = $tmpLot->Transfer($this->user_telegram);
//                $this->SendMessage(print_r($result, true), $this->user_telegram);
                $this->SendMessage("Лот успрешно опубликован!", $this->user_telegram);
                $this->showAdminMenu();
//                $this->SendMessage(print_r($result, true), $this->user_telegram);
//                if($result['result'] === true) {
//                    $this->SendMessage("Лот успрешно опубликован!", $this->user_telegram);
//                }
//                else {
//                    $this->SendMessage("Ошибка!", $this->user_telegram);
//                    $this->SendMessage(print_r($result, true), $this->user_telegram);
//                }
                break;

            case strpos($query, "tmp_lot_set_category:"):
                $cat_id = explode(":", $query)[1];
                if($this->checkAdmin()){
                    $tmpLot = new \bb_store\tmp_lot();
                    $result = $tmpLot->CheckIsAlreadyExist($this->user_telegram);
                    if($result['result'] == true){
                        if(count($result['data']) > 0){
                            $er = $tmpLot->EditTmpLot($this->user_telegram, "category", $cat_id);
                            if($er['result'] == true){
                                $this->SendMessage("Категория успешно сохранена!", $this->user_telegram);
                                $this->SendMessage("Теперь прекрепите фото!", $this->user_telegram);
                            }
                            else {
                                $this->SendMessage("Ошибка!", $this->user_telegram);
                                $this->SendMessage(print_r($er, true), $this->user_telegram);
                            }
                        }
                    }
                    else {
                        $this->SendMessage("Ошибка!", $this->user_telegram);
                        $this->SendMessage(print_r($result, true), $this->user_telegram);
                    }
                }
                break;

            case strpos($query, "delete_my_tmp"):
                if($this->checkAdmin()){
                    $tmpLot = new \bb_store\tmp_lot();
                    $ans = $tmpLot->DeleteTmp($this->user_telegram);
                    if($ans['result'] == true){
                        $this->SendMessage("<b>Успеншо удалено!</b>", $this->user_telegram);
                        $this->newLotHandler();
                    }
                    else {
                        $this->SendMessage("<b>Ошибка!</b>", $this->user_telegram);
                    }
                }
                else {
                    $this->SendMessage("<b>Вы не являетесь администратором!</b>", $this->user_telegram);
                }
                break;

            case strpos($query, "open_my_tmp"):
                if($this->checkAdmin()){
                    $tmpLot = new \bb_store\tmp_lot();
                    $result = $tmpLot->CheckIsAlreadyExist($this->user_telegram);
                    if($result['result'] == true) {
                        if (count($result['data']) > 0) {
                            $msg = "";
                            if($result['data'][0]['name'] != ""){
                                $msg .= "<b>Товар: </b><i>".$result['data'][0]['name']."</i>\n";
                            }
                            elseif($result['data'][0]['id'] != ""){
                                $msg .= "<b>Номер: </b><i>".$result['data'][0]['id']."</i>\n";
                            }
                            elseif($result['data'][0]['description'] != ""){
                                $msg .= "<b>Описание: </b><i>".$result['data'][0]['description']."</i>\n";
                            }
                            elseif($result['data'][0]['price'] != ""){
                                $msg .= "<b>Цена: </b><i>".$result['data'][0]['price']."</i>\n";
                            }
                            elseif($result['data'][0]['present'] != ""){
                                $pr = "есть";
                                if($result['data'][0]['present'] == true){
                                    $pr = "есть";
                                }
                                else {
                                    $pr = "нет";
                                }
                                $msg .= "<b>Наличие: </b><i>".$pr."</i>\n";
                            }
                            elseif ($result['data'][0]['category_id'] != "")
                            if($result['data'][0]['image_url'] != ""){
                                $msg .= "<b>Фото: </b>".urldecode($result['data'][0]['image_url'])."\n";
                            }

                            $this->SendMessage($msg, $this->user_telegram);
                        }
                    }
                }
                break;
        }
    }

    private function newLotHandler(){
        if($this->checkAdmin()){
            $tmpLot = new tmp_lot();
            $ans = $tmpLot->CheckIsAlreadyExist($this->user_telegram);
            if($ans['result'] == true){
                if(count($ans['data']) > 0){
                    $btn_open = array(
                        "text" => "Открыть и продолжить",
                        "callback_data" => "/open_my_tmp"
                    );
                    $btn_delete = array(
                        "text" => "Удалить",
                        "callback_data" => "/delete_my_tmp"
                    );
                    $row = [$btn_open, $btn_delete];
                    $kb = array(
                        "inline_keyboard" => [$row]
                    );
                    $rp = json_encode($kb);
                    $this->SendMessage("<b>У вас есть не сохраненный товар!</b>", $this->user_telegram, $rp);
                }
                else {
                    $this->SendMessage("<i>Начинаем создание товара...</i>", $this->user_telegram);
                    $cr_ans = $tmpLot->CreateTmpLot($this->user_telegram);
                    if($cr_ans['result'] == true){
                        $id = $cr_ans['data']['id'];
//                        $this->SendMessage(print_r($cr_ans, true), $this->user_telegram);
                        $this->SendMessage("Успешно!\nВременный ID товара: ".$id, $this->user_telegram);
                        $this->SendMessage("Введите <i>название </i> товара:", $this->user_telegram);
                    }
                }
            }
            else {
                $this->SendMessage("Ошибка!", $this->user_telegram);
            }
        }
        else {
            $this->SendMessage("Вы не являетесь администратором", $this->user_telegram);
        }
    }

    private function switchPresent($lot_id){
        $lot_info = $this->dataBase->GetLotById($lot_id);
        if($lot_info['result'] == true){
            $present = $lot_info['data'][0]['present'];
//            $this->SendMessage($present, $this->user_telegram);
            $col = "present";
            $new_val = false;
            if($present == true){
                $new_val = 0;
            }
            else {
                $new_val = 1;
            }
            $result = $this->dataBase->EditLot($col, $new_val, $lot_id);
            if($result['result'] == true){
                $this->SendMessage("Успешно!", $this->user_telegram);
            }
            else {
                $this->SendMessage("Ошибка", $this->user_telegram);
                $this->SendMessage(print_r($result, true), $this->user_telegram);
            }
        }
        else {
            $this->SendMessage($lot_info['error'], $this->user_telegram);
        }
    }

    private function getUser($user){
        $this->user_telegram = $user['id'];
    }

    /**
     * @param $lot_id
     * @deprecated
     */
    private function showAllLotPhoto($lot_id){
        $photo_data = $this->dataBase->GetAllLotPhoto($lot_id);
        if($photo_data['result'] === true){
//            $this->SendMessage(print_r($photo_data['data'], true), $this->user_telegram);
            $ph_number = 1;
            foreach ($photo_data['data'] as $photo_object){
                $id = $photo_object['id'];
                $url = $photo_object['url'];

                $this->SendPhoto("Фото #".$ph_number, $this->user_telegram, $url);
                $ph_number++;
            }
        }
        else {
            $this->SendMessage($photo_data['error'], $this->user_telegram);
        }
    }

    public function MainMenu(){
        $this->showAllCategories();
    }

    private function showAllCategories(){
        if($this->categories['result'] == true){
            if(count($this->categories['data']) > 0){
//                $this->SendMessage("збс", $this->user_telegram);
//                $message = "Получено ".count($categories['data'])." категорий! База работает!";
//                $this->SendMessage(print_r($this->categories, true), $this->user_telegram);
                $rows = [];
                $row = [];
                $cnt = 0;
                $k = 0;
                $first_btn = array(
                    "text" => "Все товары"
                );

                $first_row = [$first_btn];

                array_push($rows, $first_row);

//                $last = false;

                foreach ($this->categories['data'] as $category){
                    $btn = array(
                        "text" => $category['name']
                    );
//                    $this->SendMessage(print_r($btn, true), $this->user_telegram);
                    if($cnt < 3){
                        array_push($row, $btn);
//                        $last = false;
//                        $this->SendMessage("cnt < 3", $this->user_telegram);
//                        $this->SendMessage(print_r($row, true), $this->user_telegram);
                    }
                    else {
                        array_push($rows, $row);
                        $cnt = 0;
                        $row = null;
                        $row = [];

                        $btn = array(
                            "text" => $category['name']
                        );
                        array_push($row, $btn);
//                        $this->SendMessage("cnt == 3", $this->user_telegram);
//                        $this->SendMessage(print_r($rows, true), $this->user_telegram);
                    }
                    $cnt++;
                    $k++;
                }

                array_push($rows, $row);

//                $this->SendMessage("finish", $this->user_telegram);
//                $this->SendMessage(print_r($rows, true), $this->user_telegram);

                if(count($rows) < 2){
                    array_push($rows, $row);
                }


                $keyboard = array(
                    "keyboard"=>$rows,
                    "resize_keyboard"=>true
                );

//                $this->SendMessage(print_r($keyboard, true), $this->user_telegram);

                $replyMarkup = json_encode($keyboard);

                $msg = "Доступные категории:";
                $this->SendMessage($msg, $this->user_telegram, $replyMarkup);
            }
            else{
                $message = "Нет доступных категорий!";
                $this->SendMessage($message, $this->user_telegram);
            }
        }
        else {
            $message = "Ошибка получения данных!";
            $errInfo = print_r($this->categories['error'], true);
            $this->SendMessage($message, $this->user_telegram);
            $this->SendMessage($errInfo, $this->user_telegram);
        }
    }

    public function SendMessage($message, $to, $replyMarkup = null){
        if($replyMarkup != null){
            $url = $this->sendMsgQTo."&chat_id=".$to."&text=".urlencode($message)."&reply_markup=".$replyMarkup;
            $result = file_get_contents($url);
        }
        else {
            $url = $this->sendMsgQTo."&chat_id=".$to."&text=".urlencode($message);
            $result = file_get_contents($url);
            //todo: get result
        }
    }

    public function SendPhoto($message, $to, $image, $replyMarkup = null){
        if($replyMarkup != null){
            $url = $this->sendPhQTo."chat_id=".$to."&caption=".urlencode($message)."&photo=".$image."&reply_markup=".$replyMarkup;
            $result = file_get_contents($url);
        }
        else {
            $url = $this->sendPhQTo."chat_id=".$to."&caption=".urlencode($message)."&photo=".$image;
            $result = file_get_contents($url);
        }
    }

    /**
     * @deprecated
     */
    public function CreateReplyMarkup_inline(){

    }

    private function log_db($user, $action_type, $action){
        $role = "";
        if($this->checkAdmin()){
            $role = "admin";
        }
        else {
            $role = "user";
        }
        $this->dataBase->LogWriter($user, $role, $action_type, $action);
    }

}