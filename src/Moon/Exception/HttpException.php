<?php
declare(strict_types=1);
namespace Moon\Exception;

use Moon\MoonException;

class HttpException extends MoonException {
    function __construct(string $message = "", int $code = 500)
    {
        parent::__construct($message, $code);
    }
}