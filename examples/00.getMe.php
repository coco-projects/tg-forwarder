<?php

    require '../vendor/autoload.php';

    require './common.php';

    $response = $bot->getMe();

    $botId     = $response->getId();
    $firstName = $response->getFirstName();
    $username  = $response->getUsername();

    echo $botId;
    echo PHP_EOL;

    echo $firstName;
    echo PHP_EOL;

    echo $username;
    echo PHP_EOL;

    print_r($response);
/*
Telegram\Bot\Objects\User Object
(
    [items:protected] => Array
        (
            [id] => 8656070036
            [is_bot] => 1
            [first_name] => ceshibbt1
            [username] => tgg_dev1_bot
            [can_join_groups] => 1
            [can_read_all_group_messages] => 1
            [supports_inline_queries] =>
            [can_connect_to_business] =>
            [has_main_web_app] =>
        )

    [escapeWhenCastingToString:protected] =>
)

*/