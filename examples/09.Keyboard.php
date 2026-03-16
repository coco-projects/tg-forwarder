<?php

    require '../vendor/autoload.php';
    require './common.php';

    use Telegram\Bot\Keyboard\Keyboard;

    $reply_markup = Keyboard::make()
        ->setResizeKeyboard(!true)
        ->setOneTimeKeyboard(!true)
        ->row([
            Keyboard::button('1'),
            Keyboard::button('2'),
            Keyboard::button('3'),
        ])
        ->row([
            Keyboard::button('4'),
            Keyboard::button('5'),
            Keyboard::button('6'),
        ])
        ->row([
            Keyboard::button('7'),
            Keyboard::button('8'),
            Keyboard::button('9'),
        ])
        ->row([
            Keyboard::button('0'),
        ]);

    $response = $bot->sendMessage([
        'chat_id'      => $chatId,
        'text'         => 'the Keyboard',
        'reply_markup' => $reply_markup,
    ]);

    $messageId = $response->getMessageId();