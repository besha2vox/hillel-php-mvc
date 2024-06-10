<?php

const BASE_DIR = __DIR__;
require_once BASE_DIR . '/vendor/autoload.php';

use App\Commands\MigrationAdd;
use Core\Interfaces\Command;
use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

class MigrationsCli extends CLI
{
    protected function setup(Options $options): void
    {

        $options->registerCommand("add", "Create migration file");
        $options->registerCommand("run", "Run all migration file");
        $options->registerArgument('name', 'File name', true, 'add');

        $options->useCompactHelp(true);
    }

    protected function main(Options $options): void
    {
        $cmd = match ($options->getCmd()) {
            "add" => new MigrationAdd($this, $options->getArgs()),
            default => null
        };

        if ($cmd instanceof Command) {
            call_user_func([$cmd, 'run']);
        } else {
            $this->warning("No command specified");
            $this->warning("Use the -h flag to get information about available commands/arguments/options");
        }
    }
}

$cli = new MigrationsCli();
$cli->run();