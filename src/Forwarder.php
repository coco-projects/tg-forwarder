<?php

    namespace Coco\tgForwarder;

    use Coco\tableManager\TableRegistry;

    use Coco\tgForwarder\tables\BotMap;
    use Coco\tgForwarder\tables\Customer;
    use Coco\tgForwarder\tables\Message;

    use DI\Container;

    use Symfony\Component\Cache\Adapter\RedisAdapter;
    use Symfony\Component\Cache\Adapter\TagAwareAdapter;
    use Symfony\Component\Cache\Marshaller\DefaultMarshaller;
    use Symfony\Component\Cache\Marshaller\DeflateMarshaller;

    use Telegram\Bot\Api;
    use Telegram\Bot\Objects\Update;

    class Forwarder
    {
        protected ?Container $container      = null;
        protected bool       $debug          = false;
        protected bool       $enableRedisLog = false;
        protected bool       $enableEchoLog  = false;
        protected array      $tables         = [];

        protected string $redisHost     = '127.0.0.1';
        protected string $redisPassword = '';
        protected int    $redisPort     = 6379;
        protected int    $redisDb       = 9;

        protected string $mysqlDb;
        protected string $mysqlHost     = '127.0.0.1';
        protected string $mysqlUsername = 'root';
        protected string $mysqlPassword = 'root';
        protected int    $mysqlPort     = 3306;

        protected ?string $messageTableName  = null;
        protected ?string $customerTableName = null;
        protected ?string $botMapTableName   = null;

        protected ?string $logNamespace;
        protected ?string $cacheNamespace;

        public string $currentToken;

        public array $botMapInfo    = [];
        public array $msgTemplate   = [];
        public array $blockWordList = [];

        const BLOCKED_0 = 0;
        const BLOCKED_1 = 1;

        const FRAUD_0 = 0;
        const FRAUD_1 = 1;

        const ENABLED_0 = 0;
        const ENABLED_1 = 1;

        const WEBHOOK_SET_0 = 0;
        const WEBHOOK_SET_1 = 1;


        const CACHE_TAG_BOT_MAP  = 'tag_bot_map';
        const CACHE_TAG_CUSTOMER = 'tag_customer';
        const CACHE_TAG_MESSAGE  = 'tag_message';

        public function __construct(protected string $webhookBase, protected string $baseBotUrl = '', protected string $redisNamespace = 'forwarder', ?Container $container = null)
        {
            if (!is_null($container))
            {
                $this->container = $container;
            }
            else
            {
                $this->container = new Container();
            }

            $this->logNamespace   = $this->redisNamespace . '-log:';
            $this->cacheNamespace = $this->redisNamespace . '-cache';

//            $this->getMysqlClient()->logInfo();
        }

        /*
         * ---------------------------------------------------------
         * */
        public function webHookEndpoint(string $botHash): void
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            try
            {
                $bot = $this->getBotsManager($botHash);

                $this->currentToken = $bot->getAccessToken();
                $this->botMapInfo   = $this->getRegisterBotInfoByBotToken($this->currentToken);

                //测试这个方法要改 namespace Telegram\Bot\Methods\Update 的 getRequestBody 方法，加入测试数据
                $update = $bot->commandsHandler(true);

                $this->insertMessage($update, $this->botMapInfo[$botMapTable->getPkField()]);

                $bot->triggerCommand('internal_customer_message_handler', $update);
            }
            catch (\Exception $exception)
            {
                echo 'bot error: ' . $exception->getMessage();
            }
        }


        /*
         * ---------------------------------------------------------
         * */
        public function beforeInsertCheck(string $botToken, string $groupId): array
        {
            $data = [
                'ok'  => true,
                'msg' => [],
            ];

            if (!preg_match('/^\d{8,12}:[A-Za-z0-9_-]{30,}$/', $botToken))
            {
                $data['ok']    = false;
                $data['msg'][] = 'Bot Token 格式错误！正确格式应为：数字:字母数字下划线横线（至少30位）';
            }

            [
                $botUserId,
                $hash,
            ] = explode(':', $botToken, 2);
            if (!is_numeric($botUserId) || strlen($hash) < 30)
            {
                $data['ok']    = false;
                $data['msg'][] = 'Bot Token 格式错误（bot_id 必须是数字，且 hash 至少30位）';
            }

            if (!preg_match('/^-100\d{8,13}$/', $groupId))
            {
                $data['ok']    = false;
                $data['msg'][] = '群组ID格式错误！超级群必须以 -100 开头，后接8~13位数字';
            }

            // 转整数再验证（防止溢出或非法字符）
            $chatIdInt = (int)$groupId;
            if ($chatIdInt >= 0 || abs($chatIdInt) < 10000000000)
            {
                $data['ok']    = false;
                $data['msg'][] = '群组ID必须是负数，且超级群ID应为 -100xxxxxxxxxx 格式';
            }

            //$botToken 是不是已经添加过
            if ($this->getRegisterBotInfoByBotToken($botToken))
            {
                $data['ok']    = false;
                $data['msg'][] = '当前 botToken 已经被添加过';
            }

            //$groupId 是不是已经添加过
            if ($this->getRegisterBotInfoByGroupId($groupId))
            {
                $data['ok']    = false;
                $data['msg'][] = '当前 groupId 已经被添加过';
            }

            if ($data['ok'])
            {
                $data['msg'][] = '所有核心检查通过（但请注意非必须权限的缺失警告）';
            }

            return $data;
        }

        public function statusCheck(string $botToken, string $groupId): array
        {
            $data = [
                'ok'     => true,
                'msg'    => [],
                'status' => [
                    'bot'  => [],
                    'chat' => [],
                ],
            ];

            $hash = static::getBotHash($botToken);
            $bot  = $this->getBotsManager($hash);
            [
                $botUserId,
                $hash,
            ] = explode(':', $botToken, 2);

            try
            {
                $member = $bot->getChatMember([
                    'chat_id' => $groupId,
                    'user_id' => $botUserId,
                ]);

                $status = $member->status;

                $data['status']['bot']['status'] = $status;

                // 只有 administrator 才有大部分管理权限
                if ($status !== 'administrator')
                {
                    // 非管理员时，大部分 can_xxx 字段不存在或为 false，可直接跳过后续权限检查
                    $data['ok']    = false;
                    $data['msg'][] = "机器人当前状态为 {$status}，不是管理员，无法创建话题、管理消息等";
                }
                else
                {
                    // 以下字段来自 ChatMemberAdministrator
                    $permissions = [
                        'can_be_edited'          => $member->can_be_edited ?? false,
                        'can_manage_chat'        => $member->can_manage_chat ?? false,
                        'can_change_info'        => $member->can_change_info ?? false,
                        'can_delete_messages'    => $member->can_delete_messages ?? false,
                        'can_invite_users'       => $member->can_invite_users ?? false,
                        'can_restrict_members'   => $member->can_restrict_members ?? false,
                        'can_pin_messages'       => $member->can_pin_messages ?? false,
                        'can_manage_topics'      => $member->can_manage_topics ?? false,
                        'can_promote_members'    => $member->can_promote_members ?? false,
                        'can_manage_video_chats' => $member->can_manage_video_chats ?? false,
                        'can_post_stories'       => $member->can_post_stories ?? false,
                        'can_edit_stories'       => $member->can_edit_stories ?? false,
                        'can_delete_stories'     => $member->can_delete_stories ?? false,
                        'is_anonymous'           => $member->is_anonymous ?? false,
                        'can_manage_voice_chats' => $member->can_manage_voice_chats ?? false,
                    ];

                    $data['status']['bot'] = array_merge($data['status']['bot'], $permissions);

                    // 客服机器人核心权限检查与提示
                    if (!$permissions['can_manage_topics'])
                    {
                        $data['ok']    = false;
                        $data['msg'][] = '缺少 can_manage_topics 权限 → 无法创建/管理话题（每个用户独立话题将失败）';
                    }

                    if (!$permissions['can_delete_messages'])
                    {
                        $data['msg'][] = '缺少 can_delete_messages 权限 → 无法删除用户发送的敏感消息';
                    }

                    if (!$permissions['can_manage_chat'])
                    {
                        $data['msg'][] = '缺少 can_manage_chat 权限 → 部分高级管理功能受限';
                    }
                }
            }
            catch (\Telegram\Bot\Exceptions\TelegramResponseException $e)
            {
                $data['ok'] = false;
                $errMsg     = $e->getMessage();

                if (str_contains($errMsg, 'chat not found'))
                {
                    $data['msg'][] = '群组不存在或机器人已被移除';
                }
                elseif (str_contains($errMsg, 'user not found') || str_contains($errMsg, 'USER_NOT_PARTICIPANT'))
                {
                    $data['msg'][] = '机器人不在该群组中（请先把机器人加进群并设为管理员）';
                }
                elseif (str_contains($errMsg, '403'))
                {
                    $data['msg'][] = '403 Forbidden → 机器人无权查看该群成员信息（极少见）';
                }
                else
                {
                    $data['msg'][] = "getChatMember 失败：{$errMsg}";
                }
            }
            catch (\Exception $e)
            {
                $data['ok']    = false;
                $data['msg'][] = "意外错误：{$e->getMessage()}";
            }

            try
            {
                $chatInfo = $bot->getChat(['chat_id' => $groupId]);

                $data['status']['chat'] = [
                    'title'    => $chatInfo->title ?? '未知',
                    'type'     => $chatInfo->type ?? '未知',
                    'username' => $chatInfo->username ?? null,
                    'is_forum' => $chatInfo->is_forum ?? false,
                ];

                if (!$chatInfo->is_forum)
                {
                    $data['ok']    = false;
                    $data['msg'][] = '该群组未开启话题功能（is_forum = false）→ 请群管理员在群设置中打开「话题」';
                }
            }
            catch (\Exception $e)
            {
                $data['ok']    = false;
                $data['msg'][] = "getChat 失败（可能群不存在或机器人被禁）：{$e->getMessage()}";
            }

            $privacyNote = '';
            if (isset($data['status']['bot']['status']) && $data['status']['bot']['status'] === 'administrator')
            {
                $privacyNote = '机器人是管理员 → 自动忽略隐私模式，可收到群内所有消息（包括普通文本、非命令消息）';
            }
            else
            {
                $privacyNote = '机器人不是管理员 → 能否收到普通消息取决于 @BotFather 设置的 Privacy Mode（建议关闭）';
                if ($data['ok'])
                {
                    $data['msg'][] = $privacyNote;
                }
            }
            $data['status']['bot']['privacy_mode_note'] = $privacyNote;

            if ($data['ok'])
            {
                $data['msg'][] = '所有核心检查通过（但请注意非必须权限的缺失警告）';
            }

            return $data;
        }


        public function flushTabBotMap(): static
        {
            $this->getCacheManager()->invalidateTags([static::CACHE_TAG_BOT_MAP]);

            return $this;
        }

        public function flushTabCustomer(): static
        {
            $this->getCacheManager()->invalidateTags([static::CACHE_TAG_CUSTOMER]);

            return $this;
        }

        public function flushTabMessage(): static
        {
            $this->getCacheManager()->invalidateTags([static::CACHE_TAG_MESSAGE]);

            return $this;
        }

        public function getAllRegistedGroup(): array
        {
            return $this->getCacheManager()->get('AllRegistedGroup', function($item) {
                $item->expiresAfter(60);

                $item->tag([
                    static::CACHE_TAG_BOT_MAP,
                ]);

                $msgTable      = $this->getMessageTable();
                $customerTable = $this->getCustomerTable();
                $botMapTable   = $this->getBotMapTable();

                return $botMapTable->tableIns()->column($botMapTable->getGroupIdField());
            });
        }

        public function getEnabledBots(): \think\model\Collection|array|\think\Collection
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            return $this->getBotsList([
                [
                    $botMapTable->getIsEnableField(),
                    '=',
                    static::ENABLED_1,
                ],
            ]);
        }

        public function getBotsList(array $where = []): \think\model\Collection|array|\think\Collection
        {
            return $this->getCacheManager()->get('BotsList-' . md5(json_encode($where)), function($item) use ($where) {
                $item->expiresAfter(60);

                $item->tag([
                    static::CACHE_TAG_BOT_MAP,
                ]);

                $msgTable      = $this->getMessageTable();
                $customerTable = $this->getCustomerTable();
                $botMapTable   = $this->getBotMapTable();

                return $botMapTable->tableIns()->where($where)->select();
            });

        }

        public function getBotGroupId(string $botToken)
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $info = $this->getRegisterBotInfoByBotToken($botToken);

            if ($info && ($info[$botMapTable->getGroupIdField()]))
            {
                return $info[$botMapTable->getGroupIdField()];
            }

            return null;
        }

        public function getBotMapId(string $botToken)
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $info = $this->getRegisterBotInfoByBotToken($botToken);

            if ($info && ($info[$botMapTable->getPkField()]))
            {
                return $info[$botMapTable->getPkField()];
            }

            return null;
        }

        public function updateRegisterBotInfo(string $botToken, array $data): int
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $result = $botMapTable->tableIns()->where([
                [
                    $botMapTable->getBotTokenField(),
                    '=',
                    $botToken,
                ],
            ])->update($data);

            $this->flushTabBotMap();

            return $result;
        }

        public function getRegisterBotInfoByBotToken(string $botToken)
        {
            return $this->getCacheManager()
                ->get('RegisterBotInfoByBotToken-' . md5($botToken), function($item) use ($botToken) {
                    $item->expiresAfter(60);

                    $item->tag([
                        static::CACHE_TAG_BOT_MAP,
                    ]);

                    $msgTable      = $this->getMessageTable();
                    $customerTable = $this->getCustomerTable();
                    $botMapTable   = $this->getBotMapTable();

                    return $botMapTable->tableIns()->where([
                        [
                            $botMapTable->getBotTokenField(),
                            '=',
                            $botToken,
                        ],
                    ])->find();
                });

        }

        public function getRegisterBotInfoByGroupId(string $groupId)
        {
            return $this->getCacheManager()
                ->get('RegisterBotInfoByGroupId-' . md5($groupId), function($item) use ($groupId) {
                    $item->expiresAfter(60);

                    $item->tag([
                        static::CACHE_TAG_BOT_MAP,
                    ]);

                    $msgTable      = $this->getMessageTable();
                    $customerTable = $this->getCustomerTable();
                    $botMapTable   = $this->getBotMapTable();

                    return $botMapTable->tableIns()->where([
                        [
                            $botMapTable->getGroupIdField(),
                            '=',
                            $groupId,
                        ],
                    ])->find();
                });

        }

        public function getRegisterBotInfoByHash(string $hash)
        {
            return $this->getCacheManager()->get('RegisterBotInfoByHash-' . md5($hash), function($item) use ($hash) {
                $item->expiresAfter(60);
                $item->tag([
                    static::CACHE_TAG_BOT_MAP,
                ]);

                $msgTable      = $this->getMessageTable();
                $customerTable = $this->getCustomerTable();
                $botMapTable   = $this->getBotMapTable();

                return $botMapTable->tableIns()->where([
                    [
                        $botMapTable->getBotTokenHashField(),
                        '=',
                        $hash,
                    ],
                ])->find();
            });
        }


        public function disableBot(string $botToken): bool
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $info = $this->getRegisterBotInfoByBotToken($botToken);

            if ($info)
            {
                return !!$this->updateRegisterBotInfo($botToken, [
                    $botMapTable->getIsEnableField() => static::ENABLED_0,
                ]);
            }

            return false;
        }

        public function enableBot(string $botToken): bool
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $info = $this->getRegisterBotInfoByBotToken($botToken);

            if ($info)
            {
                return !!$this->updateRegisterBotInfo($botToken, [
                    $botMapTable->getIsEnableField() => static::ENABLED_1,
                ]);
            }

            return false;
        }

        public function unregisterBot(string $botToken): bool
        {
            $this->disableBot($botToken);

            return $this->deleteBotWebhook($botToken);
        }

        public function registerBot(string $botToken): bool
        {
            $this->enableBot($botToken);

            return $this->setBotWebhook($botToken);
        }

        public function addBot(string $botToken, string $groupId): int|string
        {
            $checkResult = $this->beforeInsertCheck($botToken, $groupId);
            if (!$checkResult['ok'])
            {
                throw new \Exception(implode(",", $checkResult['msg']));
            }

            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $data = [
                $botMapTable->getPkField()           => $botMapTable->calcPk(),
                $botMapTable->getGroupIdField()      => $groupId,
                $botMapTable->getBotTokenField()     => $botToken,
                $botMapTable->getBotUidField()       => explode(':', $botToken)[0],
                $botMapTable->getBotTokenHashField() => static::getBotHash($botToken),
                $botMapTable->getTimeField()         => time(),
            ];

            $result = !!$botMapTable->tableIns()->insert($data);
            $this->flushTabBotMap();

            return $result;
        }


        public function addAdminId(string $botToken, array $adminIds = []): int
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $adminIdsCurrent = $this->getAdminId($botToken);

            $mergedArray = array_merge($adminIdsCurrent, $adminIds);
            $uniqueArray = array_unique($mergedArray);

            return $this->updateRegisterBotInfo($botToken, [
                $botMapTable->getAdminIdsField() => implode(',', $uniqueArray),
            ]);
        }

        public function removeAdminId(string $botToken, array $adminIds = []): int
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $adminIdsCurrent = $this->getAdminId($botToken);

            // 从当前管理员ID中移除传入的adminIds
            $updatedAdminIds = array_diff($adminIdsCurrent, $adminIds);

            return $this->updateRegisterBotInfo($botToken, [
                $botMapTable->getAdminIdsField() => implode(',', $updatedAdminIds),
            ]);
        }

        public function getAdminId(string $botToken): array
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $info = $this->getRegisterBotInfoByBotToken($botToken);

            if (!$info)
            {
                return [];
            }

            $adminIdsCurrent = trim($info[$botMapTable->getAdminIdsField()]);

            return $adminIdsCurrent ? explode(',', $adminIdsCurrent) : [];
        }


        protected function setBotWebhook(string $botToken): bool
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $hash = static::getBotHash($botToken);

            $bot = $this->getBotsManager($hash);

            $result = $bot->setWebhook([
                'url' => $this->makeBotWebhookUrl($botToken),
            ]);

            $result && $this->updateRegisterBotInfo($botToken, [
                $botMapTable->getIsWebhookSetField() => static::WEBHOOK_SET_1,
            ]);

            return $result;
        }

        protected function deleteBotWebhook(string $botToken): bool
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $hash = static::getBotHash($botToken);

            $bot = $this->getBotsManager($hash);

            $result = $bot->deleteWebhook();

            $result && $this->updateRegisterBotInfo($botToken, [
                $botMapTable->getIsWebhookSetField() => static::WEBHOOK_SET_0,
            ]);

            return $result;
        }

        /*
         * ---------------------------------------------------------
         * */

        //两边回信时候引用回复的信息
        public function getBotAndMessageInfo(int $fromId, int $messageId)
        {
            return $this->getCacheManager()
                ->get('BotAndMessageInfo-' . md5($fromId . $messageId), function($item) use ($fromId, $messageId) {
                    $item->expiresAfter(60);

                    $item->tag([
                        static::CACHE_TAG_BOT_MAP,
                        static::CACHE_TAG_MESSAGE,
                    ]);

                    $msgTable      = $this->getMessageTable();
                    $customerTable = $this->getCustomerTable();
                    $botMapTable   = $this->getBotMapTable();

                    return $msgTable->tableIns()->field(implode(',', [
                        $msgTable->getName() . '.*',
                        $botMapTable->getName() . '.' . $botMapTable->getBotTokenField(),
                        $botMapTable->getName() . '.' . $botMapTable->getBotTokenHashField(),
                        $botMapTable->getName() . '.' . $botMapTable->getGroupIdField(),
                        $botMapTable->getName() . '.' . $botMapTable->getAdminIdsField(),
                        $botMapTable->getName() . '.' . $botMapTable->getIsEnableField(),
                        $botMapTable->getName() . '.' . $botMapTable->getIsWebhookSetField(),
                    ]))->join(                                                         //

                        $botMapTable->getName(),                                //

                        implode('', [
                            $msgTable->getName() . '.' . $msgTable->getBotMapIdField(),
                            ' = ',
                            $botMapTable->getName() . '.' . $botMapTable->getPkField(),
                        ]),                                                     //

                        'left'                                                         //
                    )->where([
                        [
                            $botMapTable->getBotUidField(),
                            '=',
                            $fromId,
                        ],
                        [
                            $msgTable->getReplyToGroupMessageIdField(),
                            '=',
                            $messageId,
                        ],
                    ])->fetchSql(false)->find();
                });

        }

        //回复引用信息时候，获取引用的信息id
        public function getReplyToMessageId(int $fromId, int $messageId): int
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $info = $this->getBotAndMessageInfo($fromId, $messageId);

            return $info ? (int)$info[$msgTable->getMessageIdField()] : 0;
        }

        // 管理员发信息给用户的时候，获取用户信息
        public function getBotAndUserInfo(int $groupId, int $messageThreadId)
        {
            return $this->getCacheManager()
                ->get('BotAndUserInfo-' . md5($groupId . $messageThreadId), function($item) use ($groupId, $messageThreadId) {
                    $item->expiresAfter(60);
                    $item->tag([
                        static::CACHE_TAG_BOT_MAP,
                        static::CACHE_TAG_CUSTOMER,
                    ]);

                    $msgTable      = $this->getMessageTable();
                    $customerTable = $this->getCustomerTable();
                    $botMapTable   = $this->getBotMapTable();

                    return $customerTable->tableIns()->field(implode(',', [
                        $customerTable->getName() . '.*',
                        $botMapTable->getName() . '.' . $botMapTable->getBotTokenField(),
                        $botMapTable->getName() . '.' . $botMapTable->getBotTokenHashField(),
                        $botMapTable->getName() . '.' . $botMapTable->getGroupIdField(),
                        $botMapTable->getName() . '.' . $botMapTable->getAdminIdsField(),
                        $botMapTable->getName() . '.' . $botMapTable->getIsEnableField(),
                        $botMapTable->getName() . '.' . $botMapTable->getIsWebhookSetField(),
                    ]))->join(                                                                 //
                        $botMapTable->getName(),                                //
                        implode('', [
                            $customerTable->getName() . '.' . $customerTable->getBotMapIdField(),
                            ' = ',
                            $botMapTable->getName() . '.' . $botMapTable->getPkField(),
                        ]),                                                     //
                        'left'                                                                 //
                    )->where([
                        [
                            $botMapTable->getGroupIdField(),
                            '=',
                            $groupId,
                        ],
                        [
                            $customerTable->getMessageThreadIdField(),
                            '=',
                            $messageThreadId,
                        ],
                    ])->find();

                });
        }

        //编辑信息时候，根据映射表找到机器人另一半的对应那个信息的id
        public function getEditedMessageId(int $editedMessage, int $editedMessageFromId): int
        {
            return (int)$this->getCacheManager()
                ->get('EditedMessageId-' . md5($editedMessage . $editedMessageFromId), function($item) use ($editedMessage, $editedMessageFromId) {
                    $item->expiresAfter(60);
                    $item->tag([
                        static::CACHE_TAG_MESSAGE,
                    ]);

                    $msgTable      = $this->getMessageTable();
                    $customerTable = $this->getCustomerTable();
                    $botMapTable   = $this->getBotMapTable();

                    return $msgTable->tableIns()->where([
                        [
                            $msgTable->getFromIdField(),
                            '=',
                            $editedMessageFromId,
                        ],
                        [
                            $msgTable->getMessageIdField(),
                            '=',
                            $editedMessage,
                        ],
                    ])->fetchSql(false)->value($msgTable->getReplyToGroupMessageIdField());

                });

        }

        //管理员编辑信息后，把数据库里的信息也同步一下
        public function updateEditedMessage(int $editedMessage, int $editedMessageFromId, string $newText): int
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $info = $msgTable->tableIns()->where([
                [
                    $msgTable->getFromIdField(),
                    '=',
                    $editedMessageFromId,
                ],
                [
                    $msgTable->getMessageIdField(),
                    '=',
                    $editedMessage,
                ],
            ])->update([
                $msgTable->getTextField() => $newText,
            ]);

            $this->flushTabMessage();

            return (int)$info;
        }


        /*
         * ---------------------------------------------------------
         * */

        public function getUserList(array $where = []): \think\model\Collection|array|\think\Collection
        {

            return $this->getCacheManager()->get('UserList-' . md5(json_encode($where)), function($item) use ($where) {
                $item->expiresAfter(60);

                $item->tag([
                    static::CACHE_TAG_BOT_MAP,
                    static::CACHE_TAG_CUSTOMER,
                ]);

                $msgTable      = $this->getMessageTable();
                $customerTable = $this->getCustomerTable();
                $botMapTable   = $this->getBotMapTable();

                return $customerTable->tableIns()                           //
                ->field(implode(',', [
                    $customerTable->getName() . '.*',
                    $botMapTable->getName() . '.' . $botMapTable->getBotTokenField(),
                    $botMapTable->getName() . '.' . $botMapTable->getBotTokenHashField(),
                    $botMapTable->getName() . '.' . $botMapTable->getGroupIdField(),
                    $botMapTable->getName() . '.' . $botMapTable->getAdminIdsField(),
                    $botMapTable->getName() . '.' . $botMapTable->getIsEnableField(),
                    $botMapTable->getName() . '.' . $botMapTable->getIsWebhookSetField(),
                ]))->join(                               //
                    $botMapTable->getName(),                                //
                    implode('', [
                        $customerTable->getName() . '.' . $customerTable->getBotMapIdField(),
                        ' = ',
                        $botMapTable->getName() . '.' . $botMapTable->getPkField(),
                    ]),                                                     //
                    'left'                               //
                )->where($where)->select();
            });

        }

        public function getUserInfo(string $userId, int $botMapId): mixed
        {
            return $this->getCacheManager()
                ->get('UserInfo-' . md5($userId . $botMapId), function($item) use ($userId, $botMapId) {
                    $item->expiresAfter(60);
                    $item->tag([
                        static::CACHE_TAG_BOT_MAP,
                        static::CACHE_TAG_CUSTOMER,
                    ]);

                    $msgTable      = $this->getMessageTable();
                    $customerTable = $this->getCustomerTable();
                    $botMapTable   = $this->getBotMapTable();

                    return $customerTable->tableIns()->field(implode(',', [
                        $customerTable->getName() . '.*',
                        $botMapTable->getName() . '.' . $botMapTable->getBotTokenField(),
                        $botMapTable->getName() . '.' . $botMapTable->getBotTokenHashField(),
                        $botMapTable->getName() . '.' . $botMapTable->getGroupIdField(),
                        $botMapTable->getName() . '.' . $botMapTable->getAdminIdsField(),
                        $botMapTable->getName() . '.' . $botMapTable->getIsEnableField(),
                        $botMapTable->getName() . '.' . $botMapTable->getIsWebhookSetField(),
                    ]))->join(                               //
                        $botMapTable->getName(),                                //
                        implode('', [
                            $customerTable->getName() . '.' . $customerTable->getBotMapIdField(),
                            ' = ',
                            $botMapTable->getName() . '.' . $botMapTable->getPkField(),
                        ]),                                                     //
                        'left'                               //
                    )->where([
                        [
                            $customerTable->getUserIdField(),
                            '=',
                            $userId,
                        ],
                        [
                            $customerTable->getBotMapIdField(),
                            '=',
                            $botMapId,
                        ],
                    ])->find();
                });
        }

        public function pushUserRemark(string $userId, int $botMapId, string $remark): static
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $currentRemark   = $this->getUserRemark($userId, $botMapId);
            $currentRemark[] = '【' . date('Y-m-d H:i:s') . '】:' . $remark;

            $this->updateUserInfo($userId, $botMapId, [
                $customerTable->getRemarkField() => json_encode($currentRemark, 256),
            ]);

            return $this;
        }

        public function getUserRemark(string $userId, int $botMapId): array
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $userInfo = $this->getUserInfo($userId, $botMapId);

            $remark = '[]';

            if ($userInfo[$customerTable->getRemarkField()])
            {
                $remark = $userInfo[$customerTable->getRemarkField()];
            }

            return json_decode($remark, 1);
        }


        public function blockUser(string $userId, int $botMapId): static
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $this->updateUserInfo($userId, $botMapId, [
                $customerTable->getIsBlockedField() => static::BLOCKED_1,
            ]);

            return $this;
        }

        public function unBlockUser(string $userId, int $botMapId): static
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $this->updateUserInfo($userId, $botMapId, [
                $customerTable->getIsBlockedField() => static::BLOCKED_0,
            ]);

            return $this;
        }

        public function setUserFraud(string $userId, int $botMapId): static
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $this->updateUserInfo($userId, $botMapId, [
                $customerTable->getIsFraudField() => static::FRAUD_1,
            ]);

            return $this;
        }

        public function unsetUserFraud(string $userId, int $botMapId): static
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $this->updateUserInfo($userId, $botMapId, [
                $customerTable->getIsFraudField() => static::FRAUD_0,
            ]);

            return $this;
        }

        public function updateUserInfo(string $userId, int $botMapId, array $data): int
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $result = $customerTable->tableIns()->where([
                [
                    $customerTable->getUserIdField(),
                    '=',
                    $userId,
                ],
                [
                    $customerTable->getBotMapIdField(),
                    '=',
                    $botMapId,
                ],
            ])->update($data);

            $this->flushTabCustomer();

            return $result;
        }

        public function isUserExists(string $userId, int $botMapId): bool
        {
            return !!$this->getUserInfo($userId, $botMapId);
        }

        public function addUser(string $userId, int $botMapId): bool
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $info = $this->getUserInfo($userId, $botMapId);
            if (!$info)
            {
                $result = !!$customerTable->tableIns()->insert([
                    $customerTable->getPkField()       => $customerTable->calcPk(),
                    $customerTable->getUserIdField()   => $userId,
                    $customerTable->getBotMapIdField() => $botMapId,
                    $customerTable->getRemarkField()   => '[]',
                    $customerTable->getTimeField()     => time(),
                ]);

                $this->flushTabCustomer();

                return $result;
            }

            return true;
        }

        /*
         * ---------------------------------------------------------
         * */

        public function updateReplyToGroupMessageId(int $chatId, int $messageId, int $replyToGroupMessageId): int
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $data = [
                $msgTable->getReplyToGroupMessageIdField() => $replyToGroupMessageId,
            ];

            $result = $msgTable->tableIns()->where([
                [
                    $msgTable->getChatIdField(),
                    '=',
                    $chatId,
                ],
                [
                    $msgTable->getMessageIdField(),
                    '=',
                    $messageId,
                ],
            ])->update($data);

            $this->flushTabMessage();

            return $result;
        }


        /*
         * ---------------------------------------------------------
         * */

        public function initBotsManager(): static
        {
            $this->container->set('botsManager', function(Container $container) {
                return new \Telegram\Bot\BotsManager($this->makeForwarderConfig());
            });

            return $this;
        }

        public function getBotsManager(string $botHash): Api
        {
            $telegram = $this->container->get('botsManager');

            $bot = $telegram->bot($botHash);

            $bot->addCommands([
                new \Coco\tgForwarder\Commands\StartHandler($this),
                new \Coco\tgForwarder\Commands\HelpHandler($this),
                new \Coco\tgForwarder\Commands\InfoHandler($this),
                new \Coco\tgForwarder\Commands\BlockHandler($this),
                new \Coco\tgForwarder\Commands\UnBlockHandler($this),
                new \Coco\tgForwarder\Commands\CustomerMessageHandler($this),
            ]);

            return $bot;
        }


        /*
         * ---------------------------------------------------------
         * */

        protected function initMysql(): static
        {
            $this->container->set('mysqlClient', function(Container $container) {

                $registry = TableRegistry::initMysqlClient($this->mysqlDb, $this->mysqlHost, $this->mysqlUsername, $this->mysqlPassword, $this->mysqlPort,);

                $logName = 'te-mysql';
                $registry->setStandardLogger($logName);

                if ($this->enableRedisLog)
                {
                    $registry->addRedisHandler(redisHost: $this->redisHost, redisPort: $this->redisPort, password: $this->redisPassword, db: $this->redisDb, logName: $this->logNamespace . $logName, callback: $registry::getStandardFormatter());
                }

                if ($this->enableEchoLog)
                {
                    $registry->addStdoutHandler($registry::getStandardFormatter());
                }

                return $registry;
            });

            return $this;
        }

        public function getMysqlClient(): TableRegistry
        {
            return $this->container->get('mysqlClient');
        }

        /*
         * ---------------------------------------------------------
         * */

        protected function initRedis(): static
        {
            $this->container->set('redisClient', function(Container $container) {
                return (new \Redis());
            });

            $this->initCacheManager();

            return $this;
        }

        public function getRedisClient(): \Redis
        {
            return $this->container->get('redisClient');
        }

        /*
         * ---------------------------------------------------------
         * */

        protected function initCacheManager(): static
        {
            $this->container->set('cacheManager', function(Container $container) {
                $marshaller   = new DeflateMarshaller(new DefaultMarshaller());
                $cacheManager = new RedisAdapter($container->get('redisClient'), $this->cacheNamespace, 0, $marshaller);

                return new TagAwareAdapter($cacheManager);
            });

            return $this;
        }

        public function getCacheManager(): TagAwareAdapter
        {
            return $this->container->get('cacheManager');
        }


        /*
         *
         * ---------------------------------------------------------
         *
         * */

        public function initMessageTable(string $name, callable $callback): static
        {
            $this->messageTableName = $name;

            $this->getMysqlClient()->initTable($name, Message::class, $callback);

            return $this;
        }

        public function getMessageTable(): Message
        {
            return $this->getMysqlClient()->getTable($this->messageTableName);
        }

        /*
         *
         * ---------------------------------------------------------
         *
         * */

        public function initBotMapTable(string $name, callable $callback): static
        {
            $this->botMapTableName = $name;

            $this->getMysqlClient()->initTable($name, BotMap::class, $callback);

            return $this;
        }

        public function getBotMapTable(): BotMap
        {
            return $this->getMysqlClient()->getTable($this->botMapTableName);
        }

        /*
         *
         * ---------------------------------------------------------
         *
         * */

        public function initCustomerTable(string $name, callable $callback): static
        {
            $this->customerTableName = $name;

            $this->getMysqlClient()->initTable($name, Customer::class, $callback);

            return $this;
        }

        public function getCustomerTable(): Customer
        {
            return $this->getMysqlClient()->getTable($this->customerTableName);
        }

        /*
          *
          * ---------------------------------------------------------
          *
          * */

        public static function getBotHash(string $botToken): string
        {
            return md5($botToken);
        }

        private function insertMessage(Update $update, int $botMapId): void
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            if (!$update->getMessage())
            {
                return;
            }

            $msg = UpdateMessage::parse(json_encode($update->getRawResponse(), 256));

            $data = [
                $msgTable->getPkField() => $msgTable->calcPk(),

                $msgTable->getBotMapIdField() => $botMapId,

                $msgTable->getUpdateIdField()  => $msg->updateId,
                $msgTable->getMessageIdField() => $msg->messageId,

                $msgTable->getReplyToGroupMessageIdField() => 0,

                $msgTable->getFromIdField()        => $msg->fromId,
                $msgTable->getFromFirstNameField() => $msg->fromFirstName,
                $msgTable->getFromLastNameField()  => $msg->fromLastName,
                $msgTable->getFromUsernameField()  => $msg->fromUsername,
                $msgTable->getFromIsBotField()     => $msg->fromIsBot,

                $msgTable->getChatIdField()        => $msg->chatId,
                $msgTable->getChatFirstNameField() => $msg->chatFirstName,
                $msgTable->getChatLastNameField()  => $msg->chatLastName,
                $msgTable->getChatUsernameField()  => $msg->chatUsername,
                $msgTable->getChatTypeField()      => $msg->chatType,
                $msgTable->getDateField()          => $msg->date,

                $msgTable->getreplyToFromIdField()        => $msg->replyToFromId,
                $msgTable->getreplyToFromFirstNameField() => $msg->replyToFromFirstName,
                $msgTable->getreplyToFromLastNameField()  => $msg->replyToFromLastName,
                $msgTable->getreplyToFromUsernameField()  => $msg->replyToFromUsername,
                $msgTable->getreplyToFromIsBotField()     => $msg->replyToFromIsBot,

                $msgTable->getreplyToChatIdField()        => $msg->replyToChatId,
                $msgTable->getreplyToChatFirstNameField() => $msg->replyToChatFirstName,
                $msgTable->getreplyToChatLastNameField()  => $msg->replyToChatLastName,
                $msgTable->getreplyToChatUsernameField()  => $msg->replyToChatUsername,
                $msgTable->getreplyToChatTypeField()      => $msg->replyToChatType,
                $msgTable->getreplyDateField()            => $msg->replyDate,

                $msgTable->getIsTopicMessageField()  => $msg->isTopicMessage,
                $msgTable->getMessageThreadIdField() => $msg->messageThreadId,

                $msgTable->getMediaGroupIdField()    => $msg->mediaGroupId,
                $msgTable->getMessageLoadTypeField() => $msg->messageLoadType,
                $msgTable->getMessageFromTypeField() => $msg->messageFromType,
                $msgTable->getFileIdField()          => $msg->fileId,
                $msgTable->getFileUniqueIdField()    => $msg->fileUniqueId,
                $msgTable->getFileSizeField()        => $msg->fileSize,
                $msgTable->getFileNameField()        => $msg->fileName,
                $msgTable->getCaptionField()         => $msg->caption,
                $msgTable->getMimeTypeField()        => $msg->mimeType,
                $msgTable->getMediaTypeField()       => $msg->mediaType,
                $msgTable->getExtField()             => $msg->ext,
                $msgTable->getTextField()            => $msg->text,
                $msgTable->getHashtagsField()        => implode(',', $msg->tags),
                $msgTable->getRawField()             => $msg->message,
                $msgTable->getTimeField()            => time(),
            ];

            $msgTable->tableIns()->insert($data);

            $this->flushTabMessage();
        }

        public function setDebug(bool $debug): void
        {
            $this->debug = $debug;
        }

        public function isDebug(): bool
        {
            return $this->debug;
        }

        public function initServer(): static
        {
            $this->initRedis();
            $this->initMysql();

            return $this;
        }

        public function setEnableEchoLog(bool $enableEchoLog): static
        {
            $this->enableEchoLog = $enableEchoLog;

            return $this;
        }

        public function setEnableRedisLog(bool $enableRedisLog): static
        {
            $this->enableRedisLog = $enableRedisLog;

            return $this;
        }

        public function setRedisConfig(string $host = '127.0.0.1', string $password = '', int $port = 6379, int $db = 9): static
        {
            $this->redisHost     = $host;
            $this->redisPassword = $password;
            $this->redisPort     = $port;
            $this->redisDb       = $db;

            return $this;
        }

        public function setMysqlConfig($db, $host = '127.0.0.1', $username = 'root', $password = 'root', $port = 3306): static
        {
            $this->mysqlHost     = $host;
            $this->mysqlPassword = $password;
            $this->mysqlUsername = $username;
            $this->mysqlPort     = $port;
            $this->mysqlDb       = $db;

            return $this;
        }


        public function getTemplate(string $name)
        {
            return $this->msgTemplate[$name] ?? '';
        }

        public function setMsgTemplate(array $msgTemplate): static
        {
            $this->msgTemplate = $msgTemplate;

            return $this;
        }

        public function isContainkWord(string $text): array
        {
            $words = [];
            foreach ($this->blockWordList as $word)
            {
                if (str_contains(strtolower($text), strtolower(trim($word))))
                {
                    $words[] = $word;
                }

            }

            return $words;
        }

        public function setBlockWordList(array $blockWordList): static
        {
            $this->blockWordList = $blockWordList;

            return $this;
        }

        public function makeForwarderConfig(): array
        {
            $msgTable      = $this->getMessageTable();
            $customerTable = $this->getCustomerTable();
            $botMapTable   = $this->getBotMapTable();

            $bots     = [];
            $botsList = $this->getBotsList();

            foreach ($botsList as $k => $botInfo)
            {
                $hash = static::getBotHash($botInfo[$botMapTable->getBotTokenField()]);

                $bots[$hash] = [
                    'token' => $botInfo[$botMapTable->getBotTokenField()],
                ];
            }

            $data = [
                'bots' => $bots,
            ];

            if ($this->baseBotUrl)
            {
                $data['base_bot_url'] = $this->baseBotUrl;
            }

            return $data;

        }

        public function makeBotWebhookUrl(string $botToken): string
        {
            return implode('', [
                $this->webhookBase,
                '?bot_hash=' . static::getBotHash($botToken),
            ]);
        }

    }
