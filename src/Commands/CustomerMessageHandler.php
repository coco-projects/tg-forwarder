<?php

    namespace Coco\tgForwarder\Commands;

    class CustomerMessageHandler extends BaseCommandHandler
    {
        protected string $name        = 'internal_customer_message_handler';
        protected string $description = '内部使用：处理所有普通用户消息并转发';

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

            if ($this->message->reply_to_message)
            {
                if ($this->message->from->id //
                    ==//
                    $this->message->reply_to_message->from->id//
                )
                {
                    $reply_to_message_id = $this->forwarderManager->getEditedMessageId($this->message->reply_to_message->message_id, $this->fromId);
                }
                else
                {
                    $reply_to_message_id = $this->getReplyToMessageId();
                }
            }
            else
            {
                $reply_to_message_id = 0;
            }

            //用户发给群组
            if ($this->chatType == 'private')
            {
                if ($this->isBotEnable)
                {
                    $userInfo = $this->forwarderManager->getUserInfo($this->chatId, $this->botMapInfo[$customerTable->getPkField()]);

                    if ($userInfo[$customerTable->getIsFraudField()] == $this->forwarderManager::FRAUD_1)
                    {
                        //用户是骗子

                        //跟客服转发信息，带上被封锁的提示
                        //跟客户发信息提示系统繁忙

                        return;
                    }

                    if ($this->msgType == static::MSG_TYPE_MESSAGE)
                    {
                        //用户状态正常
                        $data = [
                            'chat_id'                     => $this->chatIdToForwarder,
                            'from_chat_id'                => $this->fromChatId,
                            'message_id'                  => $this->messageId,
                            'message_thread_id'           => $userInfo[$customerTable->getMessageThreadIdField()],
                            'reply_to_message_id'         => $reply_to_message_id,
                            'disable_notification'        => false,
                            'protect_content'             => false,
                            'allow_sending_without_reply' => true,
                        ];

                        //转发原文信息
                        $response = $this->telegram->copyMessage($data);
                        $this->forwarderManager->updateReplyToGroupMessageId($this->chatId, $this->messageId, $response->message_id);

                        //违禁词判断
                        $text = $this->message->text ?? $this->message->caption ?? '';
                        if ($text)
                        {
                            $words = $this->forwarderManager->isContainBadWord($text);

                            if (count($words))
                            {
                                //包含违禁词
                                //记录到remark
                                $msg = implode(PHP_EOL, [
                                    '信息包含违禁词：' . implode(',', $words),
                                    '---------【信息内容】---------',
                                    $text,
                                    '------------------------------------------------------',
                                ]);
                                $this->forwarderManager->pushUserRemark($this->chatId, $this->botMapInfo[$customerTable->getPkField()], $msg);

                                //回复管理员一条信息，引用上面那个信息，提示包含哪些违禁词
                                $msg = implode(PHP_EOL, [
                                    '信息包含违禁词：* ' . implode(',', $words) . ' *',
                                    '------------------',
                                    '禁封用户： /block',
                                    '查看用户信息： /info',
                                ]);
                                $this->telegram->sendMessage([
                                    'chat_id'             => $this->chatIdToForwarder,
                                    'message_thread_id'   => $userInfo[$customerTable->getMessageThreadIdField()],
                                    'reply_to_message_id' => $response->message_id,
                                    'text'                => $msg,
                                    'parse_mode'          => 'markdown',
                                ]);
                            }
                        }
                    }
                    elseif ($this->msgType == static::MSG_TYPE_EDITED_MESSAGE)
                    {
                        $newText = $this->message->text ?? $this->message->caption ?? '（非文本内容）';

                        // 2. 通知群内 Topic（引用原消息 + 显示“已编辑”）
                        $data = [
                            'chat_id'             => $this->chatIdToForwarder,
                            'message_thread_id'   => $userInfo[$customerTable->getMessageThreadIdField()],
                            'reply_to_message_id' => $this->forwarderManager->getEditedMessageId($this->message->message_id, $this->fromId),
                            'text'                => implode(PHP_EOL, [
                                '🔄用户编辑了上面这条消息：',
                                '---------------------',
                                $newText,
                            ]),
                            'parse_mode'          => 'markdown',
                        ];
                        $this->telegram->sendMessage($data);
                    }

                    if ($userInfo[$customerTable->getIsBlockedField()] == $this->forwarderManager::BLOCKED_1)
                    {
                        //用户被封锁了
                        $msg = implode(PHP_EOL, [
                            '* 🚫当前用户已经被封锁 *',
                            '解锁用户： /unblock',
                            '查看用户信息： /info',
                        ]);
                        $this->telegram->sendMessage([
                            'chat_id'             => $this->chatIdToForwarder,
                            'message_thread_id'   => $userInfo[$customerTable->getMessageThreadIdField()],
                            'text'                => $msg,
                            'parse_mode'          => 'markdown',
                        ]);
                    }
                }
                else
                {
                    //机器人服务关闭了，直接回复用户
                    $msg = $this->replaceTemplate($this->forwarderManager->getTemplate('bot_closed_user'));

                    $this->replyWithMessage([
                        'text' => $msg,
                    ]);
                }
            }

            //群组回给用户
            if ($this->chatType == 'supergroup')
            {
                if ($this->isBotEnable)
                {
                    //客服给用户发命令操作就不转发给客户
                    if (!$this->isMessageWseCommand())
                    {
                        if ($this->msgType == static::MSG_TYPE_MESSAGE)
                        {
                            $data = [
                                'chat_id'                     => $this->chatIdToForwarder,
                                'from_chat_id'                => $this->fromChatId,
                                'message_id'                  => $this->messageId,
                                'reply_to_message_id'         => $reply_to_message_id,
                                'disable_notification'        => false,
                                'protect_content'             => false,
                                'allow_sending_without_reply' => true,

                                'message_thread_id' => '',
                                'caption'           => '',
                                'parse_mode'        => '',
                                'caption_entities'  => '',
                                'reply_markup'      => '',
                            ];

                            $response = $this->telegram->copyMessage($data);
                            $this->forwarderManager->updateReplyToGroupMessageId($this->chatId, $this->messageId, $response->message_id);
                        }
                        elseif ($this->msgType == static::MSG_TYPE_EDITED_MESSAGE)
                        {
                            $newText = $this->message->text ?? $this->message->caption ?? '（非文本内容）';

                            $response = $this->telegram->editMessageText([
                                'chat_id'    => $this->chatIdToForwarder,
                                'message_id' => $this->forwarderManager->getEditedMessageId($this->messageId, $this->fromId),
                                'text'       => $newText,
                            ]);

                            $this->forwarderManager->updateEditedMessage($this->messageId, $this->fromId, $newText);
                        }
                    }
                }
                else
                {
                    //机器人服务关闭了，直接回复客服
                    $msg = $this->replaceTemplate($this->forwarderManager->getTemplate('bot_closed_admin'));

                    $this->replyWithMessage([
                        'text'              => $msg,
                        'message_thread_id' => $this->message->message_thread_id,
                    ]);
                }
            }

        }

    }