<?php

namespace Core;

class BaseApiController extends Controller
{
    public function before(string $action, array $params = []): bool
    {
        return parent::before($action, $params);
    }
}