<?php

    require '../vendor/autoload.php';
    require './common.php';

    $response = $bot->getUserProfilePhotos([
        'user_id' => $chatId,
    ]);

    $photos_count = $response->getTotalCount();
    $photos       = $response->getPhotos();

    $messageId = $response->getMessageId();

    print_r($photos_count);
    print_r($photos);;;
    print_r($messageId);;;
