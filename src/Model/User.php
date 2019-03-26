<?php

namespace App\Model;

use App\DB;
use DateInterval;
use DateTime;

/**
 * Class User
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  26.03.2019
 */
class User
{
    public $id;

    public $email;

    public $password;

    /**
     * @param array $fields
     *
     * @return array
     */
    public static function findBy(array $fields): array
    {
        $query = "select * from users WHERE 1";
        $input = [];
        $result = [];

        foreach ($fields as $name => $value) {
            if (!property_exists(new static(), $name)) {
                throw new \InvalidArgumentException("Criteria {$name} is not valid");
            }

            $query .= " AND {$name} = ?";
            $input[] = $value;
        }

        $rows = DB::execute($query, $input);

        if (is_array($rows)) {
            foreach ($rows as $userData) {
                $user = new static();
                $user->id = $userData["id"];
                $user->email = $userData["email"];
                $user->password = $userData["password"];

                $result[] = $user;
            }
        }

        return $result;
    }

    public function getToken(): string
    {
        $token = bin2hex(random_bytes(120));
        $expireAt = (new DateTime())->add(new DateInterval("PT2H"));

        DB::execute("delete from user_tokens where user_id = ? and expire_at >= now()", [$this->id]);

        DB::execute("insert into user_tokens (user_id, token, expire_at) values(?, ?, ?)", [
            $this->id,
            $token,
            $expireAt->format("Y-m-d H:i:s")
        ]);

        return $token;
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

    public function create(): self
    {
        $query = "insert into users (email, password) values (?, ?)";

        $this->id = DB::execute($query, [$this->email, $this->password]);

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
