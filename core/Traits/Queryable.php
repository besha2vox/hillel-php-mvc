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

    static public function select(array $columns = ['*']): static|false
    {
        static::resetQuery();
        $obj = new static;
        $obj->commands[] = 'select';
        static::$query = 'SELECT ' . implode(',', $columns) . ' FROM ' . static::$table;
        return $obj;
    }

    static public function find(int $id): static|false
    {
        $query = DB::connect()->prepare('SELECT * FROM ' . static::$table . ' WHERE id=:id');
        $query->bindParam('id', $id);

        if (!$query->execute()) {
            throw new \PDOException('Failed to execute query');
        }

        return $query->fetchObject(static::class);
    }

    static public function findBy(string $column, mixed $value): static|false
    {
        $query = DB::connect()->prepare('SELECT * FROM ' . static::$table . " WHERE $column = :$column");
        $query->bindParam($column, $value);

        if (!$query->execute()) {
            throw new \PDOException('Failed to execute query');
        }

        return $query->fetchObject(static::class);
    }

    static public function create(array $fields): null|static
    {
        $params = static::prepareQueryParams($fields);

        $query = db()->prepare(
            "INSERT INTO " . static::$table . " ($params[keys]) VALUES ($params[placeholders])"
        );

        foreach ($fields as $key => &$value) {
            $query->bindParam(':' . $key, $value);
        }

        if (!$query->execute()) {
            return null;
        }

        return static::find(db()->lastInsertId());
    }

    static protected function prepareQueryParams(array $fields): array
    {
        $keys = array_keys($fields);
        $placeholders = preg_filter('/^/', ':', $keys);
        return [
            'keys' => implode(', ', $keys),
            'placeholders' => implode(', ', $placeholders),
        ];
    }

    public function where(string $column, SQL $operator = SQL::EQUAL, mixed $value = null): static
    {
        $this->require(['order', 'limit', 'having', 'group', 'where'], 'WHERE can not be used after');
        $this->require(['select'], 'WHERE can not be used without ', true);

        return $this->prepareAdditionalQuery($column, $operator, $value, 'where');
    }

    public function and(string $column, SQL $operator = SQL::EQUAL, mixed $value = null): static
    {
        $this->require(['where'], 'AND can not be used without', true);

        return $this->prepareAdditionalQuery($column, $operator, $value, 'and');
    }

    public function or(string $column, SQL $operator = SQL::EQUAL, mixed $value = null): static
    {
        $this->require(['where'], 'OR can not be used without');

        return $this->prepareAdditionalQuery($column, $operator, $value, 'or');
    }

    protected function prepareAdditionalQuery(string $column, SQL $operator, mixed $value, $method): static
    {
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


        $this->commands[] = $method;

        static::$query .= " " . strtoupper($method) ." $column $operator->value $value";

        return $this;
    }

    public function update(array $fields): static
    {
        $query = "UPDATE " . static::$table . " SET " . $this->updatePlaceholders(array_keys($fields)) . " WHERE id = :id";
        $query = db()->prepare($query);

        $fields['id'] = $this->id;
        $query->execute($fields);

        return static::find($this->id);
    }

    protected function updatePlaceholders(array $keys): string
    {
        $transformedKeys = array_map(function($key) {
            return "`$key` = :$key";
        }, $keys);

        return implode(', ', $transformedKeys);
    }

    static public function delete(int $id): bool
    {
        $query = db()->prepare('DELETE FROM ' . static::$table . ' WHERE id=:id');
        $query->bindParam(':id', $id);

        return $query->execute();
    }

    public function exists(): bool
    {
        $this->require(['select'], 'Method exists() can not be used without', true);

        return !empty($this->get());
    }

    protected function require(array $requireMethods, string $text = '', bool $mustExists = false): void
    {
        foreach ($requireMethods as $method) {
            if (in_array($method, $this->commands)) {
                $message = sprintf(
                    "%s: %s [%s]",
                    static::class,
                    $text,
                    $method
                );

                if ($mustExists) {
                    return;
                }

                throw new Exception($message, Status::UNPROCESSABLE_ENTITY->value);
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
