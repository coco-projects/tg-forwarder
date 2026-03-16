<?php

    require '../vendor/autoload.php';
    require './common.php';

    $response = $bot->sendPhoto([
        'chat_id' => $chatId,
        'photo'   => \Telegram\Bot\FileUpload\InputFile::create($imagePath, 'test.jpg'),
        'caption' => 'Some caption',
    ]);

    $messageId = $response->getMessageId();
    echo $messageId;
