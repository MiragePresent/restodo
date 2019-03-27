<?php

namespace App\Http\Controllers;

use App\DB;
use App\Exception\BadRequestException;
use App\Exception\NotFoundException;
use App\Http\Request;
use App\Model\Task;
use App\Tool\FieldValidator;

/**
 * Class TaskController
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  26.03.2019
 */
class TaskController extends RestController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->requireAuth();
    }

    public function index()
    {
        $user = $this->request->auth->getUser();

        $limit = $requestLimit = (int) ($_GET["limit"] ?? 10);
        $page = (int) ($_GET["page"] ?? 1);
        $start = ($page - 1) * $limit;
        $total = DB::execute(
            "select count(id) total from tasks where user_id = ?",
            [$user->id]
        )[0]["total"] ?? 0;

        if (!$total) {
            return $this->success(["data" => [], "page" => $page]);
        }

        if ($total < ($limit * $page)) {
            $limit = $limit - ($limit * $page - $total);
        }

        if ($limit <= 0) {
            throw new NotFoundException("Tasks not found");
        }

        $orderProp = !isset($_GET["orderProp"]) ? "due_date" : decamelize($_GET["orderProp"]);
        $orderType = strtolower(!isset($_GET["orderType"]) ? "desc" : decamelize($_GET["orderType"]));

        if (!property_exists(Task::class, camelize($orderProp))) {
            throw new BadRequestException("Cannot order items by {$_GET["orderProp"]}");
        }

        if ($orderType !== "asc") {
            if ($orderType !== "desc") {
                throw new BadRequestException("Ordering type {$orderType} is not valid");
            }
        }

        $query = sprintf(
            "select * from tasks where user_id = ? order by %s %s limit %d, %d",
            $orderProp,
            $orderType,
            $start,
            $limit
        );
        $tasks = DB::execute($query, [$user->id]);
        $result["data"] = [];

        foreach (array_reverse($tasks) as $task) {
            $result["data"][] = (new Task())->fromArray($task)->toArray();
        }

        $result["page"] = $page;
        $result["limit"] = $requestLimit;
        $result["hasNextPage"] = false;

        if ($start + $limit < $total) {
            $result["hasNextPage"] = true;
        }

        return $this->success($result);
    }

    public function create()
    {
        $title = (new FieldValidator("title", $this->request->get("title")))
            ->required()
            ->min(2)
            ->max(200);

        $dueDate = (new FieldValidator("dueDate", $this->request->get("dueDate")))
            ->required()
            ->min(10)
            ->max(10)
            ->isDate();

        $priority = (new FieldValidator("priority", $this->request->get("priority")))
            ->required()
            ->in(Task::ALLOWED_PRIORITY);

        if (!$title->isValid() || !$dueDate->isValid() || !$priority->isValid()) {
            return $this->fail(
                static::FAILURE_TYPE_INVALID_DATA,
                ["errors" => $this->getErrors($title, $dueDate, $priority)]
            );
        }

        $task = new Task();
        $task->userId = $this->request->auth->getUser()->id;
        $task->title = (string) $title;
        $task->dueDate = (string) $dueDate;
        $task->setPriority((string) $priority);
        $task->create();

        $response = $this->success([], 201);
        $response->addHeaders(["Location" => url("/tasks/{$task->id}")]);

        return $response;
    }

    public function done($taskId)
    {
        /** @var Task $task */
        $task = current(Task::findBy(['id' => (int) $taskId]));

        if (!$task || $task->userId !== $this->request->auth->getUser()->id) {
            throw new NotFoundException("Task {$taskId} is not found");
        }

        $task->isDone = 1;
        $task->save();

        return $this->success($task->toArray(), 200);
    }

    public function delete($taskId)
    {
        /** @var Task $task */
        $task = current(Task::findBy(['id' => (int) $taskId]));

        if (!$task || $task->userId !== $this->request->auth->getUser()->id) {
            throw new NotFoundException("Task {$taskId} is not found");
        }

        $task->delete();

        return $this->success([], 200);
    }
}
