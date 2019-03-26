<?php

namespace App\Exception;

use Throwable;

/**
 * Class AuthenticationException
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  26.03.2019
 */
class AuthorizationException extends \Exception implements HttpExceptionInterface
{
    public function __construct(string $message = "", int $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
