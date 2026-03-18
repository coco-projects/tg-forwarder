<?php

    require '../vendor/autoload.php';
    require './common.php';
    $manager->addAdminId($token, [
        5977238492,
    ]);
/*
    $manager->removeAdminId($token, [
        5977238492,
    ]);*/

    $res = $manager->getAdminId($token);
    print_r($res);

/*
Array
(
    [0] => 5977238492
)

*/