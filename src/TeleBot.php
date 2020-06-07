<?php

namespace TeleBot;

use TeleBot\Util\Http;
use TeleBot\Exceptions\TeleBotException;

class TeleBot
{
    private $endpoint = 'https://api.telegram.org/bot';

    public function __construct($token)
    {
        $this->endpoint .= $token . '/';
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

    private function isMatch($text, $command)
    {
        $map = ['%d' => '(\d+)', '%s' => '(\S+)'];

        $pattern = '/^' . str_replace(array_keys($map), array_values($map), $command) . '$/';
        return preg_match($pattern, $text) === 1;
    }
}
