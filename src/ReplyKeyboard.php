<?php

namespace TeleBot;

class ReplyKeyboard
{
    private $resizeKeyboard;
    private $oneTimeKeyboard;
    private $inputPlaceholder;
    private $selective;

    private $buttons = [];
    private $buttonsPerRow = 0;
    private $rtl = false;

    public function __construct($resizeKeyboard = false, $oneTimeKeyboard = false, $inputPlaceholder = '', $selective = false)
    {
        $this->resizeKeyboard = $resizeKeyboard;
        $this->oneTimeKeyboard = $oneTimeKeyboard;
        $this->inputPlaceholder = $inputPlaceholder;
        $this->selective = $selective;
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

    /**
     * @param  int|array  $buttonsPerRow
     */
    public function chunk($buttonsPerRow)
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
        
        return json_encode([
            'keyboard' => $buttons,
            'resize_keyboard' => $this->resizeKeyboard,
            'one_time_keyboard' => $this->oneTimeKeyboard,
            'input_field_placeholder' => $this->inputPlaceholder,
            'selective' => $this->selective,
        ]);
    }

    private function chunkButtons(): array
    {
        if (is_int($this->buttonsPerRow)) {
            return array_chunk($this->buttons, $this->buttonsPerRow);
        }

        $rows = [];
        while (count($this->buttons)) {
            $rows[] = array_splice($this->buttons, 0, array_shift($this->buttonsPerRow) ?? 100);
        }

        return $rows;
    }

    public function __toString()
    {
        return $this->get();
    }
}
