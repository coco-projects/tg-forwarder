<?php

    require '../vendor/autoload.php';
    require './common.php';

    $data = $manager->getUserInfo('2222', '1217432301365887170');

    print_r($data);
    exit;;