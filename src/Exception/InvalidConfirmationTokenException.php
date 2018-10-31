<?php

namespace App\Exception;

use Throwable;

/**
 * Class InvalidConfirmationTokenException
 */
class InvalidConfirmationTokenException extends \Exception
{

    public function __construct(
        string $message = "",
        int $code = 0,
        \Throwable $previous = null
    ) {
        if(empty($message)) {
            $message = 'The confirmation token is invalid.';
        }

        parent::__construct(
            $message,
            $code,
            $previous
        );
    }

}