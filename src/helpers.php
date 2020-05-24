<?php

if (! function_exists('tl')) {
    function tl($message) {
        \TeleBot\Util\Logger::log($message);
    }
}