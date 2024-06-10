<?php

namespace Core\Interfaces;

use MigrationsCli;

interface Command
{
    public function __construct(MigrationsCli $cli, array $args = []);

    public function run(): void;
}