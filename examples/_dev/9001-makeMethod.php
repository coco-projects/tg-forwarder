<?php

    use Coco\tableManager\TableRegistry;

    require '../common.php';

//    $method = TableRegistry::makeMethod($manager->getMessageTable()->getFieldsSqlMap());
    $method = TableRegistry::makeMethod($manager->getBotMapTable()->getFieldsSqlMap());
//    $method = TableRegistry::makeMethod($manager->getCustomerTable()->getFieldsSqlMap());

    print_r($method);
