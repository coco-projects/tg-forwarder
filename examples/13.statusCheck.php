<?php

    require '../vendor/autoload.php';
    require './common.php';

    $data = $manager->statusCheck($token, $group);

    print_r($data);
