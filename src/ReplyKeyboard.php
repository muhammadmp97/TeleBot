<?php

namespace TeleBot;

class ReplyKeyboard
{
    private $resizeKeyboard;
    private $oneTimeKeyboard;
    private $selective;

    private $buttons = [];

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

    public function get()
    {
        return json_encode([
            'keyboard' => [$this->buttons],
            'resize_keyboard' => $this->resizeKeyboard,
            'one_time_keyboard' => $this->oneTimeKeyboard,
            'selective' => $this->selective
        ]);
    }
}