<?php

namespace App\Model;

/**
 * Class Task
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  26.03.2019
 */
class Task extends Model
{
    public const TABLE = 'tasks';

    public const PRIORITY_LOW = "LOW";

    public const PRIORITY_NORMAL = "NORMAL";

    public const PRIORITY_HIGH = "HIGH";

    public const ALLOWED_PRIORITY = [
        self::PRIORITY_LOW,
        self::PRIORITY_NORMAL,
        self::PRIORITY_HIGH,
    ];

    private const PRIORITY_MAP = [
        self::PRIORITY_LOW => 2,
        self::PRIORITY_NORMAL => 1,
        self::PRIORITY_HIGH => 0,
    ];

    public $userId;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $dueDate;

    /**
     * @var int
     */
    public $priority;

    public $isDone;

    public function setPriority(string $priorityName): self
    {
        $this->priority = static::PRIORITY_MAP[$priorityName];

        return $this;
    }

    /**
     * Returns priority name
     *
     * @return string
     */
    public function getPriorityName(): string
    {
        return array_search((int) $this->priority, static::PRIORITY_MAP, true);
    }

    public function toArray(): array
    {
        $arr = parent::toArray();
        $arr["priority"] = $this->getPriorityName();
        $arr["dueDate"] = substr($arr["dueDate"], 0, 10);
        $arr["isDone"] = (bool) $arr["isDone"];

        unset($arr["userId"]);

        return $arr;
    }
}
