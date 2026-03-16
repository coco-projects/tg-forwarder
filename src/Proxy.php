<?php

    namespace Coco\tgForwarder;

    class Proxy
    {
        public Forwarder $forwarder;

        public function __construct(public $config, public string $hash)
        {
            $this->init();
        }

        protected function init(): void
        {
            $this->forwarder = new Forwarder($this->config['webhookBase'], $this->config['baseBotUrl'],);
            $this->forwarder->setDebug($this->config['debug']);
            $this->forwarder->setMsgTemplate($this->config['template']);
            $this->forwarder->setBlockWordList($this->config['blockWordList']);

            $this->forwarder->setRedisConfig($this->config['redisHost'], $this->config['redisPassword'], $this->config['redisPort'], $this->config['redisDb']);
            $this->forwarder->setMysqlConfig($this->config['mysqlDb'], $this->config['mysqlHost'], $this->config['mysqlUsername'], $this->config['mysqlPassword'], $this->config['mysqlPort']);

            $this->forwarder->setEnableEchoLog($this->config['enableEchoLog']);
            $this->forwarder->setEnableRedisLog($this->config['enableRedisLog']);

            $this->forwarder->initServer();

            $this->forwarder->initMessageTable('te_message', function(\Coco\tgForwarder\tables\Message $table) {
                $registry = $table->getTableRegistry();

                $table->setPkField('id');
                $table->setIsPkAutoInc(false);
                $table->setPkValueCallable($registry::snowflakePKCallback());
            });

            $this->forwarder->initCustomerTable('te_customer', function(\Coco\tgForwarder\tables\Customer $table) {
                $registry = $table->getTableRegistry();

                $table->setPkField('id');
                $table->setIsPkAutoInc(false);
                $table->setPkValueCallable($registry::snowflakePKCallback());
            });

            $this->forwarder->initBotMapTable('te_bot_map', function(\Coco\tgForwarder\tables\BotMap $table) {
                $registry = $table->getTableRegistry();

                $table->setPkField('id');
                $table->setIsPkAutoInc(false);
                $table->setPkValueCallable($registry::snowflakePKCallback());
            });

            $this->forwarder->initBotsManager();

            $this->forwarder->makeForwarderConfig();

        }


        public function getAllConfig()
        {
            return $this->config;
        }

        public function getConfigItem(string $key)
        {
            if (isset($this->config[$key]))
            {
                return $this->config[$key];
            }

            return null;
        }

        public function webHookEndpoint(): void
        {
            $this->forwarder->webHookEndpoint($this->hash);
        }

        public function getTelegramClient(): \Telegram\Bot\Api
        {
            return $this->forwarder->getBotsManager($this->hash);
        }
    }
