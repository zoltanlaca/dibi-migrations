<?php
declare(strict_types=1);

namespace Zoltanlaca\DibiMigrations\Examples\Migrations;

use Dibi\Connection;
use Dibi\Exception;
use Zoltanlaca\DibiMigrations\AbstractMigration;

final class Version20220518084420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Table ' . $this->version();
    }

    /**
     * @throws Exception
     */
    public function up(Connection $connection): void
    {
        $this->consoleWrite('Creating table...');

        $connection->query("CREATE TABLE %n (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `data` INT NULL,
            `created_at` INT NULL,
            PRIMARY KEY (`id`)
        )
        COLLATE='utf8mb4_general_ci';", $this->version());

        $this->consoleWrite('Done.');
    }

    /**
     * @throws Exception
     */
    public function down(Connection $connection): void
    {
        $this->consoleWrite('Dropping table...');

        $connection->query('DROP TABLE %n', $this->version());

        $this->consoleWrite('Done.');
    }
}