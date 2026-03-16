<?php

    $mysqlHost     = '127.0.0.1';
    $mysqlUsername = 'root';
    $mysqlPassword = 'root';
    $mysqlDb       = 'tg_forwarder';

    $imagePath = __DIR__ . '/examples/media/1.jpg';

//	tktest002 @tktest002

    $token   = '8656070000:AAHxBUslqpxjXLHXSf2qQKb-TA6OEIHD000';
    $group   = -1003807130000;

    $chatId = 5667920000;
    $fileId = 'AgACAgUAAxUAAWmux6C3PBY5PoFt4svwYHsZC3FTAAKSD2sbKvtwVXv82mQwM925AQADAgADYQADxxx';

    $baseBotUrl  = 'http://2.59.151.10:40100/bot';
    $webhookBase = 'https://xxxx.com/index.php';

    $template = [
        "start" => "__FROM_FIRST_NAME__ __FROM_LAST_NAME__【@__FROM_USERNAME__】，你好!",

        "ban_user"   => "__USER_FIRST_NAME__ __USER_LAST_NAME__【@__USER_USERNAME__】，用户已禁用!",
        "unban_user" => "__USER_FIRST_NAME__ __USER_LAST_NAME__【@__USER_USERNAME__】，用户已解禁!",

        "ban_user_admin"   => "__USER_FIRST_NAME__ __USER_LAST_NAME__【@__USER_USERNAME__】，用户是管理员，不能被禁用!",
        "unban_user_admin" => "__USER_FIRST_NAME__ __USER_LAST_NAME__【@__USER_USERNAME__】，用户是管理员，无法操作!",

        "bot_closed_user"  => "当前客服忙碌中，请稍后再试。",
        "bot_closed_admin" => "当前客服机器人已经被关闭，无法再发送信息",
    ];

    $blockWordList = [
        '转账',
        '虫虫危机 ',
        '阿拉丁 ',
    ];