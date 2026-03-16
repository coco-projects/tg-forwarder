<?php

    declare(strict_types = 1);

    namespace Coco\tgForwarder\tables;

    use Coco\tableManager\TableAbstract;

    class BotMap extends TableAbstract
    {
        public string $comment = '机器人和转发到指定群组映射表';

        public array $fieldsSqlMap = [
            "bot_token"      => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT '用户联系的机器人token',",
            "bot_uid"        => "`__FIELD__NAME__` bigint(11) NOT NULL DEFAULT '0' COMMENT '机器人id',",
            "bot_token_hash" => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT '用户联系的机器人token的hash',",
            "admin_ids"      => "`__FIELD__NAME__` char(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '' COMMENT '管理员身份的用户id,逗号分割：5667920492,5667920491',",
            "group_id"       => "`__FIELD__NAME__` bigint(11) NOT NULL DEFAULT '0' COMMENT '转发到指定群组',",
            "is_webhook_set" => "`__FIELD__NAME__` tinyint(11) unsigned NOT NULL DEFAULT '0' COMMENT '0:没设置webhook, 1:已经设置',",
            "is_enable"      => "`__FIELD__NAME__` tinyint(11) unsigned NOT NULL DEFAULT '1' COMMENT '0:已经关闭, 1:正常',",
            "time"           => "`__FIELD__NAME__` int(10) unsigned NOT NULL DEFAULT '0',",
        ];

        protected array $indexSentence = [
            "bot_token" => "KEY `__INDEX__NAME___index` ( __FIELD__NAME__ ),",
            "group_id"  => "KEY `__INDEX__NAME___index` ( __FIELD__NAME__ ),",
        ];

        public function setBotTokenField(string $value): static
        {
            $this->setFeildName('bot_token', $value);

            return $this;
        }

        public function getBotTokenField(): string
        {
            return $this->getFieldName('bot_token');
        }

        public function setBotUidField(string $value): static
        {
            $this->setFeildName('bot_uid', $value);

            return $this;
        }

        public function getBotUidField(): string
        {
            return $this->getFieldName('bot_uid');
        }

        public function setBotTokenHashField(string $value): static
        {
            $this->setFeildName('bot_token_hash', $value);

            return $this;
        }

        public function getBotTokenHashField(): string
        {
            return $this->getFieldName('bot_token_hash');
        }

        public function setAdminIdsField(string $value): static
        {
            $this->setFeildName('admin_ids', $value);

            return $this;
        }

        public function getAdminIdsField(): string
        {
            return $this->getFieldName('admin_ids');
        }

        public function setGroupIdField(string $value): static
        {
            $this->setFeildName('group_id', $value);

            return $this;
        }

        public function getGroupIdField(): string
        {
            return $this->getFieldName('group_id');
        }

        public function setIsWebhookSetField(string $value): static
        {
            $this->setFeildName('is_webhook_set', $value);

            return $this;
        }

        public function getIsWebhookSetField(): string
        {
            return $this->getFieldName('is_webhook_set');
        }

        public function setIsEnableField(string $value): static
        {
            $this->setFeildName('is_enable', $value);

            return $this;
        }

        public function getIsEnableField(): string
        {
            return $this->getFieldName('is_enable');
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
