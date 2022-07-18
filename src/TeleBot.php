<?php

namespace TeleBot;

/**
 * @method setWebhook(array $parameters)
 * @method getMe()
 * @method sendMessage(array $parameters)
 * @method forwardMessage(array $parameters)
 * @method sendPhoto(array $parameters)
 * @method sendAudio(array $parameters)
 * @method sendDocument(array $parameters)
 * @method sendVideo(array $parameters)
 * @method sendAnimation(array $parameters)
 * @method sendVoice(array $parameters)
 * @method sendLocation(array $parameters)
 * @method sendContact(array $parameters)
 * @method sendPoll(array $parameters)
 * @method sendChatAction(array $parameters)
 * @method getUserProfilePhotos(array $parameters)
 * @method getFile(array $parameters)
 * @method kickChatMember(array $parameters)
 * @method unbanChatMember(array $parameters)
 * @method restrictChatMember(array $parameters)
 * @method promoteChatMember(array $parameters)
 * @method exportChatInviteLink(array $parameters)
 * @method setChatPhoto(array $parameters)
 * @method deleteChatPhoto(array $parameters)
 * @method setChatTitle(array $parameters)
 * @method pinChatMessage(array $parameters)
 * @method unpinChatMessage(array $parameters)
 * @method leaveChat(array $parameters)
 * @method getChat(array $parameters)
 * @method getChatAdministrators(array $parameters)
 * @method getChatMembersCount(array $parameters)
 * @method getChatMember(array $parameters)
 * @method answerCallbackQuery(array $parameters)
 * @method editMessageText(array $parameters)
 * @method editMessageCaption(array $parameters)
 * @method editMessageMedia(array $parameters)
 * @method deleteMessage(array $parameters)
 * @method answerInlineQuery(array $parameters)
 * @property message
 * @property chat
 * @property user
 */

use TeleBot\Util\Http;
use TeleBot\Exceptions\TeleBotException;
use TeleBot\Traits\Extendable;

class TeleBot
{
    use Extendable;

    private $token;
    
    public $update;

    public function __construct($token)
    {
        $this->token = $token;
        $this->update = $this->getUpdate();
    }

    public function getUpdate()
    {
        return json_decode(file_get_contents('php://input'));
    }

    public function listen($command, $closure, $thenDie = true)
    {
        $text = $this->hasCallbackQuery() ?
            $this->update->callback_query->data :
            $this->update->message->text;

        if ($text == $command) {
            call_user_func($closure);
            return $this->dieIf($thenDie);
        }
        
        if ($this->isMatch($text, $command)) {
            preg_match($this->createRegexPattern($command), $text, $params);
            $params = array_slice($params, 1);
            call_user_func_array($closure, $params);
            return $this->dieIf($thenDie);
        }
    }

    private function dieIf(bool $condition)
    {
        if ($condition) {
            die();
        }
    }

    public function hasCallbackQuery()
    {
        return isset($this->update->callback_query);
    }

    private function isMatch($text, $command)
    {
        $pattern = $this->createRegexPattern($command);

        return preg_match($pattern, $text) === 1;
    }

    private function createRegexPattern($command)
    {
        $map = ['%d' => '(\d+)', '%s' => '(\S+)', '%c' => '(\S)', '%p' => '(.*)'];
        $pattern = '/^' . str_replace(array_keys($map), array_values($map), str_replace('/', '\/', $command)) . '$/';

        return $pattern;
    }

    public function __call($name, $params)
    {
        if (static::hasExtension($name)) {
            $extension = static::$extensions[$name];
            $extension = $extension->bindTo($this, static::class);

            return $extension(...$params);
        }
        
        $httpResponse = Http::post("https://api.telegram.org/bot{$this->token}/{$name}", $params[0]);

        if (!$httpResponse->ok) {
            throw new TeleBotException($httpResponse->description);
        }

        return $httpResponse->result;
    }

    public function __get($name)
    {
        if ($name === 'message') {
            if (isset($this->update->callback_query)) {
                return $this->update->callback_query->message;
            } elseif (isset($this->update->message)) {
                return $this->update->message;
            }
        }

        if ($name === 'chat') {
            if (isset($this->update->callback_query)) {
                return $this->update->callback_query->message->chat;
            } elseif (isset($this->update->message)) {
                return $this->update->message->chat;
            }
        }

        if ($name === 'user') {
            if (isset($this->update->callback_query)) {
                return $this->update->callback_query->from;
            } elseif (isset($this->update->message)) {
                return $this->update->message->from;
            }
        }

        throw new TeleBotException("Property $name doesn't exist!");
    }
}
