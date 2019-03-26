<?php

namespace App\Http\Controllers;

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

        $this->response->addHeaders(["Location" => url("/tasks/{$task->id}")]);

        return $this->success([], 201);
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
