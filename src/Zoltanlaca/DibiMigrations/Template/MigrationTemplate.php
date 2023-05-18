<?php
declare(strict_types=1);

namespace Zoltanlaca\DibiMigrations\Template;

use Dibi\Connection;
use Zoltanlaca\DibiMigrations\AbstractMigration;

/**
 * Class MigrationTemplate
 * @package Zoltanlaca\DibiMigrations\Template
 */
final class MigrationTemplate extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        //TODO: describe this migration
        return 'Description of version ' . $this->version();
    }

    /**
     * @param Connection $connection
     * @return void
     */
    public function up(Connection $connection): void
    {
        //TODO: write here migration up process
    }

    /**
     * @param Connection $connection
     * @return void
     */
    public function down(Connection $connection): void
    {
        //TODO: write here migration down process
    }
}