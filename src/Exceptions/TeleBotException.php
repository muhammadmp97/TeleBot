<?php

namespace TeleBot\Exceptions;

use Exception;

class TeleBotException extends Exception
{
    public function errorMessage()
    {
        $errorMsg = 'Error on line ' . $this->getLine() . ': <b>' . $this->getMessage() . '</b>';
        return $errorMsg;
    }
}