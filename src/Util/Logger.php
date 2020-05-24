<?php

namespace TeleBot\Util;

class Logger
{
    public static function log($message)
    {
        $filePath = dirname($_SERVER['SCRIPT_FILENAME']) . '/log.txt';
        $time = (new \DateTime())->format('Y-m-d H:i:s');
        $message = "({$time}): {$message}";

        if (file_exists($filePath)) {
            $message .= "\n" . file_get_contents($filePath);
        } else {
            touch($filePath);
        }

        file_put_contents($filePath, $message);
    }
}