<?php

declare(strict_types=1);

namespace TeleBotTests;

use PHPUnit\Framework\TestCase;
use TeleBot\ReplyKeyboard;

class ReplyKeyboardTest extends TestCase
{
    public function test_reply_keyboard_can_have_buttons()
    {
        $keyboard = (new ReplyKeyboard(true, true, true))
            ->addButton('Subscribe')
            ->addButton('Unsubscribe')
            ->get();

        $this->assertEquals(json_encode([
            'keyboard' => [
                [
                    [
                        'text' => 'Subscribe',
                        'request_contact' => false,
                        'request_location' => false,
                    ],
                    [
                        'text' => 'Unsubscribe',
                        'request_contact' => false,
                        'request_location' => false,
                    ],
                ],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
            'selective' => true,
        ]), $keyboard);
    }

    public function test_reply_keyboard_can_be_rtl()
    {
        $keyboard = (new ReplyKeyboard())
            ->addButton('Subscribe')
            ->addButton('Unsubscribe')
            ->rightToLeft()
            ->get();

        $this->assertEquals(json_encode([
            'keyboard' => [
                [
                    [
                        'text' => 'Unsubscribe',
                        'request_contact' => false,
                        'request_location' => false,
                    ],
                    [
                        'text' => 'Subscribe',
                        'request_contact' => false,
                        'request_location' => false,
                    ],
                ],
            ],
            'resize_keyboard' => false,
            'one_time_keyboard' => false,
            'selective' => false,
        ]), $keyboard);
    }

    public function test_reply_keyboard_can_be_chucked_by_int()
    {
        $keyboard = (new ReplyKeyboard())
            ->addButton('Subscribe')
            ->addButton('Unsubscribe')
            ->addButton('Settings')
            ->addButton('Help')
            ->chunk(2)
            ->get();
        
        $keyboardObject = json_decode($keyboard);

        $this->assertCount(2, $keyboardObject->keyboard[0]);
        $this->assertCount(2, $keyboardObject->keyboard[1]);
    }

    public function test_reply_keyboard_can_be_chucked_by_array()
    {
        $keyboard = (new ReplyKeyboard())
            ->addButton('Subscribe')
            ->addButton('Unsubscribe')
            ->addButton('Settings')
            ->addButton('Help')
            ->chunk([1, 1])
            ->get();
        
        $keyboardObject = json_decode($keyboard);

        $this->assertCount(1, $keyboardObject->keyboard[0]);
        $this->assertCount(1, $keyboardObject->keyboard[1]);
        $this->assertCount(2, $keyboardObject->keyboard[2]);
    }
}
