<?php

namespace App\Model;

use App\DB;
use DateInterval;
use DateTime;
use DateTimeZone;

/**
 * Class User
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  26.03.2019
 *
 * @method User load()
 */
class User extends Model
{
    public const TABLE = 'users';

    public $email;

    public $password;

    public function getToken(): string
    {
        $token = bin2hex(random_bytes(120));
        $expireAt = (new DateTime('now', new DateTimeZone('UTC')))
            ->add(new DateInterval("PT2H"));

        DB::execute("delete from user_tokens where user_id = ? and expire_at >= now()", [$this->id]);

        DB::execute("insert into user_tokens (user_id, token, expire_at) values(?, ?, ?)", [
            $this->id,
            $token,
            $expireAt->format("Y-m-d H:i:s")
        ]);

        return $token;
    }

    /**
     * Checks user's credentials
     *
     * @return bool
     */
    public function validCredentials()
    {
        return (bool) count($this->findBy(["email" => $this->email, "password" => $this->password]));
    }

    /**
     * @param string $pass
     *
     * @return User
     */
    public function createPassword(string $pass): self
    {
        $this->password = $this->hash($pass);

        return $this;
    }

    /**
     * Creates hashed password
     *
     * @param string $source
     *
     * @return string
     */
    protected function hash(string $source): string
    {
        /** @var string $salt Password salt */
        $salt = "kslgjn4ptnjsg";

        return md5($salt . $source);
    }

}
