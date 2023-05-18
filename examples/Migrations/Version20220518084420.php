<?php
declare(strict_types=1);

namespace Zoltanlaca\DibiMigrations\Examples\Migrations;

use Dibi\Connection;
use Zoltanlaca\DibiMigrations\AbstractMigration;

/**
 * Class Version20220518084420
 * @package Zoltanlaca\DibiMigrations\Examples\Migrations
 */
final class Version20220518084420 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Table ' . $this->version();
    }

    /**
     * @param Connection $connection
     * @return void
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
     * @param Connection $connection
     * @return void
     */
    public function down(Connection $connection): void
    {
        $this->consoleWrite('Dropping table...');

        $connection->query('DROP TABLE %n', $this->version());

        $this->consoleWrite('Done.');
    }
}