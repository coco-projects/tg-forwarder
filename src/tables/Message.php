<?php

    declare(strict_types = 1);

    namespace Coco\tgForwarder\tables;

    use Coco\tableManager\TableAbstract;

    class Message extends TableAbstract
    {
        public string $comment = '机器人消息表';

        public array $fieldsSqlMap = [
            "bot_map_id" => "`__FIELD__NAME__` bigint(20) NOT NULL DEFAULT '0' COMMENT 'bot_map 主键id',",
            "update_id"  => "`__FIELD__NAME__` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'update_id（965722105）',",
            "message_id" => "`__FIELD__NAME__` bigint(20) NOT NULL DEFAULT '0' COMMENT '当前消息在群内的 message_id（61）',",

            "reply_to_group_message_id" => "`__FIELD__NAME__` bigint(20) NOT NULL DEFAULT '0' COMMENT '被回复的消息在群里对应的 message_id（57，用于 copyMessage 的 reply_to_message_id）',",

            "from_id"         => "`__FIELD__NAME__` bigint(20) NOT NULL DEFAULT '0' COMMENT '当前发言者ID（5667920492）',",
            "from_first_name" => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT '当前发言者 first_name（source-1）',",
            "from_last_name"  => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT '当前发言者 last_name',",
            "from_username"   => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT '当前发言者 username（wangwang_source_1800a）',",
            "from_is_bot"     => "`__FIELD__NAME__` tinyint(11) unsigned NOT NULL DEFAULT '0' COMMENT '当前发言者是否为bot（0）',",

            "chat_id"         => "`__FIELD__NAME__` bigint(20) NOT NULL DEFAULT '0' COMMENT '群组 chat_id（-1003807130395）',",
            "chat_first_name" => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT '群组 first_name（通常为空）',",
            "chat_last_name"  => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT '群组 last_name（通常为空）',",
            "chat_username"   => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT '群组 username（tktest002）',",
            "chat_type"       => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT '群组类型（supergroup）',",
            "date"            => "`__FIELD__NAME__` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前消息发送时间（1773305209）',",

            "reply_to_from_id"         => "`__FIELD__NAME__` bigint(20) NOT NULL DEFAULT '0' COMMENT '被回复消息的发送者ID（8656070036，机器人）',",
            "reply_to_from_first_name" => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT '被回复消息发送者 first_name（ceshibbt1）',",
            "reply_to_from_last_name"  => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT '被回复消息发送者 last_name',",
            "reply_to_from_username"   => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT '被回复消息发送者 username（tgg_dev1_bot）',",
            "reply_to_from_is_bot"     => "`__FIELD__NAME__` tinyint(11) unsigned NOT NULL DEFAULT '0' COMMENT '被回复消息发送者是否为bot（1）',",

            "reply_to_chat_id"         => "`__FIELD__NAME__` bigint(20) NOT NULL DEFAULT '0' COMMENT '被回复消息所在的 chat_id（-1003807130395）',",
            "reply_to_chat_first_name" => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT '被回复消息 chat first_name（通常为空）',",
            "reply_to_chat_last_name"  => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT '被回复消息 chat last_name（通常为空）',",
            "reply_to_chat_username"   => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT '被回复消息 chat username（tktest002）',",
            "reply_to_chat_type"       => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT '被回复消息 chat 类型（supergroup）',",
            "reply_date"               => "`__FIELD__NAME__` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '被回复消息的发送时间（1773305139）',",

            "is_topic_message"  => "`__FIELD__NAME__` tinyint(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否为话题消息（1）',",
            "message_thread_id" => "`__FIELD__NAME__` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '话题ID（55）',",

            "media_group_id"    => "`__FIELD__NAME__` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'media_group_id',",
            "message_load_type" => "`__FIELD__NAME__` tinyint(11) unsigned NOT NULL DEFAULT '0' COMMENT 'text=1, video=2, photo=3, audio=4, document=5, animation=6, sticker=7, location=8, contact=9, news=10, poll=11',",
            "message_from_type" => "`__FIELD__NAME__` tinyint(11) unsigned NOT NULL DEFAULT '0' COMMENT '1:message, 2:edited_message, 3:channel_post, 4:edited_channel_post',",
            "file_id"           => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT 'file_id',",
            "file_unique_id"    => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT 'file_unique_id',",
            "file_size"         => "`__FIELD__NAME__` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'file_size',",
            "file_name"         => "`__FIELD__NAME__` text COLLATE utf8mb4_unicode_520_ci COMMENT '文件名',",
            "caption"           => "`__FIELD__NAME__` longtext COLLATE utf8mb4_unicode_520_ci COMMENT '标题',",
            "mime_type"         => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT 'mime_type',",
            "media_type"        => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT 'media_type',",
            "ext"               => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT '后缀',",
            "text"              => "`__FIELD__NAME__` longtext COLLATE utf8mb4_unicode_520_ci COMMENT 'text 信息（管理回复用户信息1）',",
            "hashtags"          => "`__FIELD__NAME__` text COLLATE utf8mb4_unicode_520_ci COMMENT 'hashtags',",
            "raw"               => "`__FIELD__NAME__` longtext COLLATE utf8mb4_unicode_520_ci COMMENT '原生json',",
            "time"              => "`__FIELD__NAME__` int(10) unsigned NOT NULL DEFAULT '0',",
        ];

        protected array $indexSentence = [
            "file_id"           => "KEY `__INDEX__NAME___index` ( __FIELD__NAME__ ),",
            "update_id"         => "KEY `__INDEX__NAME___index` ( __FIELD__NAME__ ),",
            "from_id"           => "KEY `__INDEX__NAME___index` ( __FIELD__NAME__ ),",
            "chat_id"           => "KEY `__INDEX__NAME___index` ( __FIELD__NAME__ ),",
            "media_group_id"    => "KEY `__INDEX__NAME___index` ( __FIELD__NAME__ ),",
            "message_load_type" => "KEY `__INDEX__NAME___index` ( __FIELD__NAME__ ),",
            "message_from_type" => "KEY `__INDEX__NAME___index` ( __FIELD__NAME__ ),",
        ];

        public function setBotMapIdField(string $value): static
        {
            $this->setFeildName('bot_map_id', $value);

            return $this;
        }

        public function getBotMapIdField(): string
        {
            return $this->getFieldName('bot_map_id');
        }

        public function setUpdateIdField(string $value): static
        {
            $this->setFeildName('update_id', $value);

            return $this;
        }

        public function getUpdateIdField(): string
        {
            return $this->getFieldName('update_id');
        }

        public function setMessageIdField(string $value): static
        {
            $this->setFeildName('message_id', $value);

            return $this;
        }

        public function getMessageIdField(): string
        {
            return $this->getFieldName('message_id');
        }

        public function setReplyToGroupMessageIdField(string $value): static
        {
            $this->setFeildName('reply_to_group_message_id', $value);

            return $this;
        }

        public function getReplyToGroupMessageIdField(): string
        {
            return $this->getFieldName('reply_to_group_message_id');
        }

        public function setFromIdField(string $value): static
        {
            $this->setFeildName('from_id', $value);

            return $this;
        }

        public function getFromIdField(): string
        {
            return $this->getFieldName('from_id');
        }

        public function setFromFirstNameField(string $value): static
        {
            $this->setFeildName('from_first_name', $value);

            return $this;
        }

        public function getFromFirstNameField(): string
        {
            return $this->getFieldName('from_first_name');
        }

        public function setFromLastNameField(string $value): static
        {
            $this->setFeildName('from_last_name', $value);

            return $this;
        }

        public function getFromLastNameField(): string
        {
            return $this->getFieldName('from_last_name');
        }

        public function setFromUsernameField(string $value): static
        {
            $this->setFeildName('from_username', $value);

            return $this;
        }

        public function getFromUsernameField(): string
        {
            return $this->getFieldName('from_username');
        }

        public function setFromIsBotField(string $value): static
        {
            $this->setFeildName('from_is_bot', $value);

            return $this;
        }

        public function getFromIsBotField(): string
        {
            return $this->getFieldName('from_is_bot');
        }

        public function setChatIdField(string $value): static
        {
            $this->setFeildName('chat_id', $value);

            return $this;
        }

        public function getChatIdField(): string
        {
            return $this->getFieldName('chat_id');
        }

        public function setChatFirstNameField(string $value): static
        {
            $this->setFeildName('chat_first_name', $value);

            return $this;
        }

        public function getChatFirstNameField(): string
        {
            return $this->getFieldName('chat_first_name');
        }

        public function setChatLastNameField(string $value): static
        {
            $this->setFeildName('chat_last_name', $value);

            return $this;
        }

        public function getChatLastNameField(): string
        {
            return $this->getFieldName('chat_last_name');
        }

        public function setChatUsernameField(string $value): static
        {
            $this->setFeildName('chat_username', $value);

            return $this;
        }

        public function getChatUsernameField(): string
        {
            return $this->getFieldName('chat_username');
        }

        public function setChatTypeField(string $value): static
        {
            $this->setFeildName('chat_type', $value);

            return $this;
        }

        public function getChatTypeField(): string
        {
            return $this->getFieldName('chat_type');
        }

        public function setDateField(string $value): static
        {
            $this->setFeildName('date', $value);

            return $this;
        }

        public function getDateField(): string
        {
            return $this->getFieldName('date');
        }

        public function setReplyToFromIdField(string $value): static
        {
            $this->setFeildName('reply_to_from_id', $value);

            return $this;
        }

        public function getReplyToFromIdField(): string
        {
            return $this->getFieldName('reply_to_from_id');
        }

        public function setReplyToFromFirstNameField(string $value): static
        {
            $this->setFeildName('reply_to_from_first_name', $value);

            return $this;
        }

        public function getReplyToFromFirstNameField(): string
        {
            return $this->getFieldName('reply_to_from_first_name');
        }

        public function setReplyToFromLastNameField(string $value): static
        {
            $this->setFeildName('reply_to_from_last_name', $value);

            return $this;
        }

        public function getReplyToFromLastNameField(): string
        {
            return $this->getFieldName('reply_to_from_last_name');
        }

        public function setReplyToFromUsernameField(string $value): static
        {
            $this->setFeildName('reply_to_from_username', $value);

            return $this;
        }

        public function getReplyToFromUsernameField(): string
        {
            return $this->getFieldName('reply_to_from_username');
        }

        public function setReplyToFromIsBotField(string $value): static
        {
            $this->setFeildName('reply_to_from_is_bot', $value);

            return $this;
        }

        public function getReplyToFromIsBotField(): string
        {
            return $this->getFieldName('reply_to_from_is_bot');
        }

        public function setReplyToChatIdField(string $value): static
        {
            $this->setFeildName('reply_to_chat_id', $value);

            return $this;
        }

        public function getReplyToChatIdField(): string
        {
            return $this->getFieldName('reply_to_chat_id');
        }

        public function setReplyToChatFirstNameField(string $value): static
        {
            $this->setFeildName('reply_to_chat_first_name', $value);

            return $this;
        }

        public function getReplyToChatFirstNameField(): string
        {
            return $this->getFieldName('reply_to_chat_first_name');
        }

        public function setReplyToChatLastNameField(string $value): static
        {
            $this->setFeildName('reply_to_chat_last_name', $value);

            return $this;
        }

        public function getReplyToChatLastNameField(): string
        {
            return $this->getFieldName('reply_to_chat_last_name');
        }

        public function setReplyToChatUsernameField(string $value): static
        {
            $this->setFeildName('reply_to_chat_username', $value);

            return $this;
        }

        public function getReplyToChatUsernameField(): string
        {
            return $this->getFieldName('reply_to_chat_username');
        }

        public function setReplyToChatTypeField(string $value): static
        {
            $this->setFeildName('reply_to_chat_type', $value);

            return $this;
        }

        public function getReplyToChatTypeField(): string
        {
            return $this->getFieldName('reply_to_chat_type');
        }

        public function setReplyDateField(string $value): static
        {
            $this->setFeildName('reply_date', $value);

            return $this;
        }

        public function getReplyDateField(): string
        {
            return $this->getFieldName('reply_date');
        }

        public function setIsTopicMessageField(string $value): static
        {
            $this->setFeildName('is_topic_message', $value);

            return $this;
        }

        public function getIsTopicMessageField(): string
        {
            return $this->getFieldName('is_topic_message');
        }

        public function setMessageThreadIdField(string $value): static
        {
            $this->setFeildName('message_thread_id', $value);

            return $this;
        }

        public function getMessageThreadIdField(): string
        {
            return $this->getFieldName('message_thread_id');
        }

        public function setMediaGroupIdField(string $value): static
        {
            $this->setFeildName('media_group_id', $value);

            return $this;
        }

        public function getMediaGroupIdField(): string
        {
            return $this->getFieldName('media_group_id');
        }

        public function setMessageLoadTypeField(string $value): static
        {
            $this->setFeildName('message_load_type', $value);

            return $this;
        }

        public function getMessageLoadTypeField(): string
        {
            return $this->getFieldName('message_load_type');
        }

        public function setMessageFromTypeField(string $value): static
        {
            $this->setFeildName('message_from_type', $value);

            return $this;
        }

        public function getMessageFromTypeField(): string
        {
            return $this->getFieldName('message_from_type');
        }

        public function setFileIdField(string $value): static
        {
            $this->setFeildName('file_id', $value);

            return $this;
        }

        public function getFileIdField(): string
        {
            return $this->getFieldName('file_id');
        }

        public function setFileUniqueIdField(string $value): static
        {
            $this->setFeildName('file_unique_id', $value);

            return $this;
        }

        public function getFileUniqueIdField(): string
        {
            return $this->getFieldName('file_unique_id');
        }

        public function setFileSizeField(string $value): static
        {
            $this->setFeildName('file_size', $value);

            return $this;
        }

        public function getFileSizeField(): string
        {
            return $this->getFieldName('file_size');
        }

        public function setFileNameField(string $value): static
        {
            $this->setFeildName('file_name', $value);

            return $this;
        }

        public function getFileNameField(): string
        {
            return $this->getFieldName('file_name');
        }

        public function setMimeTypeField(string $value): static
        {
            $this->setFeildName('mime_type', $value);

            return $this;
        }

        public function getMimeTypeField(): string
        {
            return $this->getFieldName('mime_type');
        }

        public function setMediaTypeField(string $value): static
        {
            $this->setFeildName('media_type', $value);

            return $this;
        }

        public function getMediaTypeField(): string
        {
            return $this->getFieldName('media_type');
        }

        public function setExtField(string $value): static
        {
            $this->setFeildName('ext', $value);

            return $this;
        }

        public function getExtField(): string
        {
            return $this->getFieldName('ext');
        }

        public function setCaptionField(string $value): static
        {
            $this->setFeildName('caption', $value);

            return $this;
        }

        public function getCaptionField(): string
        {
            return $this->getFieldName('caption');
        }

        public function setTextField(string $value): static
        {
            $this->setFeildName('text', $value);

            return $this;
        }

        public function getTextField(): string
        {
            return $this->getFieldName('text');
        }

        public function setHashtagsField(string $value): static
        {
            $this->setFeildName('hashtags', $value);

            return $this;
        }

        public function getHashtagsField(): string
        {
            return $this->getFieldName('hashtags');
        }

        public function setRawField(string $value): static
        {
            $this->setFeildName('raw', $value);

            return $this;
        }

        public function getRawField(): string
        {
            return $this->getFieldName('raw');
        }

        public function setTimeField(string $value): static
        {
            $this->setFeildName('time', $value);

            return $this;
        }

        public function getTimeField(): string
        {
            return $this->getFieldName('time');
        }
    }
