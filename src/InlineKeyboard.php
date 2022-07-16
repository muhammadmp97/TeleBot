<?php

namespace TeleBot;

class InlineKeyboard
{
    private $buttons = [];
    private $columns = 0;
    private $rtl = false;

    public function addUrlButton($text, $url)
    {
        return $this->addButton($text, $url);
    }

    public function addCallbackButton($text, $data)
    {
        return $this->addButton($text, null, null, $data);
    }

    public function addButton($text, $url = '', $loginUrl = '', $callbackData = '', $switchInlineQuery = '', $switchInlineQueryCurrentChat = '')
    {
        $button = ['text' => $text];

        if ($url) {
            $button['url'] = $url;
        }

        if ($loginUrl) {
            $button['login_url'] = $loginUrl;
        }

        if ($callbackData) {
            $button['callback_data'] = $callbackData;
        }

        if ($switchInlineQuery) {
            $button['switch_inline_query'] = $switchInlineQuery;
        }

        if ($switchInlineQuery) {
            $button['switch_inline_query_current_chat'] = $switchInlineQueryCurrentChat;
        }

        $this->buttons[] = $button;

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
            $buttons = array_map(fn ($buttonRow) => array_reverse($buttonRow), $buttons);
        }

        return json_encode(['inline_keyboard' => $buttons]);
    }

    public function __toString()
    {
        return $this->get();
    }
}
