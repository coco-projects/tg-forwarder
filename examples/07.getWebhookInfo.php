<?php

    require '../vendor/autoload.php';
    require './common.php';

    $response = $bot->getWebhookInfo();

    print_r($response);;;
