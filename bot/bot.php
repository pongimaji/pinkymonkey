<?php
$input=file_get_contents('php://input');
$data = json_decode($input);
$chat_id = $data->message->chat->id;
$first_name = $data->message->chat->first_name;
$text = $data->message->text;
$callback = $data->callback_query;
$callback_data = $callback->data;
$secret_token_telegram = '6796827160:AAGYEQeKgVlnDnwnbhrPsFWyK7x84bGcOCU';

//setup
// https://api.telegram.org/bot{my_bot_token}/setWebhook?url={url_to_send_updates_to}

//send message function
function sendMessage($message_text,$reply_markup='',$to) {
    global $secret_token_telegram;

    $url = "https://api.telegram.org/bot" . $secret_token_telegram . "/sendMessage?parse_mode=markdown&chat_id=" . $to;
    $url = $url . "&text=" . urlencode($message_text);
    $url = $url . ($reply_markup!='' ? "&reply_markup={$reply_markup}" : '');
    $ch = curl_init();
    $optArray = array(  
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
    );
    curl_setopt_array($ch, $optArray);
    $result = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
}
function sendImage($chatId,$imagePath){
    global $secret_token_telegram;

    // URL untuk mengirim gambar ke API Telegram
    $apiUrl = "https://api.telegram.org/bot$secret_token_telegram/sendPhoto";

    // Persiapan data POST
    $postData = [
        'chat_id' => $chatId,
    ];
    $postData['photo'] = new CurlFile($imagePath, 'image/jpeg', 'image.jpg');
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
    $response = curl_exec($ch);
    curl_close($ch);
}
function getActivity(){
    // URL API yang ingin Anda akses
    $api_url = 'http://www.boredapi.com/api/activity/';

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = array();

    // Jika berhasil mengambil data
    if ($response !== false) {
        $data = json_decode($response, true); // Mengurai data JSON
        $result['stat'] = True;
        $result['aktifitas'] = $data['activity'];
        $result['tipe']      = $data['type'];
    } else {
        $result['stat'] = False;
        $result['msg'] = 'Maaf saat ini kami tidak dapat memberikan anda kegiatan random :(';
    }

    return $result;
}
function generateAcitivity($chat_id){
    $getdata = getActivity();
    if($getdata['stat']){
        $keyboard=json_encode(array(
         'keyboard' => array(
             array('Generate Kegiatan')
         ),
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ));
        sendMessage('🧐 Berikut adalah saran kegiatan buat anda:','',$chat_id);
        sendMessage('***"'.$getdata['aktifitas'].'"***','',$chat_id);
        sendMessage('Kurang puas?? /generate lagi ',$keyboard,$chat_id);
    }else{
        sendMessage($getdata['msg'],'',$chat_id);
    }
}
// ============================================================================

switch ($text) {    
    case '/generate':
        generateAcitivity($chat_id);
        break;

    case 'Generate Kegiatan':
        generateAcitivity($chat_id);
        break;

    case '/about':
        $text ='';
        $keyboard=json_encode(array(
         'keyboard' => array(
             array('/dzakyy'),
             array('/elgaa'),
             array('/salsaa'),
             array('/lipongg')
         ),
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ));
        sendMessage('BOT ini dibuat dengan cinta, teh pucuk, batagor serta canda & tawa oleh: /dzakyy /elgaa /salsaa /lipongg dalam rangka event Hackaton KeDai 2023. Semoga bot ini dapat membantu serta menghiburmu agar hidupmu bahagia selalu 😁',$keyboard,$chat_id);
        break;

    case '/salsaa':
        sendImage($chat_id,'img/salsa.jpg');
        sendMessage('Salsa adalah gadis cantik lucu & imut tapi bikin pakbal . . . .','',$chat_id);
        break;

    case '/lipongg':
        sendImage($chat_id,'img/lipong.jpg');
        sendMessage('Lipongg adalah manusia yang suka makan, mencintai Lisa dan berharap dapat menikahi blackpink secara lengkap karena pas 4 . . . .','',$chat_id);
        break;

    case '/elgaa':
        sendImage($chat_id,'img/elga.jpg');
        sendMessage('Elga adalah ibu-ibu dengan volume suara kayak speaker, ndak bisa berhenti bicara tapi cantik dan baik hati anjayyy . . . .','',$chat_id);
        break;

    case '/dzakyy':
        sendImage($chat_id,'img/dzaky.jpg');
        sendMessage('Dzaky adalah pria paling cool dikelompok ini. idaman wanita dan semua gadis terklepek klepek kepadanya . . . .','',$chat_id);
        break;

    default:
        $text ='';
        $keyboard=json_encode(array(
         'keyboard' => array(
             array('Generate Kegiatan')
         ),
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ));
        sendMessage('Halo '.$first_name.'... Selamat datang di Gabutta Mi.... Anda gabut? cari kegiatan random disini . . . .',$keyboard,$chat_id);
        break;
}

?>