<?php

namespace App\Exception;

use \Exception;
use Throwable;

/**
 * Class BadRequestException
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  25.03.2019
 */
class BadRequestException extends Exception implements HttpExceptionInterface
{
    /**
     * @inheritdoc
     * @see Exception::__construct()
     */
    public function __construct(string $message = "", int $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
