<?php

    require '../vendor/autoload.php';
    require './common.php';

    $forwarder->addAdminId($token, [
        5977238492,
    ]);

    /*
        $forwarder->removeAdminId($token, [
            5977238492,
        ]);
    */

    $res = $forwarder->getAdminId($token);
    print_r($res);

    /*
    Array
    (
        [0] => 5977238492
    )

    */