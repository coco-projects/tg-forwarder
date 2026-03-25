<?php

    namespace Coco\tgForwarder\Commands;

    use Coco\tgForwarder\UpdateMessage;
    use Telegram\Bot\Objects\Update;

    class InfoHandler extends BaseCommandHandler
    {
        protected string $name        = 'info';
        protected array  $aliases     = [];
        protected string $description = '';

        public function handle()
        {
            if (!$this->initCommon())
            {
                return;
            }

            $msgTable      = $this->forwarderManager->getMessageTable();
            $customerTable = $this->forwarderManager->getCustomerTable();
            $botMapTable   = $this->forwarderManager->getBotMapTable();

            /********************************************************/

            /*
            [
                'id'                => 1217813618469375624,
                'user_id'           => 5977238492,
                'first_name'        => 'wangwang',
                'last_name'         => 'tests',
                'username'          => 'wangwang_tests',
                'message_thread_id' => 81,
                'bot_map_id'        => 1217812680438452122,
                'is_blocked'        => 1,
                'is_fraud'          => 0,
                'remark'            => null,
                'time'              => 1773376592,
                'bot_token'         => '8656070036:AAHxBUslqpxjXLHXSf2qQKb-TA6OEIHDFu0',
                'bot_token_hash'    => 'd5d84c97f093c2664f8183690d4bf957',
                'group_id'          => -1003807130395,
                'admin_ids'         => '',
                'is_enable'         => 1,
                'is_webhook_set'    => 1,
            ];*/

            //群组回给用户
            if ($this->chatType == 'supergroup')
            {
                $this->replyWithMessage([
                    'parse_mode'        => 'HTML',
                    'message_thread_id' => $this->userInfo['message_thread_id'],
                    'text'              => implode(PHP_EOL, [
                        '<b>内部ID：</b>' . htmlspecialchars($this->userInfo['id']),
                        '<b>用户名：</b>' . '@' . htmlspecialchars($this->userInfo['username']),
                        '<b>名字：</b>' . htmlspecialchars($this->userInfo['first_name']),
                        '<b>姓氏：</b>' . htmlspecialchars($this->userInfo['last_name']),
                        '<b>消息线程ID：</b>' . htmlspecialchars($this->userInfo['message_thread_id']),
                        '<b>是否被封禁：</b>' . ($this->userInfo['is_blocked'] ? '✅' : '❌'),
                        '<b>是否为欺诈：</b>' . ($this->userInfo['is_fraud'] ? '✅' : '❌'),
                        '<b>注册时间：</b>' . htmlspecialchars(date('Y-m-d H:i:s', $this->userInfo['time'])),
                        '<b>备注：</b>' . PHP_EOL . implode(PHP_EOL, json_decode($this->userInfo['remark'], 1)),
                    ]),

                ]);
            }
        }
    }