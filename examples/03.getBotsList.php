<?php

    require '../vendor/autoload.php';

    require './common.php';

    $res = $forwarder->getBotsList();
    print_r($res);

    /*
think\Collection Object
(
    [items:protected] => Array
        (
            [0] => Array
                (
                    [id] => 1218919484316715176
                    [bot_token] => 8656070036:AAHxBUslqpxjXLHXSf2qQKb-TA6OEIHDFu0
                    [bot_uid] => 8656070036
                    [bot_token_hash] => d5d84c97f093c2664f8183690d4bf957
                    [admin_ids] => 5977238492
                    [group_id] => -1003807130395
                    [is_webhook_set] => 1
                    [is_enable] => 1
                    [time] => 1773640251
                )

        )

)


     * */