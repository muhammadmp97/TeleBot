<?php

namespace TeleBot;

class InlineKeyboard
{
    private $buttons = [];

    public function __construct()
    {
        return $this;
    }

    public function addButton($text, $url = '', $loginUrl = '', $callbackData = '', $switchInlineQuery = '', $switchInlineQueryCurrentChat = '')
    {
        $this->buttons[] = [
            'text' => $text,
            'url' => $url,
            'login_url' => $loginUrl,
            'callback_data' => $callbackData,
            'switch_inline_query' => $switchInlineQuery,
            'switch_inline_query_current_chat' => $switchInlineQueryCurrentChat
        ];

        return $this;
    }

    public function get()
    {
        return json_encode(['inline_keyboard' => [$this->buttons]]);
    }
}