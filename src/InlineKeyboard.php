<?php

namespace TeleBot;

class InlineKeyboard
{
    private $buttons = [];
    private $buttonsPerRow = 0;
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

    public function chunk(int|array $buttonsPerRow)
    {
        $this->buttonsPerRow = $buttonsPerRow;

        return $this;
    }

    public function rightToLeft()
    {
        $this->rtl = true;

        return $this;
    }

    public function get()
    {
        $buttons = $this->buttonsPerRow ? $this->chunkButtons() : [$this->buttons];

        if ($this->rtl) {
            $buttons = array_map(fn ($buttonRow) => array_reverse($buttonRow), $buttons);
        }

        return json_encode(['inline_keyboard' => $buttons]);
    }

    private function chunkButtons(): array
    {
        if (is_int($this->buttonsPerRow)) {
            return array_chunk($this->buttons, $this->buttonsPerRow);
        }

        $buttonCount = count($this->buttons);
        $rowIndex = 0;
        $rows = [];
        for ($i = 0; $i < $buttonCount;) {
            $rows[] = array_slice($this->buttons, $i, $this->buttonsPerRow[$rowIndex] ?? 100);
            $i += $this->buttonsPerRow[$rowIndex] ?? 100;
            $rowIndex++;
        }

        return $rows;
    }

    public function __toString()
    {
        return $this->get();
    }
}
