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
            ->email();

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

        if (!empty(User::findBy(["email" => $email->toString()]))) {
            return $this->fail(
                static::FAILURE_TYPE_INVALID_DATA,
                ["errors" => ["email" => "The email has already been taken."]]
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
}
