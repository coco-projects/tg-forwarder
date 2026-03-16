<?php

    namespace Coco\tgForwarder\Commands;

    use Coco\tgForwarder\UpdateMessage;
    use Telegram\Bot\Objects\Update;

    class BlockHandler extends BaseCommandHandler
    {
        protected string $name        = 'block';
        protected string $pattern     = '{remark}';
        protected string $description = '禁用用户';

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
                $remark = $this->argument('remark', '手动点击，没有备注');

                if ($this->isAdmin($this->chatIdToForwarder))
                {
                    //管理
                    $msg = $this->replaceTemplate($this->forwarderManager->getTemplate('ban_user_admin'));
                }
                else
                {
                    //普通用户
                    $msg = $this->replaceTemplate($this->forwarderManager->getTemplate('ban_user'));

                    $this->forwarderManager->blockUser($this->chatIdToForwarder, $this->botMapInfo[$customerTable->getPkField()]);
                    $this->forwarderManager->pushUserRemark($this->chatIdToForwarder, $this->botMapInfo[$customerTable->getPkField()], $remark);
                }

                $this->replyWithMessage([
                    'text'              => $msg,
                    'message_thread_id' => $this->message->message_thread_id,
                ]);
            }

        }

    }