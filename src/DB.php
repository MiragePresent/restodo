<?php

namespace App;

use \PDO;

/**
 * Class Db
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  26.03.2019
 */
class DB
{
    /**
     * PDO instance
     *
     * @var PDO
     */
    private $PDO;

    /**
     * @var DB
     */
    private static $db;

    private function __construct(string $username, string $password, string $dbName, string $dbHost)
    {
        $this->PDO = new PDO(
            "mysql:host={$dbHost};dbname={$dbName}",
            $username,
            $password,
            [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]
        );

        $this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    private function __clone() {}

    private function __wakeup() {}

    /**
     * @return DB
     */
    public static function getInstance(): DB
    {
        if (!static::$db) {
            static::$db = new static(
                env("DB_USER", 'root'),
                env("DB_PASSWORD", ""),
                env("DB_NAME"),
                env("DB_HOST", "localhost")
            );
        }

        return static::$db;
    }

    public function beginTransaction()
    {
        return $this->PDO->beginTransaction();
    }

    public function commitTransaction()
    {
        return $this->PDO->commit();
    }

    public function rollback()
    {
        return $this->PDO->rollBack();
    }

    /**
     * Execute query
     *
     * @param string $query SQL-query
     * @param array  $input Query arguments
     *
     * @return array
     */
    public static function execute(string $query, $input = [])
    {
        $result = [];
        $query = static::getInstance()->PDO->prepare($query);

        if ($query) {
            if ($query->execute($input)) {
                if ($rowId = static::$db->PDO->lastInsertId()) {
                    $result = $rowId;
                } elseif ($query->rowCount()) {
                    $result = $query->fetchAll(PDO::FETCH_NAMED);
                } else {
                    $result = $query->rowCount();
                }
            }
        }

        return $result;
    }
}
