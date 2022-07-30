[![Latest Stable Version](http://poser.pugx.org/webpajooh/telebot/v)](https://packagist.org/packages/webpajooh/telebot) [![Total Downloads](http://poser.pugx.org/webpajooh/telebot/downloads)](https://packagist.org/packages/webpajooh/telebot) [![PHP Version Require](http://poser.pugx.org/webpajooh/telebot/require/php)](https://packagist.org/packages/webpajooh/telebot) [![License](http://poser.pugx.org/webpajooh/telebot/license)](https://packagist.org/packages/webpajooh/telebot)

# TeleBot
A minimal tool for Telegram bot developers

## Installation
`composer require webpajooh/telebot`

## How to use

### Start point
We start by creating an instance of `TeleBot` class:
```php
try {
    $tg = new TeleBot('YOUR_BOT_TOKEN');
} catch (Throwable $th) {...}
```

### Get the update object
There are short ways to access the `update` object and some important fields. I recommend you to read [the official documentation](https://core.telegram.org/bots/api) to understand these objects well.
```
$tg->update
$tg->message
$tg->chat
$tg->user
```
You also can use `hasCallbackQuery()` method, when you want to check if the `update` object has a `callback_query` field.

### Methods
Thanks to [magic methods](https://www.php.net/manual/en/language.oop5.magic.php), we can use API methods without implementing them, and just call them by name and pass an array as parameter:
```php
$tg->editMessageText([...])
```

### Router
You may define some routes to your bot features; define them by `listen()` method:
```php
$tg->listen('/start', function () use ($tg) {
    $tg->sendMessage([
        'chat_id' => $tg->user->id,
	'text' => 'Hello, world!',
    ]);
}, false);
```
The third parameter that is true by default, makes you able to terminate the script after running a command. In the previous example we passed `false` so script continues.

You can also get parameters and use them as variables:
```php
$tg->listen('set_age_%d', function ($age) use ($tg) {
    // TODO
});
```

TeleBot translates them to regex, so it will be good to take a look at this table to know how to use them efficiently:

| Type |TeleBot| Regex |
|--|--|--|
| Digits | %d | (\d+) |
| String (Anything but a whitespace) | %s | (\S+) |
| Character | %c | (\S) |
| Everything including an empty string| %p | (.*) |

### Logger
Use this if you need to log something into a `log.txt` file:
```php
Logger::log($tg->user->id);
tl($tg->user->id); // Does the same thing
```

### Keyboard
TeleBot includes two classes for making keyboards; `InlineKeyboard` and `ReplyKeyboard`. Here you see an example:
```php
$keyboard = (new InlineKeyboard())
    ->addCallbackButton('📕 Help', 'help_callback')
    ->addUrlButton('📱 Share', 'https://t.me/share/url?url=https://t.me/your_awesome_bot&text=Some text')
    ->chunk(1)
    ->rightToLeft()
    ->get();
```
Next use it like this:
```php
$tg->sendMessage([
    // Other parameters
    'reply_markup' => $keyboard,
]);
```

Consider that `chunk()` method accepts multiple numbers as well! You may pass an array like [1, 3, 2] to build such a keyboard:  
<pre>
[        1        ]  
[ 2 ]  [ 3️ ]  [ 4 ]  
[   5   ] [   6   ]  
</pre>

### Default parameters
Sometimes you do not want to repeat some parameters everywhere, so you can define default parameters for each method. Here are three example that makes it clear how you can use it:
```php
$tg->setDefaults('sendMessage', ['parse_mode' => 'html']); // You will not need passing parse_mode anymore
$tg->setDefaults(['sendMessage', 'banChatMember'], ['chat_id' => $chatId]);
$tg->setDefaults('*', ['chat_id' => $chatId]); // a default parameter for all methods
```

### Extend it!
You may want to add some methods to TeleBot class to improve your code readability and avoid duplication. Look at this simple example as an inspiration:
```php
TeleBot::extend('isReply', function () {
    return property_exists($this->message, 'reply_to_message');
});

if ($tg->isReply()) { ... }
```

## Have you seen a problem?
Create an issue and explain your problem!

## Made with TeleBot
- <a  href="https://github.com/WebPajooh/AntiBot">AntiBot</a>
- <a  href="https://github.com/WebPajooh/MediumBot">MediumBot</a>
