<?php

    require '../vendor/autoload.php';

    require './common.php';

    $response = $bot->setWebhook([
        'url' => 'https://bottest.7yunchiyun.com/index.php',
    ]);

    print_r($response);
