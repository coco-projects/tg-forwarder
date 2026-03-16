<?php

    require '../vendor/autoload.php';
    require './common.php';

    $response = $bot->sendMessage([
        'chat_id' => $chatId,
        'text'    => 'Hello World',
    ]);

    $messageId = $response->getMessageId();
    echo $messageId;
    