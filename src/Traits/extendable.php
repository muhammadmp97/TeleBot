<?php

namespace TeleBot\Traits;

trait Extendable
{
    private static $extensions = [];

    public static function extend(string $name, callable $extension)
    {
        static::$extensions[$name] = $extension;
    }

    private static function hasExtension(string $name)
    {
        return in_array($name, array_keys(static::$extensions));
    }
}
