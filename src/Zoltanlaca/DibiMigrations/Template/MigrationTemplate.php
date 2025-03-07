<?php
declare(strict_types=1);

namespace Zoltanlaca\DibiMigrations\Template;

use Dibi\Connection;
use Zoltanlaca\DibiMigrations\AbstractMigration;
final class MigrationTemplate extends AbstractMigration
{
    public function getDescription(): string
    {
        //TODO: describe this migration
        return 'Description of version ' . $this->version();
    }

    public function up(Connection $connection): void
    {
        //TODO: write here migration up process
    }

    public function down(Connection $connection): void
    {
        //TODO: write here migration down process
    }
}