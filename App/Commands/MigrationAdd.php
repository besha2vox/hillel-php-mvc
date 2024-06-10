<?php

namespace App\Commands;

use Core\Interfaces\Command;
use Exception;
use MigrationsCli;

class MigrationAdd implements Command
{
    const string MIGRATIONS_DIR = BASE_DIR . '/migrations';

    public function __construct(public MigrationsCli $cli, public array $args = [])
    {
    }

    public function run(): void
    {
        $this->createDir();
        $this->createMigration();
    }

    protected function createDir(): void
    {
        if (!file_exists(static::MIGRATIONS_DIR)) {
            mkdir(static::MIGRATIONS_DIR);
            $this->cli->info("Directory " . static::MIGRATIONS_DIR . " was created");

        }
    }


    protected function createMigration(): void
    {
        $name = time() . '_' . $this->args[0] . ".sql";
        $fullPath = static::MIGRATIONS_DIR . "/$name";
        try {
            file_put_contents($fullPath, '');
            $this->cli->info("Migration file $name was created");
            $this->cli->notice("Full file path: $fullPath");
        } catch (Exception $e) {
            $this->cli->error($e->getMessage());
        }
    }
}