<?php

    require '../vendor/autoload.php';

    require './common.php';

    $response = $bot->setWebhook([
        'url' => 'https://bottest.xxx.com/index.php',
    ]);

    print_r($response);
