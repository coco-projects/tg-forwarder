<?php

    namespace Coco\tgForwarder\Commands;

    use Coco\tgForwarder\UpdateMessage;
    use Telegram\Bot\Objects\Update;

    class UnBlockHandler extends BaseCommandHandler
    {
        protected string $name        = 'unblock';
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
                if ($this->isAdmin($this->chatIdToForwarder))
                {
                    //管理
                    $msg = $this->replaceTemplate($this->forwarderManager->getTemplate('unban_user_admin'));
                }
                else
                {
                    //普通用户
                    $msg = $this->replaceTemplate($this->forwarderManager->getTemplate('unban_user'));

                    $this->forwarderManager->unBlockUser($this->chatIdToForwarder, $this->botMapInfo[$customerTable->getPkField()]);
                    $this->forwarderManager->pushUserRemark($this->chatIdToForwarder, $this->botMapInfo[$customerTable->getPkField()], '用户被解除封禁');
                }

                $this->replyWithMessage([
                    'text'              => $msg,
                    'message_thread_id' => $this->message->message_thread_id,
                ]);
            }

        }

    }