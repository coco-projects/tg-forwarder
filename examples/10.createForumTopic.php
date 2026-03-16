<?php

    require '../vendor/autoload.php';
    require './common.php';

    $response = $bot->createForumTopic([
        // int|string - Required. Unique identifier for the target chat or username of the target supergroup (in the format "@supergroupusername")
        'chat_id'    => $group,
        // string     - Required. Topic name, 1-128 characters
        'name'       => 'Topic name-' . time(),
        // int        - (Optional). Color of the topic icon in RGB format. Currently,
        // must be one of 7322096 (0x6FB9F0), 16766590 (0xFFD67E), 13338331 (0xCB86DB), 9367192 (0x8EEE98), 16749490 (0xFF93B2), or 16478047 (0xFB6F5F)
        'icon_color' => 0x6FB9F0,
    ]);

    $topic = $response;

    echo "话题创建成功！\n";
    echo "Message Thread ID（话题ID）: " . $topic->getMessageThreadId() . "\n";
    echo "话题名称: " . $topic->getName() . "\n";
    echo "图标颜色: " . $topic->getIconColor() . "\n";

    print_r($response);

    /*
话题创建成功！
Message Thread ID（话题ID）: 5
话题名称: Topic name-1773119090
图标颜色: 7322096

Telegram\Bot\Objects\ForumTopic Object
(
    [items:protected] => Array
        (
            [message_thread_id] => 5
            [name] => Topic name-1773119090
            [icon_color] => 7322096
        )

    [escapeWhenCastingToString:protected] =>
)

     */


