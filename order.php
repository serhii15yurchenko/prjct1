<?php
session_start();
date_default_timezone_set('Europe/Kiev');
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
//die;

// TG
$tg_token = '';
$tg_chatid = '';

// Mail
$mail = '';

// LP-CRM
$crm_token = '';
$crm_address = '';

// Product details
$product_id = '';
$product_price = '';
$product_title = '';
$product_qnt = 1;


// client details
$name = input_cleaner($_POST['name']);
$phone = preg_replace('/[^0-9]/', '', input_cleaner($_POST['phone']));
$user_mail = $_POST['mail'];
$comment = input_cleaner($_POST['comment']);


// server details
$domain = $_SERVER['SERVER_NAME'];
$date = date("Y-m-d H:i:s");

if (!empty($phone)) {

    if (!empty($crm_token)) {
        $products_list = array(
            0 => array(
                'product_id' => $product_id,
                'price' => $product_price,
                'count' => $product_qnt,
            ),
        );
        $products = urlencode(serialize($products_list));
        $sender = urlencode(serialize($_SERVER));
        $data = array(
            'key' => $crm_token,
            'order_id' => number_format(round(microtime(true) * 10), 0, '.', ''),
            'country' => 'UA',                         // Географическое направление заказа
            'office' => '1',                          // Офис (id в CRM)
            'products' => $products,                    // массив с товарами в заказе
            'bayer_name' => $name,            // покупатель (Ф.И.О)
            'phone' => $phone,           // телефон
            'email' => $_REQUEST['email'],           // электронка
            'comment' => $comment,                           // комментарий
            'delivery' => $_REQUEST['delivery'],        // способ доставки (id в CRM)
            'delivery_adress' => $_REQUEST['delivery_adress'], // адрес доставки
            'payment' => '',                           // вариант оплаты (id в CRM)
            'sender' => $sender,
            'utm_source' => $_SESSION['utms']['utm_source'],  // utm_source
            'utm_medium' => $_SESSION['utms']['utm_medium'],  // utm_medium
            'utm_term' => $_SESSION['utms']['utm_term'],    // utm_term
            'utm_content' => $_SESSION['utms']['utm_content'], // utm_content
            'utm_campaign' => $_SESSION['utms']['utm_campaign'], // utm_campaign
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $crm_address . '/api/addNewOrder.html');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $out = curl_exec($curl);
        curl_close($curl);

        // echo "<pre>";
        // print_r(json_decode($out, true));
        // echo "</pre>";
    }

    $arr = array(
        'Заявка:' => '',
        'Імʼя: ' => $name,
        'Телефон: ' => $phone,
        'Пошта: ' => $user_mail,
        'Дата: ' => $date,
        'Сайт: ' => $domain,
    );

    // if (isset($_COOKIE['fbid'])) {
    //     $arr['FbID: '] = $_COOKIE['fbid'];
    // };
    // if (!empty($product_id)) {
    //     $arr['Product ID: '] = $product_id;
    // };
    // if (!empty($product_price)) {
    //     $arr['Ціна: '] = $product_price;
    // };
    // if (!empty($product_qnt)) {
    //     $arr['Кількість: '] = $product_qnt;
    // };
    // if (!empty($comment)) {
    //     $arr['Комент: '] = $comment;
    // };
    // if (!empty($product_title)) {
    //     $arr['Товар: '] = $product_title;
    // };

    if (isset($_COOKIE['utm_source'])) {
        $arr['utm_source: '] = $_COOKIE['utm_source'];
    }
    if (isset($_COOKIE['utm_medium'])) {
        $arr['utm_medium: '] = $_COOKIE['utm_medium'];
    }
    if (isset($_COOKIE['utm_term'])) {
        $arr['utm_term: '] = $_COOKIE['utm_term'];
    }
    if (isset($_COOKIE['utm_content'])) {
        $arr['utm_content: '] = $_COOKIE['utm_content'];
    }
    if (isset($_COOKIE['utm_campaign'])) {
        $arr['utm_campaign: '] = $_COOKIE['utm_campaign'];
    }

    if (!empty($tg_token) && !empty($tg_chatid)) {
        $txt = '';

        foreach ($arr as $key => $value) {
            $txt .= "<b>" . $key . "</b> " . $value . "%0A";
        };

        $link = "https://api.telegram.org/bot{$tg_token}/sendMessage?chat_id={$tg_chatid}&parse_mode=html&text={$txt}";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $out = curl_exec($curl);
        curl_close($curl);

//         echo "<pre>";
//         print_r(json_decode($out, true));
//         echo "</pre>";


        // $sendToTelegram = fopen("https://api.telegram.org/bot{$tg_token}/sendMessage?chat_id={$tg_chatid}&parse_mode=html&text={$txt}", "r");
    }


//     if (!empty($mail)) {
//         $mail_txt = '';
//
//         foreach ($arr as $key => $value) {
//             $mail_txt .= "<b>" . $key . "</b> " . $value . "<br>";
//         };
//
//         $tema_r = 'New order';
//         $from = "New order <noreply@{$_SERVER['HTTP_HOST']}>";
//         $subject = "=?utf-8?B?" . base64_encode("$tema_r") . "?=";
//         $header = "From: $from";
//         $header .= "\nContent-type: text/html; charset=\"utf-8\"";
//         $msg = $mail_txt;
//         mail($mail, $subject, $msg, $header);
//     }

    if (!empty($mail)) {
        $mail_txt = '';

        foreach ($arr as $key => $value) {
            $mail_txt .= "<b>" . $key . "</b> " . $value . "<br>";
        };

        $tema_r = 'Заявка';
        $from = "New order 12312";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $msg = $mail_txt;

        mail($mail, $tema_r, $msg, $headers);
    }
}



