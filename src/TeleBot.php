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

class TeleBot
{
    private $endpoint = 'https://api.telegram.org/bot';
    public $update;

    public function __construct($token)
    {
        $this->endpoint .= $token . '/';
        $this->update = $this->getUpdate();
    }

    public function getUpdate()
    {
        return json_decode(file_get_contents('php://input'));
    }

    public function listen($command, $closure)
    {
        $update = $this->getUpdate();
        $text = (isset($update->callback_query)) ? $update->callback_query->data : $update->message->text;

        if ($text == $command) {
            call_user_func($closure);
            return;
        } elseif ($this->isMatch($text, $command)) {
            $params = sscanf($text, $command);
            call_user_func_array($closure, $params);
        }
    }

    public function __call($name, $params)
    {
        $httpResponse = Http::post($this->endpoint . $name, $params[0]);

        if (!$httpResponse->ok) {
            throw new TeleBotException($httpResponse->description);
        }

        return $httpResponse->result;
    }

    public function __get($name)
    {
        if ($name === 'message') {
            return $this->update->message;
        }

        if ($name === 'chat') {
            return $this->update->message->chat;
        }

        if ($name === 'user') {
            return $this->update->message->from;
        }

        throw new \Exception("Property $name doesn't exists");
    }

    private function isMatch($text, $command)
    {
        $map = ['%d' => '(\d+)', '%s' => '(\S+)', '%c' => '(\S)'];

        $pattern = '/^' . str_replace(array_keys($map), array_values($map), str_replace('/', '\/', $command)) . '$/';
        return preg_match($pattern, $text) === 1;
    }
}
