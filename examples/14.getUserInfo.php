<?php

    require '../vendor/autoload.php';
    require './common.php';

    $data = $forwarder->getUserInfo(5977238492, '1218919484316715176');

    print_r($data);
    /*
Array
(
    [id] => 1218919663375748296
    [user_id] => 5977238492
    [first_name] => wangwang
    [last_name] => tests
    [username] => wangwang_tests
    [message_thread_id] => 341
    [bot_map_id] => 1218919484316715176
    [is_blocked] => 0
    [is_fraud] => 0
    [remark] => ["【2026-03-16 13:57:57】:信息包含违禁词：阿拉丁 \n---------【信息内容】---------\n阿拉丁 (1992) #经典迪士尼 #奇幻冒险 #必看动画 经典动画的独家巅峰之作，充满异域风情的奇幻冒险。神《阿拉丁 (1992)》灯精灵的幽默表演与动人音乐，带来无尽欢乐与感动。 2025-10-21 17:10:30 🔗 https:\/\/pan.quark.cn\/s\/5cb25ccf5ff5\n------------------------------------------------------","【2026-03-16 13:59:05】:手动点击，没有备注","【2026-03-16 13:59:47】:用户被解除封禁"]
    [time] => 1773640294
    [bot_token] => 8656070036:AAHxBUslqpxjXLHXSf2qQKb-TA6OEIHDFu0
    [bot_token_hash] => d5d84c97f093c2664f8183690d4bf957
    [group_id] => -1003807130395
    [admin_ids] =>
    [is_enable] => 1
    [is_webhook_set] => 1
)

     * */