function input_cleaner($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}
?>
<!-- <!DOCTYPE html>
<html lang="ua_UK">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://i.ibb.co/KwXbcKf/favicon-32x32.png" type="image/x-icon">
    <?php if ($_POST['phone']) : ?>
        <title>Дякуємо за замовлення!</title>


        
    <?php else : ?>
        <title>Якась проблема...!</title>
    <?php endif; ?>
</head>

<body>
<div class="main">
    <div class="container">
        <div class="order-info">
            <?php if ($_POST['phone']) : ?>
                <h2>Дякуємо за замовлення!</h2>
                <h4>
                    Наш менеджер скоро зв'яжеться з Вами!
                    <br>
                    очікуйте дзвінок
                </h4>
                <a href="javascript:history.back()" class="btn btn-success">Повернутися назад</a>

            <?php else : ?>
                <h4 style="color: darkred;">Що щось пішло не так, і форма не була відправлена...</h4>
                <h2 style="font-weight: 700; color: green">Будь ласка, заповніть форму ще раз!</h2>
                <a href="javascript:history.back()" class="btn btn-error" style="text-transform: uppercase;">Заповнити
                    форму ще раз</a>
            <?php endif; ?>

        </div>
    </div>
</div>
<style>
    body {
        background-image: url('https://i.ibb.co/gmNVzPD/photo-1560264280-88b68371db39.jpg');
        background-size: cover;
        background-repeat: no-repeat;
        padding: 0px;
        margin: 0px;
        font-family: Tahoma, sans-serif;
    }
    .container{
        width: 90%;
        max-width: 600px;
    }
    .main {
        width: 100%;
        height: 100vh;
        background-color: rgba(195, 195, 195, .7);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .order-info {
        padding: 20px;
        background-color: #f5f5f5;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center
    }
    .order-info h2 {
        font-weight: 700;
        margin-bottom: 30px
    }
    .btn{
        text-transform: uppercase;
        text-decoration: none;
        color: white;
        padding: 16px 30px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: .4s;
        margin-top: 0px
    }
    .btn.btn-success{
        border: 2px solid #21b021;
        background-color: #1fae1f;
    }
    .btn.btn-success:hover{
        border: 2px solid #0c880c;
        background-color: #0c880c;
    }
    .btn.btn-error{
        border: 2px solid green;
        background-color: green;
    }
    .btn.btn-error:hover{
        border: 2px solid #770e0e;
        background-color: #770e0e;
    }
</style>

</body>

</html> -->