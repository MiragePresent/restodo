<?php

namespace App\Http\Controllers;

use App\Exception\AuthorizationException;
use App\Http\Request;
use App\Http\Response;
use App\Tool\FieldValidator;
use \InvalidArgumentException;

/**
 * Class Controller
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  25.03.2019
 */
class Controller
{
    /**
     * PSR-7 request
     *
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Controller constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->response = new Response();
    }

    /**
     * @param FieldValidator[] ...$fields
     *
     * @return array
     */
    protected function getErrors(...$fields): array
    {
        $errors = [];

        foreach ($fields as $field) {
            if (!$field instanceof FieldValidator) {
                throw new InvalidArgumentException("Field item must bu instance of FieldValidator");
            }

            if (!$field->isValid()) {
                $errors[$field->getName()] = $field->getError();
            }
        }

        return $errors;
    }

    protected function requireAuth(): void
    {
        if (!$this->request->auth->isAuthenticated()) {
            throw new AuthorizationException("User is not authorized");
        }
    }
}
