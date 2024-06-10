<?php

namespace App\Commands;

use Core\Interfaces\Command;
use MigrationsCli;

class MigrationRun implements Command
{
    const string MIGRATIONS_DIR = BASE_DIR . '/migrations';
    const string MIGRATIONS_TABLE = '0_migrations.sql';

    public function __construct(MigrationsCli $cli, array $args = [])
    {
    }

    public function run(): void
    {
        $this->createMigrationTable();
    }
    protected function createMigrationTable(): void
    {

    }
}