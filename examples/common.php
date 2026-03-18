<?php

    require __DIR__ . '/../vendor/autoload.php';
    require __DIR__ . '/../config.php';

    $forwarder = new \Coco\tgForwarder\Proxy($config);

    $manager = $forwarder->forwarder;

    $bot = $forwarder->setHash($hash)->getTelegramClient();

