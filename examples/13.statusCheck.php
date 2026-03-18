<?php

    require '../vendor/autoload.php';
    require './common.php';

    $data = $manager->statusCheck($token, $group);

    print_r($data);

    /*


Array
(
    [ok] => 1
    [msg] => Array
        (
            [0] => 所有核心检查通过（但请注意非必须权限的缺失警告）
        )

    [status] => Array
        (
            [bot] => Array
                (
                    [status] => administrator
                    [can_be_edited] =>
                    [can_manage_chat] => 1
                    [can_change_info] => 1
                    [can_delete_messages] => 1
                    [can_invite_users] => 1
                    [can_restrict_members] => 1
                    [can_pin_messages] => 1
                    [can_manage_topics] => 1
                    [can_promote_members] =>
                    [can_manage_video_chats] => 1
                    [can_post_stories] => 1
                    [can_edit_stories] => 1
                    [can_delete_stories] => 1
                    [is_anonymous] =>
                    [can_manage_voice_chats] => 1
                    [privacy_mode_note] => 机器人是管理员 → 自动忽略隐私模式，可收到群内所有消息（包括普通文本、非命令消息）
                )

            [chat] => Array
                (
                    [title] => tktest002
                    [type] => supergroup
                    [username] => tktest002
                    [is_forum] => 1
                )

        )

)


    */