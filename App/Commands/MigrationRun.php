<?php

namespace App\Commands;

use Core\Interfaces\Command;
use Exception;
use MigrationsCli;
use PDO;
use PDOException;

class MigrationRun implements Command
{
    const string MIGRATIONS_DIR = BASE_DIR . '/migrations';
    const string MIGRATIONS_TABLE = '0_migrations.sql';

    public function __construct(public MigrationsCli $cli, array $args = [])
    {
    }

    public function run(): void
    {
        try {
            db()->beginTransaction();
            $this->cli->info("Migrations process start...");

            $this->createMigrationTable();
            $this->runMigration();

            if(db()->inTransaction()) {
                db()->commit();
            }
            $this->cli->info("Migrations process end...");

        } catch (PDOException $e) {
            if(db()->inTransaction()) {
                db()->rollback();
            }
            $this->cli->error($e->getMessage(), $e->getTrace());
        } catch (Exception $e) {
            $this->cli->error($e->getMessage(), $e->getTrace());
        }
    }

    protected function createMigrationTable(): void
    {
        $this->cli->info("Run Migration Table...");

        $query = db()->prepare("
CREATE TABLE IF NOT EXISTS migrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE
)"
        );

        if (!$query->execute()) {
            throw new Exception("Failed to create migrations");
        }
        $this->cli->success("Migrations table was created");
    }

    protected function runMigration(): void
    {
        $this->cli->info("Fetch Migrations...");

        $migrations = scandir(static::MIGRATIONS_DIR);
        $migrations = array_values(array_diff($migrations, ['.', '..']));

        $runedMigrations = $this->getRunedMigrations();
        $this->cli->notice(json_encode($runedMigrations));
        if(!empty($migrations)) {
            foreach ($migrations as $migration) {
                $this->cli->notice("- run" . $migration);
                if(in_array($migration, $runedMigrations)) {
                    $this->cli->notice("-- skip" . $migration);
                    continue;
                }

                $sql = file_get_contents(static::MIGRATIONS_DIR . '/' . $migration);
                $query = db()->prepare($sql);

                if (!$query->execute()) {
                    throw new Exception("Failed to run migration: " . $query);
                }

                $this->createMigrationRecord($migration);
                $this->cli->success("- `$migration` migrated");
            }
        }

        $this->cli->notice("Migrations table was fetched");
    }

    protected function createMigrationRecord(string $migration): void
    {
        $query = db()->prepare("INSERT INTO migrations (name) VALUES (:name)");
        $query->bindValue(':name', $migration);
        $query->execute();
    }



    protected function getRunedMigrations(): array
    {
        $query = db()->prepare("SELECT * FROM migrations");
        $query->execute();

        return array_map(fn($item) => $item['name'],$query->fetchAll(PDO::FETCH_ASSOC));
    }
}