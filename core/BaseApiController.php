<?php

namespace Core;

use App\Enums\Http\Status;
use Exception;

abstract class BaseApiController extends Controller
{
    protected ?Model $model = null;

    abstract protected function getModelClass(): string;

    public function before(string $action, array $params = []): bool
    {
        $this->setModel($action, $params, $this->getModelClass());

        return parent::before($action, $params);
    }

    protected function setModel(string $action, array $params, string $modelClass): void
    {
        if (in_array($action, ['update', 'delete'])) {
            $result = call_user_func_array([$modelClass, 'find'], $params);

            if (!$result) {
                throw new Exception("Folder not found", Status::NOT_FOUND->value);
            }

            $this->model = $result;

            if ($this->model->user_id != authId()) {
                throw new Exception("Access denied", Status::FORBIDDEN->value);
            }
        }
    }
}