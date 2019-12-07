<?php
require __DIR__.'/vendor/autoload.php';
use Cvar1984\TelegramBot\Telegram;
use Cvar1984\TelegramBot\Google;
$lastUpdate=0;
while (true) {
    $lastUpdate = Telegram::getUpdates($lastUpdate['update_id'] + 1);
    $msg = $lastUpdate['message'];
    if (empty($msg['text'])) {
        continue;
    } else {
        if (empty($msg['chat']['title'])) {
            $msg['chat']['title'] = null;
        }
        if (empty($msg['from']['username'])) {
            $msg['from']['username'] = null;
        }

        echo 'Chat Title : ' . $msg['chat']['title'] . PHP_EOL;
        echo 'Chat ID : ' . $msg['chat']['id'] . PHP_EOL;
        echo 'Name : ' . $msg['from']['first_name'] . PHP_EOL;
        echo 'Username: ' . $msg['from']['username'] . PHP_EOL;
        echo 'Text : ' . $msg['text'] . PHP_EOL;
        echo 'Date : ' . date('d/m/Y H:i:s', $msg['date']) . PHP_EOL . PHP_EOL;

        $data = array(
            'username' => $msg['from']['username'],
            'chat_title' => $msg['chat']['title'],
            'chat_id' => $msg['chat']['id'],
            'name' => $msg['from']['first_name'],
            'username' => $msg['from']['username'],
            'text' => $msg['text'],
            'date' => date('d/m/Y H:i:s', $msg['date'])
        );

        $data = json_encode($data, JSON_PRETTY_PRINT);
        Telegram::writeFiles($data, 'chat_logs.json');

        $data = json_encode($msg, JSON_PRETTY_PRINT);
        $status = null;
        Google::search($msg['text']);

        $result=Google::$data;
        $status[]=Telegram::bot(
            'sendMessage',
            [
                'chat_id' => $msg['chat']['id'],
                'parse_mode'=>'html',
                'text'=>$result
            ]
        );

        Telegram::writeFiles(json_encode($status, JSON_PRETTY_PRINT), 'status.json');
        unset($result, $status);
    }
}
