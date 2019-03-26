<?php

namespace App\Http\Controllers;

use App\DB;
use App\Model\User;
use App\Tool\FieldValidator;

/**
 * Class AuthController
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  25.03.2019
 */
class AuthController extends RestController
{
    public function register()
    {
        $email = (new FieldValidator("email", $this->request->get("email")))
            ->required()
            ->email()
            ->unique('users', 'email');

        $password = (new FieldValidator("password", $this->request->get("password")))
            ->required()
            ->min(6)
            ->max(14);

        if (!$email->isValid() || !$password->isValid()) {
            return $this->fail(
                static::FAILURE_TYPE_INVALID_DATA,
                ["errors" => $this->getErrors($email, $password)]
            );
        }

        try {
            DB::getInstance()->beginTransaction();

            $user = new User();
            $user->email = (string)$email;
            $user->createPassword((string)$password);
            $user->create();

            $token = $user->getToken();

            DB::getInstance()->commitTransaction();
        } catch (\Exception $e) {
            DB::getInstance()->rollback();

            throw $e;
        }

        return $this->success(["token" => $token]);
    }

    public function login()
    {
        $email = (new FieldValidator("email", $this->request->get("email")))
            ->required()
            ->email()
            ->exists('users', 'email');

        $password = (new FieldValidator("password", $this->request->get("password")))
            ->required()
            ->min(6)
            ->max(14);

        $user = new User();
        $user->email = (string) $email;
        $user->createPassword((string) $password);

        if (!$email->isValid() || !$password->isValid() || !$user->validCredentials()) {
            $errors = $this->getErrors($email, $password);

            if (empty($errors)) {
                $errors = ["email" => "These credentials do not match our records."];
            }

            return $this->fail(static::FAILURE_TYPE_INVALID_DATA, compact("errors"));
        }

        return $this->success(["token" => $user->load()->getToken()]);
    }
}
