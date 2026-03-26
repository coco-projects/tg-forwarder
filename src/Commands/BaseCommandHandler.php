<?php

    namespace Coco\tgForwarder\Commands;

    use Coco\tgForwarder\Forwarder;
    use Coco\tgForwarder\UpdateMessage;
    use Random\RandomException;
    use Telegram\Bot\Api;
    use Telegram\Bot\Commands\Command;
    use Telegram\Bot\Objects\Message;
    use Telegram\Bot\Objects\Update;

    abstract class BaseCommandHandler extends Command
    {
        public ?Message $message;

        public string $botToken;
        public array  $botMapInfo;
        public bool   $isBotEnable;

        //发这个信息的对话框，如果是私聊就是这个账号id
        //如果是群聊，就是群id -100xxxxxxxx
        public int $chatId;

        //发这个信息的账号 @xxxxxx，@后面这段
        public string $chatUsername;

        //private，用户发给群组
        //supergroup，还是群组发出给用户
        public string $chatType;

        public int    $fromId;
        public string $fromUsername;


        //要转发去的chat_id
        //对应参数的chat_id
        public ?string $chatIdToForwarder;

        //从哪个群转发
        //对应参数的 from_chat_id
        public string $fromChatId;

        //message_id
        //对应参数的 message_id
        public string $messageId;

        public array $userInfo = [];

        public string $msgType;

        const MSG_TYPE_MESSAGE        = 'message';
        const MSG_TYPE_EDITED_MESSAGE = 'edited_message';

        public function __construct(public Forwarder $forwarderManager)
        {
        }

        public function initCommon(): bool
        {
            $msgTable      = $this->forwarderManager->getMessageTable();
            $customerTable = $this->forwarderManager->getCustomerTable();
            $botMapTable   = $this->forwarderManager->getBotMapTable();

            if ($this->update->message)
            {
                $this->message = $this->update->message;
                $this->msgType = static::MSG_TYPE_MESSAGE;
            }
            elseif ($this->update->edited_message)
            {
                $this->message = $this->update->edited_message;
                $this->msgType = static::MSG_TYPE_EDITED_MESSAGE;
            }
            else
            {
                return false;
            }

            $this->botToken    = $this->forwarderManager->currentToken;
            $this->botMapInfo  = $this->forwarderManager->botMapInfo;
            $this->isBotEnable = ($this->botMapInfo[$botMapTable->getIsEnableField()] == $this->forwarderManager::ENABLED_1);

            $this->chatId       = $this->message->chat->id;
            $this->chatType     = $this->message->chat->type;
            $this->chatUsername = $this->message->chat->username;

            $this->fromId       = $this->message->from->id;
            $this->fromUsername = $this->message->from->username;

            $this->messageId  = $this->message->message_id;
            $this->fromChatId = $this->chatId;

            if ($this->chatType == 'private')
            {
                $this->chatIdToForwarder = $this->botMapInfo[$botMapTable->getGroupIdField()] ?? 0;

                if (//
                    !$this->chatIdToForwarder ||//
                    !$this->fromChatId ||//
                    !$this->messageId//
                )
                {
                    return false;
                }

                return true;

            }
            elseif ($this->chatType == 'supergroup')
            {
                //如果是没注册的群拉机器人当管理员，发信息不理睬
                if (!in_array($this->chatId, $this->forwarderManager->getAllRegistedGroup()))
                {
                    return false;
                }

                if (!$this->message->message_thread_id)
                {
                    //管理员在在群里群发的信息，不是跟指定的topics发的

                    $this->replyWithMessage([
                        'text' => '这是全局,需要指定一个topics发送信息',
                    ]);

                    return false;
                }

                $this->userInfo = $this->forwarderManager->getBotAndUserInfo($this->chatId, $this->message->message_thread_id);

                $this->chatIdToForwarder = $this->userInfo ? (int)$this->userInfo[$customerTable->getUserIdField()] : 0;

                if (//
                    !$this->chatIdToForwarder ||//
                    !$this->fromChatId ||//
                    !$this->messageId//
                )
                {
                    return false;
                }

                return true;
            }
            else
            {
                return false;
            }

        }

        public function getReplyToMessageId(): int
        {
            $reply_to_message_id = 0;
            if ($this->message->reply_to_message)
            {
                $reply_to_message_id = $this->forwarderManager->getReplyToMessageId($this->message->reply_to_message->from->id, $this->message->reply_to_message->message_id);
            }

            return $reply_to_message_id;
        }

        public function isAdmin($userId): bool
        {
            $adminIds = $this->forwarderManager->getAdminId($this->botToken);

            return in_array($userId, $adminIds);
        }

        public function replaceTemplate(string $template): string
        {
            $msgTable      = $this->forwarderManager->getMessageTable();
            $customerTable = $this->forwarderManager->getCustomerTable();
            $botMapTable   = $this->forwarderManager->getBotMapTable();

            return strtr($template, [
                "__CHAT_USERNAME__"   => $this->chatUsername,
                "__CHAT_FIRST_NAME__" => $this->message->chat->first_name,
                "__CHAT_LAST_NAME__"  => $this->message->chat->last_name,

                "__FROM_USERNAME__"   => $this->fromUsername,
                "__FROM_FIRST_NAME__" => $this->message->from->first_name,
                "__FROM_LAST_NAME__"  => $this->message->from->last_name,

                "__USER_USERNAME__"   => $this->userInfo ? $this->userInfo[$customerTable->getUsernameField()] : '',
                "__USER_FIRST_NAME__" => $this->userInfo ? $this->userInfo[$customerTable->getFirstNameField()] : '',
                "__USER_LAST_NAME__"  => $this->userInfo ? $this->userInfo[$customerTable->getLastNameField()] : '',
            ]);
        }

        public static function getRandomIconColor(): string
        {
            $colorArray = [
                '0x6FB9F0',
                '0xFFD67E',
                '0xCB86DB',
                '0x8EEE98',
                '0xFF93B2',
                '0xFB6F5F',
            ];

            mt_srand();                    // 每次调用强制刷新随机种子
            $index = mt_rand(0, 5);        // 直接用 mt_rand

            return $colorArray[$index];
        }

        public function isMessageWseCommand(): bool
        {
            //非命令的消息转发逻辑
            if (!is_null($this->message->text) and !preg_match('#^/#ium', $this->message->text))
            {
                return false;
            }

            return true;
        }

    }