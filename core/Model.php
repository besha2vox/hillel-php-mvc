<?php

namespace Core;

use Core\Traits\Queryable;
use ReflectionClass;
use ReflectionProperty;

abstract class Model
{
    use Queryable;
    public int $id;
    public function toArray(): array
    {
        $data = [];

        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        $vars = (array) $this;

        foreach ($properties as $property) {
            if (in_array($property->getName(), ['commands', 'tableName'])) {
                continue;
            }
            $data[$property->getName()] = $vars[$property->getName()] ?? null;
        }

// TODO check this variant
//        foreach ($properties as $property) {
//            if (in_array($property, ['commands', 'tableName'])) {
//                continue;
//            }
//            $data[$property] = $this->{$property};
//        }

        return $data;
    }

}