<?php

    require '../vendor/autoload.php';
    require './common.php';

    try
    {
        $update = $forwarder->setHash($hash)->webHookEndpoint();
    }
    catch (\Exception $e)
    {

    }


    /*
Telegram\Bot\Objects\Update Object
(
    [items:protected] => Array
        (
            [update_id] => 965722044
            [message] => Array
                (
                    [message_id] => 1
                    [from] => Array
                        (
                            [id] => 5667920492
                            [is_bot] =>
                            [first_name] => source-1
                            [username] => wangwang_source_1800a
                            [language_code] => id
                        )

                    [chat] => Array
                        (
                            [id] => 5667920492
                            [first_name] => source-1
                            [username] => wangwang_source_1800a
                            [type] => private
                        )

                    [date] => 1773068760
                    [text] => /start
                    [entities] => Array
                        (
                            [0] => Array
                                (
                                    [offset] => 0
                                    [length] => 6
                                    [type] => bot_command
                                )

                        )

                )

        )

    [escapeWhenCastingToString:protected] =>
    [updateType:protected] => message
)

     */