<?php

    namespace Coco\tgForwarder\Commands;

    use Coco\tgForwarder\UpdateMessage;
    use Telegram\Bot\Objects\Update;

    class HelpHandler extends BaseCommandHandler
    {
        protected string $name        = 'help';
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

            //群组回给用户
            if ($this->chatType == 'supergroup')
            {
                $this->replyWithMessage([
                    'message_thread_id' => $this->message->message_thread_id,
                    'text'              => implode(PHP_EOL, [
                        '功能列表：',
                        '/help ：查看当前功能列表',
                        '/info ：查看当前用户信息',
                        '/block ：禁用当前用户',
                        '/unblock ：解除禁用当前用户',
                    ]),
                ]);
            }
        }
    }