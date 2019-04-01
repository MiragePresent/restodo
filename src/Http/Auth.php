<?php

namespace App\Http;

use App\DB;
use App\Model\User;

/**
 * Class Auth
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  26.03.2019
 */
class Auth
{
    private $request;

    /**
     * @var User
     */
    private $user = null;

    /**
     * @var string
     */
    private $token;

    public function __construct(Request $request)
    {
        $this->request = $request;

        if ($this->request->hasHeader("x-auth-token")) {
            $this->token = $this->request->getHeader("x-auth-token");
        }
    }

    public function isAuthenticated(): bool
    {
        return $this->token && $this->getUser();
    }

    public function getUser(): ?User
    {
        if (!$this->user) {
            if ($this->token) {
                $users = DB::execute("
                  select users.* 
                  from users 
                    join user_tokens ut on users.id = ut.user_id
                  where ut.token = ?
                  and ut.expire_at > now()
                  limit 1
                ", [
                    $this->token
                ]);

                if (!empty($users)) {
                    $this->user = new User();
                    $this->user->fromArray(current($users));
                }
            }
        }

        return $this->user;
    }
}
