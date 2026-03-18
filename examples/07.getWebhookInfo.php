<?php

    require '../vendor/autoload.php';
    require './common.php';

    $response = $bot->getWebhookInfo();

    print_r($response);;;
/*
Telegram\Bot\Objects\WebhookInfo Object
(
    [items:protected] => Array
        (
            [url] => https://bottest.7yunchiyun.com/index.php
            [has_custom_certificate] =>
            [pending_update_count] => 0
            [max_connections] => 100
            [ip_address] => 2.59.151.132
            [last_synchronization_error_date] => 1773799824
        )

    [escapeWhenCastingToString:protected] =>
)

 * */