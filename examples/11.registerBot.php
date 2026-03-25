<?php

    require '../vendor/autoload.php';
    require './common.php';

//    $forwarder->addBot($token, $group);
//    $forwarder->registerBot($token);
//    $forwarder->disableBot($token);
    $forwarder->enableBot($token);
    //    $forwarder->unregisterBot($token);
    var_dump($forwarder->isBotEnabled($token));


