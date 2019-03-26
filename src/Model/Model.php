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
    public const TABLE = "not_provided_table";

    public $id;

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
            implode(", ", array_map("decamelize", array_keys($fields))),
            implode(", ", array_fill(0, count($fields), "?"))
        );

        $data = [];

        foreach ($fields as $field => $value) {
            $data[] = $value;
        }

        $this->id = DB::execute($query, $data);

        return $this;
    }

    /**
     * Create entity
     *
     * @return Model
     */
    public function save(): self
    {
        $fields = get_object_vars($this);

        $values = "";

        foreach (array_keys($fields) as $field) {
            $values .= ($values ? "," : "") . sprintf(" %s = ?", decamelize($field));
        }

        $query = sprintf("update %s set {$values} where id = ?", static::TABLE);

        $data = [];

        foreach ($fields as $field => $value) {
            $data[] = $value;
        }

        $data[] = $this->id;

        DB::execute($query, $data);

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

    public function delete(): bool
    {
        $query = sprintf("delete from %s where id = ?", static::TABLE);

        return (bool) DB::execute($query, [$this->id]);
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

                foreach ($data as $field => $value) {
                    $camelField = camelize($field);

                    if (property_exists($entity, $camelField)) {
                        $entity->{$camelField} = $value;
                    }
                }

                $result[] = $entity;
            }
        }

        return $result;
    }

    /**
     * Load data from array
     *
     * @param array $data
     *
     * @return Model
     */
    public function fromArray(array $data): self
    {
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }

        return $this;
    }

    public function toArray(): array
    {
        return (array) $this;
    }
}
