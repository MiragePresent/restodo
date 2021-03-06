<?php

namespace App\Tool;

use App\DB;

/**
 * Class FieldValidator
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  26.03.2019
 */
class FieldValidator
{
    /**
     * Field name
     *
     * @var string
     */
    private $field;

    /**
     * Validation value
     *
     * @var string
     */
    private $value;

    /**
     * Validation status
     *
     * @var bool
     */
    private $valid = true;

    /**
     * Validation error
     *
     * @var string
     */
    private $error = 'Invalid field';

    public function __construct(string $name, $value)
    {
        $this->field = $name;
        $this->value = (string) $value;
    }

    /**
     * Returns validation status
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * Returns field name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->field;
    }

    /**
     * Returns validation error
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Checks if field is required
     *
     * @return FieldValidator
     */
    public function required(): self
    {
        if ($this->valid) {
            if (!$this->value) {
                $this->valid = false;
                $this->error = "The {$this->field} field is required.";
            }
        }

        return $this;
    }

    /**
     * Checks minimal field length
     *
     * @param int $length
     *
     * @return FieldValidator
     */
    public function min(int $length): self
    {
        if ($this->valid) {
            if (strlen($this->value) < $length) {
                $this->valid = false;
                $this->error = "The {$this->field} must be at least {$length} characters.";
            }
        }

        return $this;
    }

    /**
     * Checks maximal field length
     *
     * @param int $length
     *
     * @return FieldValidator
     */
    public function max(int $length): self
    {
        if ($this->valid) {
            if (strlen($this->value) > $length) {
                $this->valid = false;
                $this->error = "The {$this->field} may not be greater than {$length} characters.";
            }
        }

        return $this;
    }

    /**
     * Checks if field is valid email
     *
     * @return FieldValidator
     */
    public function email(): self
    {
        if ($this->valid) {
            if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
                $this->valid = false;
                $this->error = "The {$this->field} must be a valid email address.";
            }
        }

        return $this;
    }

    /**
     * Checks if value is unique in DB
     *
     * @param string $table
     * @param string $column
     *
     * @return $this
     */
    public function unique(string $table, string $column): self
    {
        if ($this->valid) {
            $query = "select {$column} from {$table} where {$column} = ? limit 1";

            if (!empty(DB::execute($query, [$this->value]))) {
                $this->valid = false;
                $this->error = "The {$this->field} has already been taken.";
            }
        }

        return $this;
    }

    /**
     * Checks if value exists in table
     *
     * @param string $table
     * @param string $column
     *
     * @return $this
     */
    public function exists(string $table, string $column): self
    {
        if ($this->valid) {
            $query = "select {$column} from {$table} where {$column} = ? limit 1";

            if (empty(DB::execute($query, [$this->value]))) {
                $this->valid = false;
                $this->error = "The provided {$this->field} is invalid.";
            }
        }

        return $this;
    }

    /**
     * Checks enum value
     *
     * @param array $allowed
     *
     * @return $this
     */
    public function in(array $allowed): self
    {
        if ($this->valid) {
            if (!in_array($this->value, $allowed)) {
                $this->valid = false;
                $this->error = "The provided {$this->field} is invalid.";
            }
        }

        return $this;
    }

    /**
     * Checks date format
     *
     * @return FieldValidator
     */
    public function isDate(): self
    {
        if ($this->valid) {
            $date = explode("-", $this->value);

            if (!checkdate($date[1] ?? '', $date[2] ?? '', $date[0] ?? '')) {
                $this->valid = false;
                $this->error = "The {$this->field} must be date in format yyyy-mm-dd";
            }
        }

        return $this;
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->__toString();
    }
}
