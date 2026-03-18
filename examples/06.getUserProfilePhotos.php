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
    echo PHP_EOL;
    print_r($photos);;;
    echo PHP_EOL;
    print_r($messageId);;;
    echo PHP_EOL;
    print_r($response);;;
/*
Illuminate\Support\Collection Object
(
    [items:protected] => Array
        (
            [0] => Telegram\Bot\Objects\PhotoSize Object
                (
                    [items:protected] => Array
                        (
                            [0] => Array
                                (
                                    [file_id] => AgACAgUAAxUAAWm6s9U0tUY4rqi_NVTNiwQZfGk6AALEtDEbGSrgV76oLfUmi3yQAQADAgADYQADNgQ
                                    [file_unique_id] => AQADxLQxGxkq4FcAAQ
                                    [file_size] => 8600
                                    [width] => 160
                                    [height] => 160
                                )

                            [1] => Array
                                (
                                    [file_id] => AgACAgUAAxUAAWm6s9U0tUY4rqi_NVTNiwQZfGk6AALEtDEbGSrgV76oLfUmi3yQAQADAgADYgADNgQ
                                    [file_unique_id] => AQADxLQxGxkq4Fdn
                                    [file_size] => 23004
                                    [width] => 320
                                    [height] => 320
                                )

                            [2] => Array
                                (
                                    [file_id] => AgACAgUAAxUAAWm6s9U0tUY4rqi_NVTNiwQZfGk6AALEtDEbGSrgV76oLfUmi3yQAQADAgADYwADNgQ
                                    [file_unique_id] => AQADxLQxGxkq4FcB
                                    [file_size] => 60145
                                    [width] => 640
                                    [height] => 640
                                )

                        )

                    [escapeWhenCastingToString:protected] =>
                )

        )

    [escapeWhenCastingToString:protected] =>
)


*/