<?php

    declare(strict_types = 1);

    namespace Coco\tgForwarder;

    use Coco\snowflake\Snowflake;

    class UpdateMessage
    {
        const MSG_SOURCE_TYPE_PRIVATE    = 1;
        const MSG_SOURCE_TYPE_GROUP      = 2;
        const MSG_SOURCE_TYPE_SUPERGROUP = 3;
        const MSG_SOURCE_TYPE_CHANNEL    = 4;

        const MSG_CARRIER_TYPE_TEXT      = 1;
        const MSG_CARRIER_TYPE_VIDEO     = 2;
        const MSG_CARRIER_TYPE_PHOTO     = 3;
        const MSG_CARRIER_TYPE_AUDIO     = 4;
        const MSG_CARRIER_TYPE_DOCUMENT  = 5;
        const MSG_CARRIER_TYPE_ANIMATION = 6;
        const MSG_CARRIER_TYPE_STICKER   = 7;
        const MSG_CARRIER_TYPE_LOCATION  = 8;
        const MSG_CARRIER_TYPE_CONTACT   = 9;
        const MSG_CARRIER_TYPE_NEWS      = 10;
        const MSG_CARRIER_TYPE_POLL      = 11;

        const MSG_FROM_TYPE_MESSAGE             = 1;
        const MSG_FROM_TYPE_EDITED_MESSAGE      = 2;
        const MSG_FROM_TYPE_CHANNEL_POST        = 3;
        const MSG_FROM_TYPE_EDITED_CHANNEL_POST = 4;

        protected static Snowflake|null $snowflake   = null;
        protected array                 $messageRow;
        protected array                 $messageBody = [];
        public array                    $tags        = [];

        public string $mediaGroupId    = '';
        public int    $messageLoadType = 0;
        public int    $messageFromType = 0;


        public string $caption = '';
        public string $text    = '';

        public string $fileId       = '';
        public string $fileUniqueId = '';

        public string $mediaType = '';
        public string $mimeType  = '';
        public string $ext       = '';
        public string $fileName  = '';
        public int    $fileSize  = 0;

        public int $messageId = 0;
        public int $updateId  = 0;

        public int    $fromId        = 0;
        public string $fromFirstName = '';
        public string $fromLastName  = '';
        public string $fromUsername  = '';
        public bool   $fromIsBot     = false;

        public int    $chatId        = 0;
        public string $chatFirstName = '';
        public string $chatLastName  = '';
        public string $chatUsername  = '';
        public string $chatType      = '';
        public int    $date          = 0;

        public int    $replyToFromId        = 0;
        public string $replyToFromFirstName = '';
        public string $replyToFromLastName  = '';
        public string $replyToFromUsername  = '';
        public bool   $replyToFromIsBot     = false;

        public int    $replyToChatId        = 0;
        public string $replyToChatFirstName = '';
        public string $replyToChatLastName  = '';
        public string $replyToChatUsername  = '';
        public string $replyToChatType      = '';
        public int    $replyDate            = 0;

        public bool $isTopicMessage  = false;
        public int  $messageThreadId = 0;

        public int $latitude  = 0;
        public int $longitude = 0;

        public string $pollQuestion              = '';
        public bool   $pollIsAnonymous           = false;
        public string $pollType                  = 'regular';
        public array  $pollOptions               = [];
        public ?int   $pollCorrectOptionId       = null;
        public string $pollExplanation           = '';
        public int    $pollOpenPeriod            = 0;
        public int    $pollCloseDate             = 0;
        public bool   $pollAllowsMultipleAnswers = false;

        protected static array $mimeTypesMap = [

            // 文档
            'application/pdf'                                                           => 'pdf',
            'application/msword'                                                        => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
            'application/vnd.ms-excel'                                                  => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
            'application/vnd.ms-powerpoint'                                             => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/rtf'                                                           => 'rtf',
            'application/xml'                                                           => 'xml',
            'application/json'                                                          => 'json',
            'application/vnd.oasis.opendocument.text'                                   => 'odt',
            'application/vnd.oasis.opendocument.spreadsheet'                            => 'ods',
            'application/vnd.oasis.opendocument.presentation'                           => 'odp',

            // 压缩文档
            'application/zip'                                                           => 'zip',
            'application/x-zip-compressed'                                              => 'zip',
            'application/x-gzip'                                                        => 'gz',
            'application/x-tar'                                                         => 'tar',
            'application/x-bzip2'                                                       => 'bz2',
            'application/x-rar-compressed'                                              => 'rar',
            'application/x-7z-compressed'                                               => '7z',

            // 视频
            'video/mp4'                                                                 => 'mp4',
            'video/x-msvideo'                                                           => 'avi',
            'video/x-flv'                                                               => 'flv',
            'video/ogg'                                                                 => 'ogv',
            'video/webm'                                                                => 'webm',
            'video/mpeg'                                                                => 'mpg',
            'video/quicktime'                                                           => 'mov',
            'video/x-ms-wmv'                                                            => 'wmv',
            'video/x-matroska'                                                          => 'mkv',
            'video/x-rmvb'                                                              => 'rmvb',
            'video/3gpp'                                                                => '3gp',
            'video/3gpp2'                                                               => '3g2',
            'video/x-ms-asf'                                                            => 'asf',

            // 图像
            'image/jpeg'                                                                => 'jpg',
            'image/pjpeg'                                                               => 'jpg',
            'image/png'                                                                 => 'png',
            'image/gif'                                                                 => 'gif',
            'image/svg+xml'                                                             => 'svg',
            'image/webp'                                                                => 'webp',
            'image/bmp'                                                                 => 'bmp',
            'image/tiff'                                                                => 'tiff',
            'image/heif'                                                                => 'heif',
            'image/heic'                                                                => 'heic',
            'image/jfif'                                                                => 'jfif',
            'image/vnd.adobe.photoshop'                                                 => 'psd',
            'image/x-icon'                                                              => 'ico',
            'image/x-cmu-raster'                                                        => 'ras',
            'image/x-portable-pixmap'                                                   => 'ppm',

            // 音频
            'audio/mpeg'                                                                => 'mp3',
            'audio/wav'                                                                 => 'wav',
            'audio/ogg'                                                                 => 'ogg',
            'audio/x-midi'                                                              => 'mid',
            'audio/aac'                                                                 => 'aac',
            'audio/x-ms-wma'                                                            => 'wma',
            'audio/flac'                                                                => 'flac',
            'audio/x-realaudio'                                                         => 'ra',
            'audio/opus'                                                                => 'opus',
            'audio/x-aac'                                                               => 'aac',
            'audio/aiff'                                                                => 'aiff',
            'audio/x-m4a'                                                               => 'm4a',

            // 文本和网页
            'text/html'                                                                 => 'html',
            'text/css'                                                                  => 'css',
            'text/javascript'                                                           => 'js',
            'text/xml'                                                                  => 'xml',
            'text/markdown'                                                             => 'md',
            'text/x-shellscript'                                                        => 'sh',
            'text/plain'                                                                => 'txt',
            'text/csv'                                                                  => 'csv',

            // 应用程序
            'application/octet-stream'                                                  => 'bin',
            'application/x-shockwave-flash'                                             => 'swf',
            'application/vnd.android.package-archive'                                   => 'apk',
            'application/epub+zip'                                                      => 'epub',
            'application/x-font-ttf'                                                    => 'ttf',
            'application/x-font-opentype'                                               => 'otf',
            'application/font-woff'                                                     => 'woff',
            'application/font-woff2'                                                    => 'woff2',
            'application/x-font-woff'                                                   => 'woff',
            'application/vnd.ms-cab-compressed'                                         => 'cab',
            'application/x-quicktimeplayer'                                             => 'qtl',
            'application/x-msdownload'                                                  => 'exe',
            'application/x-msdos-program'                                               => 'exe',
            'application/x-ms-dos-executable'                                           => 'exe',

        ];

        public static function parse(string $message): UpdateMessage
        {
            $massageObj = new static($message);

            return $massageObj->parseMassage();
        }

        public function __construct(public string $message)
        {
            if (is_null(static::$snowflake))
            {
                static::$snowflake = new Snowflake();
            }
        }

        public function parseMassage(): static
        {
            $this->messageRow = json_decode($this->message, true);

            if (isset($this->messageRow['message']))
            {
                $this->messageFromType = static::MSG_FROM_TYPE_MESSAGE;
                $this->messageBody     = $this->messageRow['message'];
            }

            if (isset($this->messageRow['edited_message']))
            {
                $this->messageFromType = static::MSG_FROM_TYPE_EDITED_MESSAGE;
                $this->messageBody     = $this->messageRow['edited_message'];
            }

            if (isset($this->messageRow['channel_post']))
            {
                $this->messageFromType = static::MSG_FROM_TYPE_CHANNEL_POST;
                $this->messageBody     = $this->messageRow['channel_post'];
            }

            if (isset($this->messageRow['edited_channel_post']))
            {
                $this->messageFromType = static::MSG_FROM_TYPE_EDITED_CHANNEL_POST;
                $this->messageBody     = $this->messageRow['edited_channel_post'];
            }

            /**
             * ********************************************************
             * ********************************************************
             */
            $this->messageLoadType = 0;  // 先清零
            $this->mediaType       = '';
            $this->fileId          = '';
            $this->fileUniqueId    = '';
            $this->fileSize        = 0;
            $this->fileName        = '';
            $this->mimeType        = '';
            $this->ext             = '';

            if (isset($this->messageBody['text']))
            {
                $this->messageLoadType = self::MSG_CARRIER_TYPE_TEXT;
                $this->text            = $this->messageBody['text'];

                if (isset($this->messageBody['entities']))
                {
                    $this->text = self::parseCaptionWithEntities($this->messageBody['text'], $this->messageBody['entities']);
                    $this->tags = self::extractHashtags($this->messageBody['text'], $this->messageBody['entities']);
                }
            }

            elseif (isset($this->messageBody['animation']))
            {
                $this->messageLoadType = self::MSG_CARRIER_TYPE_ANIMATION;
                $this->mediaType       = 'animation';
                $this->fileId          = $this->messageBody['animation']['file_id'];
                $this->fileUniqueId    = $this->messageBody['animation']['file_unique_id'];
                $this->fileSize        = $this->messageBody['animation']['file_size'] ?? 0;
                $this->mimeType        = $this->messageBody['animation']['mime_type'] ?? 'video/mp4';
                $this->ext             = self::getExt($this->messageBody['animation'], 'gif');
                $this->fileName        = $this->messageBody['animation']['file_name'] ?? '';
            }

            elseif (isset($this->messageBody['video']))
            {
                $this->messageLoadType = self::MSG_CARRIER_TYPE_VIDEO;
                $this->mediaType       = 'video';
                $this->fileId          = $this->messageBody['video']['file_id'];
                $this->fileUniqueId    = $this->messageBody['video']['file_unique_id'];
                $this->fileSize        = $this->messageBody['video']['file_size'] ?? 0;
                $this->mimeType        = $this->messageBody['video']['mime_type'] ?? 'video/mp4';
                $this->ext             = self::getExt($this->messageBody['video'], 'mp4');
                $this->fileName        = $this->messageBody['video']['file_name'] ?? '';
            }

            elseif (isset($this->messageBody['photo']))
            {
                $this->messageLoadType = self::MSG_CARRIER_TYPE_PHOTO;
                $this->mediaType       = 'photo';
                $phonoList             = array_reverse($this->messageBody['photo']);   // 取最大尺寸
                $best                  = $phonoList[0];
                $this->fileId          = $best['file_id'];
                $this->fileUniqueId    = $best['file_unique_id'];
                $this->fileSize        = $best['file_size'] ?? 0;
                $this->mimeType        = 'image/jpeg';
                $this->ext             = 'jpg';
            }

            elseif (isset($this->messageBody['audio']))
            {
                $this->messageLoadType = self::MSG_CARRIER_TYPE_AUDIO;
                $this->mediaType       = 'audio';
                $this->fileId          = $this->messageBody['audio']['file_id'];
                $this->fileUniqueId    = $this->messageBody['audio']['file_unique_id'];
                $this->fileSize        = $this->messageBody['audio']['file_size'] ?? 0;
                $this->fileName        = $this->messageBody['audio']['file_name'] ?? '';
                $this->mimeType        = $this->messageBody['audio']['mime_type'] ?? 'audio/mpeg';
                $this->ext             = self::getExt($this->messageBody['audio'], 'mp3');
            }

            elseif (isset($this->messageBody['document']))
            {
                $this->messageLoadType = self::MSG_CARRIER_TYPE_DOCUMENT;
                $this->mediaType       = 'document';
                $this->fileId          = $this->messageBody['document']['file_id'];
                $this->fileUniqueId    = $this->messageBody['document']['file_unique_id'];
                $this->fileSize        = $this->messageBody['document']['file_size'] ?? 0;
                $this->fileName        = $this->messageBody['document']['file_name'] ?? '';
                $this->mimeType        = $this->messageBody['document']['mime_type'] ?? 'application/octet-stream';
                $this->ext             = self::getExt($this->messageBody['document'], '');
            }

            elseif (isset($this->messageBody['sticker']))
            {
                $this->messageLoadType = self::MSG_CARRIER_TYPE_STICKER;
                $this->mediaType       = 'sticker';
                $this->fileId          = $this->messageBody['sticker']['file_id'];
                $this->fileUniqueId    = $this->messageBody['sticker']['file_unique_id'];
                $this->fileSize        = $this->messageBody['sticker']['file_size'] ?? 0;
                $this->mimeType        = 'image/webp';
                $this->ext             = 'webp';
            }

            elseif (isset($this->messageBody['location']))
            {
                $this->messageLoadType = self::MSG_CARRIER_TYPE_LOCATION;
                $this->mediaType       = 'location';
                $this->latitude        = $this->messageBody['location']['latitude'] ?? 0;
                $this->longitude       = $this->messageBody['location']['longitude'] ?? 0;
            }

            elseif (isset($this->messageBody['poll']))
            {
                $this->messageLoadType = self::MSG_CARRIER_TYPE_POLL;
                $this->mediaType       = 'poll';

                $poll = $this->messageBody['poll'];

                // 内核字段 - 几乎一定会有
                $this->pollQuestion    = $poll['question'] ?? '';
                $this->pollIsAnonymous = $poll['is_anonymous'] ?? false;
                $this->pollType        = $poll['type'] ?? 'regular';  // regular 或 quiz

                // 选项（最重要）
                $this->pollOptions = [];
                if (isset($poll['options']) && is_array($poll['options']))
                {
                    foreach ($poll['options'] as $option)
                    {
                        $this->pollOptions[] = [
                            'text'        => $option['text'] ?? '',
                            'voter_count' => $option['voter_count'] ?? 0,
                        ];
                    }
                }

                // Quiz 专属字段
                if ($this->pollType === 'quiz')
                {
                    $this->pollCorrectOptionId = $poll['correct_option_id'] ?? null;
                    $this->pollExplanation     = $poll['explanation'] ?? '';

                    // 如果有 explanation_entities（格式化文本）
                    if (isset($poll['explanation_entities']))
                    {
                        $this->pollExplanation = self::parseCaptionWithEntities($this->pollExplanation, $poll['explanation_entities']);
                    }
                }

                // 投票持续时间或关闭时间（两者择一）
                if (isset($poll['open_period']))
                {
                    $this->pollOpenPeriod = (int)($poll['open_period']);
                }
                if (isset($poll['close_date']))
                {
                    $this->pollCloseDate = (int)($poll['close_date']);
                }

                // 可选：是否允许多选（Telegram 目前 regular poll 固定 false，未来可能有变化）
                $this->pollAllowsMultipleAnswers = $poll['allows_multiple_answers'] ?? false;

                // 方便后续显示或存 DB 的简化文本版本
                $this->text = "[投票] " . $this->pollQuestion;
                if ($this->caption)
                {
                    $this->text .= "\n" . $this->caption;
                }
            }

            /**
             * ********************************************************
             * ********************************************************
             */

            if (isset($this->messageBody['caption']))
            {
                $captionText = $this->messageBody['caption'];

                if (isset($this->messageBody['caption_entities']))
                {
                    $this->caption = self::parseCaptionWithEntities($captionText, $this->messageBody['caption_entities']);
                    $this->tags    = array_merge($this->tags, self::extractHashtags($captionText, $this->messageBody['caption_entities']));
                }
                else
                {
                    $this->caption = $captionText;
                }
            }

            if (isset($this->messageBody['media_group_id']))
            {
                $this->mediaGroupId = $this->messageBody['media_group_id'];
            }

            if (!$this->mediaGroupId)
            {
                $this->mediaGroupId = static::$snowflake->id();
            }

            // ==================== 基础信息赋值 ====================
            $this->chatId        = $this->messageBody['chat']['id'] ?? 0;
            $this->chatFirstName = $this->messageBody['chat']['first_name'] ?? '';
            $this->chatLastName  = $this->messageBody['chat']['last_name'] ?? '';
            $this->chatUsername  = $this->messageBody['chat']['username'] ?? '';
            $this->chatType      = $this->messageBody['chat']['type'] ?? '';

            // ==================== from 发送者信息 ====================
            $from                = $this->messageBody['from'] ?? [];
            $this->fromId        = $from['id'] ?? 0;
            $this->fromIsBot     = $from['is_bot'] ?? false;
            $this->fromFirstName = $from['first_name'] ?? '';
            $this->fromLastName  = $from['last_name'] ?? '';
            $this->fromUsername  = $from['username'] ?? '';

            $this->date            = $this->messageBody['date'] ?? 0;
            $this->messageId       = $this->messageBody['message_id'] ?? 0;
            $this->updateId        = $this->messageRow['update_id'] ?? 0;
            $this->isTopicMessage  = $this->messageRow['is_topic_message'] ?? false;
            $this->messageThreadId = $this->messageRow['message_thread_id'] ?? 0;

            $replyTo = $this->messageBody['reply_to_message'] ?? null;
            if ($replyTo)
            {
                // reply_to 的 from（被回复消息的发送者）
                $replyFrom                  = $replyTo['from'] ?? [];
                $this->replyToFromId        = $replyFrom['id'] ?? 0;
                $this->replyToFromIsBot     = $replyFrom['is_bot'] ?? false;
                $this->replyToFromFirstName = $replyFrom['first_name'] ?? '';
                $this->replyToFromLastName  = $replyFrom['last_name'] ?? '';
                $this->replyToFromUsername  = $replyFrom['username'] ?? '';

                // reply_to 的 chat（被回复消息所在的聊天）
                $replyChat                  = $replyTo['chat'] ?? [];
                $this->replyToChatId        = $replyChat['id'] ?? 0;
                $this->replyToChatFirstName = $replyChat['first_name'] ?? '';
                $this->replyToChatLastName  = $replyChat['last_name'] ?? '';
                $this->replyToChatUsername  = $replyChat['username'] ?? '';
                $this->replyToChatType      = $replyChat['type'] ?? '';
                $this->replyDate            = $replyChat['date'] ?? 0;
            }

            return $this;
        }

        public function isNeededType(): bool
        {
            return in_array($this->messageLoadType, [
                    static::MSG_CARRIER_TYPE_TEXT,
                    static::MSG_CARRIER_TYPE_VIDEO,
                    static::MSG_CARRIER_TYPE_PHOTO,
                    static::MSG_CARRIER_TYPE_AUDIO,
                    static::MSG_CARRIER_TYPE_DOCUMENT,
                ]) and in_array($this->messageFromType, [
                    static::MSG_FROM_TYPE_MESSAGE,
                    static::MSG_FROM_TYPE_EDITED_MESSAGE,
                    static::MSG_FROM_TYPE_CHANNEL_POST,
                    static::MSG_FROM_TYPE_EDITED_CHANNEL_POST,
                ]);
        }

        protected static function getExt(array $msgBody, string $default = '-'): string
        {
            if (isset($msgBody['mime_type']) && isset(static::$mimeTypesMap[$msgBody['mime_type']]))
            {
                $ext = static::$mimeTypesMap[$msgBody['mime_type']];
            }
            elseif (isset($msgBody['file_name']))
            {
                $ext = pathinfo($msgBody['file_name'], PATHINFO_EXTENSION);
            }
            else
            {
                $ext = $default;
            }

            return strtolower($ext);
        }

        protected static function getMimeType(array $msgBody, string $default = '-'): string
        {
            return $msgBody['mime_type'] ?? $default;
        }

        // Function to calculate the length of a string in UTF-16 code points.
        protected static function mbStrlen(string $text): int
        {
            $length     = 0;
            $textlength = \strlen($text);
            for ($x = 0; $x < $textlength; $x++)
            {
                $char = \ord($text[$x]);
                if (($char & 0xc0) != 0x80)
                {
                    $length += 1 + ($char >= 0xf0 ? 1 : 0);
                }
            }

            return $length;
        }

        // Function to substring a string based on UTF-16 encoding.
        protected static function mbSubstr(string $text, int $offset, ?int $length = null): string
        {
            /** @var string */
            $converted = \mb_convert_encoding($text, 'UTF-16');

            /** @var string */
            return \mb_convert_encoding(\substr($converted, $offset << 1, $length === null ? null : ($length << 1),), 'UTF-8', 'UTF-16',);
        }

        // Main function to parse text with entities.
        protected static function parseCaptionWithEntities($text, $caption_entities): string
        {
            $result     = [];
            $lastOffset = 0;

            foreach ($caption_entities as $entity)
            {
                if ($entity['type'] === 'text_link')
                {
                    // Append text before this entity
                    $start    = $entity['offset'];
                    $length   = $entity['length'];
                    $result[] = self::mbSubstr($text, $lastOffset, $start - $lastOffset);

                    // Append the link text
                    $linkText = self::mbSubstr($text, $start, $length);
                    $result[] = "{<$linkText><{$entity['url']}>}";

                    // Update last offset
                    $lastOffset = $start + $length;
                }
            }

            // Append any remaining text after the last entity
            if ($lastOffset < self::mbStrlen($text))
            {
                $result[] = self::mbSubstr($text, $lastOffset);
            }

            return implode('', $result);
        }

        protected function extractHashtags($caption, $caption_entities): array
        {
            // 初始化保存hashtag文本的数组
            $hashtags = [];

            // 遍历所有实体
            foreach ($caption_entities as $entity)
            {
                // 检查实体类型是否为hashtag
                if ($entity['type'] === 'hashtag')
                {
                    // 使用mbSubstr方法获取hashtag的文本
                    $hashtagLength = $entity['length'];
                    $hashtagOffset = $entity['offset'];

                    // 获取hashtag文本
                    $hashtag    = self::mbSubstr($caption, $hashtagOffset, $hashtagLength);
                    $hashtags[] = $hashtag;
                }
            }

            return $hashtags;
        }
    }




