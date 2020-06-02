<?php

namespace TeleBot;

class InlineKeyboard
{
    private $buttons = [];
    private $columns = 0;
    private $rtl = false;

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

    public function chunk(int $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function rightToLeft()
    {
        $this->rtl = true;

        return $this;
    }

    public function get()
    {
        $buttons = $this->columns ? array_chunk($this->buttons, $this->columns) : [$this->buttons];

        if ($this->rtl) {
            $rtlButtons = [];
            foreach ($buttons as $buttonRow) {
                $rtlButtons[] = array_reverse($buttonRow);
            }

            $buttons = $rtlButtons;
        }

        return json_encode(['inline_keyboard' => $buttons]);
    }
}
