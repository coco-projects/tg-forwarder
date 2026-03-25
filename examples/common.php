<?php

    require __DIR__ . '/../vendor/autoload.php';
    require __DIR__ . '/../config.php';

    $proxy     = new \Coco\tgForwarder\Proxy($config);
    $forwarder = $proxy->forwarder;
    $bot       = $proxy->setHash($hash)->getTelegramClient();

