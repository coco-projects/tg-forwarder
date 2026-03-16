<?php

    require '../vendor/autoload.php';

    require './common.php';

    $res = $manager->getBotsList();
    print_r($res);