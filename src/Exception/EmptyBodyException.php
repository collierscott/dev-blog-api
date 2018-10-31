<?php

namespace App\Exception;

use Throwable;

/**
 * Class EmptyBodyException
 */
class EmptyBodyException extends \Exception
{
    public function __construct(
        string $message = "",
        int $code = 0,
        \Throwable $previous = null
    ) {

        if(empty($message)) {
            $message = 'The body of the POST/PUT method cannot be empty.';
        }
        parent::__construct(
            $message,
            $code,
            $previous
        );
    }
}