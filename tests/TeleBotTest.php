<?php

declare(strict_types=1);

namespace TeleBotTests;

use PHPUnit\Framework\TestCase;
use ReflectionObject;
use TeleBot\Exceptions\TerminationException;
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

    public function test_getters_work_on_callbacks()
    {
        $tg = new TeleBot('some-token');
        $tg->update = json_decode($this->getFakeCallbackUpdate());

        $this->assertEquals('John', $tg->user->first_name);
        $this->assertEquals(333, $tg->message->message_id);
        $this->assertEquals(1962800, $tg->chat->id);
        $this->assertEquals('do_something', $tg->callback_query->data);
    }

    public function test_terminates_if_secret_token_didnt_match()
    {
        $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] = 'password';

        $this->expectException(TerminationException::class);

        (new TeleBot('some-token', 'indeedpassword'));
    }

    public function test_continues_if_secret_token_matched()
    {
        $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] = 'password';

        $this->expectNotToPerformAssertions();

        (new TeleBot('some-token', 'password'));
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

    public function test_withDefaults_works_with_method_name()
    {
        $tg = new TeleBot('some-token');

        $tg->setDefaults('sendMessage', ['parse_mode' => 'html']);

        $object = new ReflectionObject($tg);

        $this->assertEquals(['parse_mode' => 'html'], $object->getMethod('getDefaults')->invoke($tg, 'sendMessage'));
    }

    public function test_withDefaults_works_with_a_star_character()
    {
        $tg = new TeleBot('some-token');

        $tg->setDefaults('*', ['parse_mode' => 'html']);

        $object = new ReflectionObject($tg);

        $this->assertEquals(['parse_mode' => 'html'], $object->getMethod('getDefaults')->invoke($tg, 'sendMessage'));
    }

    public function test_withDefaults_works_with_overrided_parameters()
    {
        $tg = new TeleBot('some-token');

        $tg->setDefaults('*', ['parse_mode' => 'markdown']);

        $tg->setDefaults([
            'sendMessage',
            'sendPhoto',
        ], ['parse_mode' => 'html']);

        $object = new ReflectionObject($tg);

        $this->assertEquals(['parse_mode' => 'html'], $object->getMethod('getDefaults')->invoke($tg, 'sendPhoto'));
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
    
    private function getFakeCallbackUpdate($data = 'do_something')
    {
        return '{
            "update_id": 66049321,
            "callback_query": {
                "id": 500,
                "from": {
                    "id": 1962800,
                    "first_name": "John",
                    "username": "johndoe",
                    "language_code": "en"
                },
                "message": {
                    "message_id": 333,
                    "from": {
                        "id": 1962801,
                        "first_name": "Bot",
                        "username": "mybot",
                        "is_bot": 1
                    },
                    "chat": {
                        "id": 1962800,
                        "first_name": "John",
                        "username": "johndoe",
                        "type": "private"
                    }
                },
                "data": "'.$data.'"
            }
        }';
    }
}
