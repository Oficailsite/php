<?php
//Замените на id оффера на который будите лить
$offer_id = [784];

//Токен вашего аккаунта. Изменяется при смене пароля! Берется из раздела профиль, вкладка API.
$token = '13ee18267eb8d4718a0fc83cc4d572a6';

//0 - Переход на оффер без приленда, 1 - Переход на оффер с прилендом.
$preland = 0;

// Укажите поток по которому нужно направлять трафик (НЕ ОБЯЗАТЕЛЬНО)!
$stream = 'aD9xOAN4mT';

//Создайте папку redirectlinks c правом на запись (777) - необходимо для кэширования и более быстрой работы!!!
$link_file = 'redirectlinks/' . md5($token . $offer_id[0]) . '.txt';

// Код ниже не трогайте.
if ($offer_id[0]) {

    $subid = (!is_null($_GET['subid']) && !empty($_GET['subid'])) ? '&subid=' . $_GET['subid'] : '';

    if (file_exists($link_file) && filectime($link_file) > time() - 3 * 60) {
        header('Location: ' . file_get_contents($link_file) . $subid);
        exit;
    }
    $post_url = 'https://kavuki.shop/api/getTrafficDomain';
    $post_data = [
        'token' => $token,
        'offer_id' => $offer_id[0]
    ];
    if (!empty($stream)) $post_data['stream'] = $stream;

    $headers = ['Content-Type: application/json']; // заголовки нашего запроса
    $data_json = json_encode($post_data); // переводим поля в формат JSON
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($ch, CURLOPT_URL, $post_url);
    curl_setopt($ch, CURLOPT_POST, true);
    $res = curl_exec($ch);
    if ($preland === 0) {
        $response = $res;
    } else {
        $response = $res . '&p=1';
    }
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (trim($response) && $httpcode == 200) {
        @file_put_contents($link_file, $response);
        header('Location: ' . $response . $subid);
        echo $response . $subid;
    } elseif (file_exists($link_file))
        header('Location: ' . file_get_contents($link_file) . $subid);
    else die('Error: ' . curl_error($ch));
}
?>