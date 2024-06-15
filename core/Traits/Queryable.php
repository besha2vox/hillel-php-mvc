<?php

namespace Core\Traits;

use App\Enums\DB\SQL;
use App\Enums\Http\Status;
use Core\DB;
use Exception;
use PDO;

trait Queryable
{
    static protected string|null $table = null;
    static protected string $query = '';
    protected array $commands = [];

//    public static function __callStatic(string $name, array $arguments)
//    {
//        if ($name == 'where') {
//            return call_user_func_array([new static, $name], $arguments);
//        }
//
//        throw new Exception("Static method not allowed", Status::UNPROCESSABLE_ENTITY);
//    }
//
//    public function __call(string $name, array $arguments)
//    {
//        if ($name == 'where') {
//            return call_user_func_array([$this, $name], $arguments);
//        }
//
//        throw new Exception("Static method not allowed", Status::UNPROCESSABLE_ENTITY);
//    }

    static public function select(array $columns = ['*']): static
    {
        static::resetQuery();
        $obj = new static;
        $obj->commands[] = 'select';
        static::$query = 'SELECT ' . implode(',', $columns) . ' FROM ' . static::$table;
        return $obj;
    }

    static public function find(int $id): static
    {
        $query = DB::connect()->prepare('SELECT * FROM ' . static::$table . ' WHERE id=:id');
        $query->bindParam('id', $id);

        if (!$query->execute()) {
            throw new \PDOException('Failed to execute query');
        }

        return $query->fetchObject(static::class);
    }

    static public function findBy(string $column, mixed $value): static
    {
        $query = DB::connect()->prepare('SELECT * FROM ' . static::$table . " WHERE $column = :$column");
        $query->bindParam($column, $value);

        if (!$query->execute()) {
            throw new \PDOException('Failed to execute query');
        }

        return $query->fetchObject(static::class);
    }

    protected function where(string $column, SQL $operator = SQL::EQUAL, mixed $value = null): static
    {
        $this->require(['order', 'limit', 'having', 'group', 'where'], 'WHERE can not be used after');
        $this->require(['select'], 'WHERE can not be used without ');
        $obj = in_array('select', $this->commands) ? $this : static::select();

        if (
            !is_null($value) &&
            !is_bool($value) &&
            !is_numeric($value) &&
            !is_array($value)
        ) {
            $value = "'$value'";
        }

        if (is_null($value)) {
            $value = SQL::NULL->value;
        }

        if (is_array($value)) {
            $value = array_map(fn($item) => is_string($item) && $item !== SQL::NULL->value ? "'$item'" : $item, $value);
            $value = '(' . implode(', ', $value) . ')';
        }

        if (!in_array('where', $obj->commands)) {
            static::$query .= " WHERE";
            $obj->commands[] = 'where';
        }

        static::$query .= " $column $operator->value $value";

        return $obj;
    }

    protected function require(array $requireMethods, string $text = ''): void
    {
        foreach ($requireMethods as $method) {
            if (in_array($method, $this->commands)) {
                $message = sprintf(
                    "%s: %s [%s]",
                    static::class,
                    $text,
                    $method
                );
                throw new Exception($message, Status::UNPROCESSABLE_ENTITY);
            }
        }
    }

    static protected function resetQuery(): void
    {
        static::$query = '';
    }

    public function get(): array
    {
        return DB::connect()->query(static::$query)->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    static public function all(array $columns = ['*']): array
    {
        return static::select($columns)->get();
    }
}
