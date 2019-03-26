<?php

namespace App\Model;

use App\DB;

/**
 * Class Model
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  26.03.2019
 */
class Model
{
    /**
     * Table name
     *
     * @var string
     */
    public const TABLE = "";

    /**
     * Create entity
     *
     * @return Model
     */
    public function create(): self
    {
        $fields = get_object_vars($this);

        $query = sprintf(
            "insert into %s (%s) values (%s)",
            static::TABLE,
            implode(", ", array_keys($fields)),
            implode(", ", array_fill(0, count($fields), "?"))
        );

        $data = [];

        foreach ($fields as $field => $value) {
            $data[] = $value;
        }

        $this->id = DB::execute($query, $data);

        return $this;
    }

    public function load(): self
    {
        $query = sprintf("select * from %s where 1", static::TABLE);
        $input = [];

        $fields = get_object_vars($this);

        foreach ($fields as $field => $value) {
            if ($value) {
                $query .= " and {$field} = ?";
                $input[] = $value;
            }
        }

        if (count($input) === count($fields)) {
            return $this;
        }

        $query .= " limit 2";

        $all = DB::execute($query, $input);

        if (count($all) !== 1) {
            throw new \Exception("Cannot load user. There were found " . count($all) ." record");
        }

        foreach (current($all) as $field => $value) {
            if (!$this->{$field}) {
                $this->{$field} = $value;
            }
        }

        return $this;
    }

    /**
     * @param array $fields
     *
     * @return User[]
     */
    public static function findBy(array $fields): array
    {
        $query = sprintf("select * from %s WHERE 1", static::TABLE);
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
            foreach ($rows as $data) {
                $entity = new static();

                foreach (get_class_vars(static::class) as $field) {
                    $entity->{$field} = $data[$field] ?? null;
                }

                $result[] = $entity;
            }
        }

        return $result;
    }
}
