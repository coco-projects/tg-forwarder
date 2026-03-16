<?php

    declare(strict_types = 1);

    namespace Coco\tgForwarder\tables;

    use Coco\tableManager\TableAbstract;

    class Customer extends TableAbstract
    {
        public string $comment = '用户信息表';

        public array $fieldsSqlMap = [
            "user_id" => "`__FIELD__NAME__` bigint(11) NOT NULL DEFAULT '0' COMMENT '用户id',",

            "first_name" => "`__FIELD__NAME__` text COLLATE utf8mb4_unicode_520_ci COMMENT '昵称',",
            "last_name"  => "`__FIELD__NAME__` text COLLATE utf8mb4_unicode_520_ci COMMENT '昵称',",
            "username"   => "`__FIELD__NAME__` text COLLATE utf8mb4_unicode_520_ci COMMENT '账号',",

            "message_thread_id" => "`__FIELD__NAME__` bigint(11) NOT NULL DEFAULT '0' COMMENT '群里的topics_id',",

            "bot_map_id" => "`__FIELD__NAME__` bigint(11) NOT NULL DEFAULT '0' COMMENT 'bot_map 主键id',",

            "is_blocked" => "`__FIELD__NAME__` tinyint(11) unsigned NOT NULL DEFAULT '0' COMMENT '0:未封禁, 1:已封禁',",
            "is_fraud"   => "`__FIELD__NAME__` tinyint(11) unsigned NOT NULL DEFAULT '0' COMMENT '0:正常, 1:是骗子',",
            "remark"     => "`__FIELD__NAME__` text COLLATE utf8mb4_unicode_520_ci COMMENT '封禁或者骗子的备注',",

            "time" => "`__FIELD__NAME__` int(10) unsigned NOT NULL DEFAULT '0',",
        ];

        protected array $indexSentence = [
            "bot_map_id"           => "KEY `__INDEX__NAME___index` ( __FIELD__NAME__ ),",
            "user_id"           => "KEY `__INDEX__NAME___index` ( __FIELD__NAME__ ),",
            "message_thread_id" => "KEY `__INDEX__NAME___index` ( __FIELD__NAME__ ),",
        ];

        public function setUserIdField(string $value): static
        {
            $this->setFeildName('user_id', $value);

            return $this;
        }

        public function getUserIdField(): string
        {
            return $this->getFieldName('user_id');
        }

        public function setFirstNameField(string $value): static
        {
            $this->setFeildName('first_name', $value);

            return $this;
        }

        public function getFirstNameField(): string
        {
            return $this->getFieldName('first_name');
        }

        public function setLastNameField(string $value): static
        {
            $this->setFeildName('last_name', $value);

            return $this;
        }

        public function getLastNameField(): string
        {
            return $this->getFieldName('last_name');
        }

        public function setUsernameField(string $value): static
        {
            $this->setFeildName('username', $value);

            return $this;
        }

        public function getUsernameField(): string
        {
            return $this->getFieldName('username');
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

        public function setBotMapIdField(string $value): static
        {
            $this->setFeildName('bot_map_id', $value);

            return $this;
        }

        public function getBotMapIdField(): string
        {
            return $this->getFieldName('bot_map_id');
        }

        public function setIsBlockedField(string $value): static
        {
            $this->setFeildName('is_blocked', $value);

            return $this;
        }

        public function getIsBlockedField(): string
        {
            return $this->getFieldName('is_blocked');
        }

        public function setIsFraudField(string $value): static
        {
            $this->setFeildName('is_fraud', $value);

            return $this;
        }

        public function getIsFraudField(): string
        {
            return $this->getFieldName('is_fraud');
        }

        public function setRemarkField(string $value): static
        {
            $this->setFeildName('remark', $value);

            return $this;
        }

        public function getRemarkField(): string
        {
            return $this->getFieldName('remark');
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
