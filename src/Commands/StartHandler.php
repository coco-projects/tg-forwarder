<?php

    namespace Coco\tgForwarder\Commands;

    class StartHandler extends BaseCommandHandler
    {
        protected string $name        = 'start';
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
            if ($this->chatType == 'private')
            {
                //添加用户
                $this->forwarderManager->addUser($this->chatId, $this->botMapInfo[$customerTable->getPkField()]);

                $userInfo = $this->forwarderManager->getUserInfo($this->chatId, $this->botMapInfo[$customerTable->getPkField()]);

                $userInfoToEdit = [
                    $customerTable->getFirstNameField() => $this->message->from->first_name,
                    $customerTable->getLastNameField()  => $this->message->from->last_name,
                    $customerTable->getUsernameField()  => $this->chatUsername,
                ];

                if (!$userInfo[$customerTable->getMessageThreadIdField()])
                {
                    //创建用户topics，更新到用户表
                    $topic = $this->telegram->createForumTopic([
                        'chat_id'    => $this->chatIdToForwarder,
                        'name'       => $this->fromUsername,
                        'icon_color' => static::getRandomIconColor(),
                    ]);

                    $this->telegram->setMyCommands([
                        'commands' => json_encode([
                            [
                                'command'     => 'help',
                                'description' => '📋查看帮助和命令行表',
                            ],
                            [
                                'command'     => 'info',
                                'description' => '👤查看当前用户资料',
                            ],
                            [
                                'command'     => 'block',
                                'description' => '🚫屏蔽当前用户',
                            ],
                            [
                                'command'     => 'unblock',
                                'description' => '✅解除屏蔽当前用户',
                            ],
                        ]),
                        'scope'    => json_encode([
                            'type'    => 'chat',
                            'chat_id' => $this->chatIdToForwarder,
                        ]),
                    ]);

                    $userInfoToEdit[$customerTable->getMessageThreadIdField()] = $topic->getMessageThreadId();
                }

                //更新用户信息
                $this->forwarderManager->updateUserInfo($this->chatId, $this->botMapInfo[$customerTable->getPkField()], $userInfoToEdit);

                $msg = $this->replaceTemplate($this->forwarderManager->getTemplate('start'));

                if ($this->isAdmin($this->chatId))
                {
                    //是管理使用bot
                    $msg = "【管理员】" . $msg;
                }
                else
                {
                    //普通用户
                }

                $this->replyWithMessage([
                    'text' => $msg,
                    //                'message_thread_id' => '',
                    //                'chat_id'           => '',
                    //                'parse_mode'                  => '',
                    //                'entities'                    => '',
                    //                'disable_web_page_preview'    => '',
                    //                'protect_content'             => '',
                    //                'disable_notification'        => '',
                    //                'reply_to_message_id'         => '',
                    //                'allow_sending_without_reply' => '',
                    //                'reply_markup'                => '',

                ]);
            }
        }
    }