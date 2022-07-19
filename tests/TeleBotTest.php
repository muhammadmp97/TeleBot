<?php

declare(strict_types=1);

namespace TeleBotTests;

use PHPUnit\Framework\TestCase;
use TeleBot\TeleBot;

class TeleBotTest extends TestCase
{
    public function test_getters_work_on_normal_messages()
    {
        $tg = new TeleBot('some-token');
        $tg->update = json_decode($this->getFakeNormalMessageUpdate());

        $this->assertEquals('Hello, world!', $tg->message->text);
        $this->assertEquals('John', $tg->user->first_name);
        $this->assertEquals(1962800, $tg->chat->id);
        $this->assertEquals(66049321, $tg->update_id);
    }

    public function test_router_works_properly_with_direct_commands()
    {
        $tg = new TeleBot('some-token');
        $tg->update = json_decode($this->getFakeNormalMessageUpdate('/unsubscribe'));

        $tg->listen('/subscribe', function () {
            $this->assertTrue(false);
        }, false);

        $tg->listen('/unsubscribe', function () {
            $this->assertTrue(true);
        }, false);
    }

    public function test_router_works_properly_with_pattern_commands()
    {
        $tg = new TeleBot('some-token');
        $tg->update = json_decode($this->getFakeNormalMessageUpdate('/charge John 25'));

        $tg->listen('/charge %s %d', function ($name, $amount) {
            $this->assertEquals('John', $name);
            $this->assertEquals(25, $amount);
        }, false);
    }

    private function getFakeNormalMessageUpdate($messageText = 'Hello, world!')
    {
        return '{
            "update_id": 66049321,
            "message": {
                "message_id": 500,
                "from": {
                    "id": 1962800,
                    "first_name": "John",
                    "username": "johndoe",
                    "language_code": "en"
                },
                "chat": {
                    "id": 1962800,
                    "first_name": "John",
                    "username": "johndoe",
                    "type": "private"
                },
                "date": 1655100501,
                "text": "'.$messageText.'"
            }
        }';
    }
}
