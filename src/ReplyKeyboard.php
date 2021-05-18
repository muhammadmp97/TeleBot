<?php

namespace TeleBot;

class ReplyKeyboard
{
    private $resizeKeyboard;
    private $oneTimeKeyboard;
    private $selective;

    private $buttons = [];
    private $columns = 0;
    private $rtl = false;

    public function __construct($resizeKeyboard = false, $oneTimeKeyboard = false, $selective = false)
    {
        $this->resizeKeyboard = $resizeKeyboard;
        $this->oneTimeKeyboard = $oneTimeKeyboard;
        $this->selective = $selective;

        return $this;
    }

    public function addButton($text, $requestContact = false, $requestLocation = false)
    {
        $this->buttons[] = [
            'text' => $text,
            'request_contact' => $requestContact,
            'request_location' => $requestLocation
        ];

        return $this;
    }

    public function addButtons(...$buttons)
    {
        foreach ($buttons as $button) {
            $this->buttons[] = $button;
        }

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
        
        return json_encode([
            'keyboard' => $buttons,
            'resize_keyboard' => $this->resizeKeyboard,
            'one_time_keyboard' => $this->oneTimeKeyboard,
            'selective' => $this->selective
        ]);
    }

    public function __toString()
    {
        return $this->get();
    }
}
