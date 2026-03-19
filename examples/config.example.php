<?php

    $config = [

        'baseBotUrl'  => 'http://2.59.151.000:40100/bot',
        'webhookBase' => 'https://bottest.7yunchiyun.com/index.php',

        'mysqlHost'     => '127.0.0.1',
        'mysqlUsername' => 'root',
        'mysqlPassword' => 'root',
        'mysqlDb'       => 'tg_forwarder',
        'mysqlPort'     => 3306,

        'template' => [
            "start" => "__FROM_FIRST_NAME__ __FROM_LAST_NAME__【@__FROM_USERNAME__】，你好!",

            "ban_user"   => "__USER_FIRST_NAME__ __USER_LAST_NAME__【@__USER_USERNAME__】，用户已禁用!",
            "unban_user" => "__USER_FIRST_NAME__ __USER_LAST_NAME__【@__USER_USERNAME__】，用户已解禁!",

            "ban_user_admin"   => "__USER_FIRST_NAME__ __USER_LAST_NAME__【@__USER_USERNAME__】，用户是管理员，不能被禁用!",
            "unban_user_admin" => "__USER_FIRST_NAME__ __USER_LAST_NAME__【@__USER_USERNAME__】，用户是管理员，无法操作!",

            "bot_closed_user"  => "当前客服忙碌中，请稍后再试。",
            "bot_closed_admin" => "当前客服机器人已经被关闭，无法再发送信息",
        ],

        "blockWordList" => [
            '转账',
            '虫虫危机 ',
            '阿拉丁 ',
        ],

        "debug"          => true,
        "enableEchoLog"  => true,
        "enableRedisLog" => true,

        'redisHost'     => '127.0.0.1',
        'redisPassword' => '',
        'redisPort'     => 6379,
        'redisDb'       => 9,

    ];

    $hash = 'd5d84c97f093c2664f8183690d4bf957';

    $imagePath = __DIR__ . '/examples/media/1.jpg';
//	tktest002 @tktest002

    $token   = '8656070000:AAHxBUslqpxjXLHXSf2qQKb-TA6OEIHD000';
    $channel = -1003819901000;
    $group   = -1003807130000;

    $chatId = 5667920000;
    $fileId = 'AgACAgUAAxUAAWmux6C3PBY5PoFt4svw000ZC3FTAAKSD2sbKvtwVXv82mQwM925AQADAgADYQADNgQ';
