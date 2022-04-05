<?php
declare(strict_types=1);
namespace Moon;

use Exception;

class MoonException extends Exception {
    function __construct(string $message = "", int $code = 500)
    {
        parent::__construct($message, $code);
    }
}
