<?php

declare(strict_types=1);

namespace TeleBotTests;

use PHPUnit\Framework\TestCase;
use TeleBot\InlineKeyboard;

class InlineKeyboardTest extends TestCase
{
    public function test_inline_keyboard_can_have_buttons()
    {
        $keyboard = (new InlineKeyboard())
            ->addUrlButton('Google', 'https://google.com/')
            ->addCallbackButton('Like', 'like_22')
            ->get();

        $this->assertEquals(json_encode([
            'inline_keyboard' => [
                [
                    [
                        'text' => 'Google',
                        'url' => 'https://google.com/',
                    ],
                    [
                        'text' => 'Like',
                        'callback_data' => 'like_22',
                    ],
                ],
            ],
        ]), $keyboard);
    }

    public function test_inline_keyboard_can_be_rtl()
    {
        $keyboard = (new InlineKeyboard())
            ->addUrlButton('Google', 'https://google.com/')
            ->addUrlButton('Bing', 'https://bing.com/')
            ->rightToLeft()
            ->get();

        $this->assertEquals(json_encode([
            'inline_keyboard' => [
                [
                    [
                        'text' => 'Bing',
                        'url' => 'https://bing.com/',
                    ],
                    [
                        'text' => 'Google',
                        'url' => 'https://google.com/',
                    ],
                ],
            ],
        ]), $keyboard);
    }

    public function test_inline_keyboard_can_be_chucked()
    {
        $keyboard = (new InlineKeyboard())
            ->addCallbackButton('Start', 'start')
            ->addCallbackButton('Stop', 'stop')
            ->addCallbackButton('Like', 'like')
            ->addCallbackButton('Dislike', 'dislike')
            ->addCallbackButton('Contact', 'contact')
            ->chunk(2)
            ->get();
        
        $keyboardObject = json_decode($keyboard);

        $this->assertCount(2, $keyboardObject->inline_keyboard[0]);
        $this->assertCount(2, $keyboardObject->inline_keyboard[1]);
        $this->assertCount(1, $keyboardObject->inline_keyboard[2]);
    }
}
