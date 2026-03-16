<?php

    require __DIR__ . '/../vendor/autoload.php';
    require __DIR__ . '/../config.php';

    $forwarder = new \Coco\tgForwarder\Proxy($config, $hash);

    $manager = $forwarder->forwarder;
    $bot     = $forwarder->getTelegramClient();

