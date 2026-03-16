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
