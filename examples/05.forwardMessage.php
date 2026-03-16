<?php

    require '../vendor/autoload.php';
    require './common.php';

    $response = $bot->forwardMessage([
        'chat_id'      => $chatId,
        'from_chat_id' => $chatId,
        'message_id'   => 3,
    ]);

    $messageId = $response->getMessageId();
    echo $messageId;
