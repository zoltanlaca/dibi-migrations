<?php
declare(strict_types=1);

namespace Zoltanlaca\DibiMigrations;

use Dibi\Connection;
use Dibi\Exception;

/**
 * Class AbstractMigration
 * @package ZoltanLaca\DibiMigrations
 */
abstract class AbstractMigration
{
    protected Migrations $migrations;

    /**
     * @param Migrations $migrations
     */
    public function __construct(Migrations $migrations)
    {
        $this->migrations = $migrations;
    }

    /**
     * @param string $message
     * @return void
     */
    public function consoleWrite(string $message): void
    {
        $this->migrations->consoleWrite($this->version(), $message);
    }

    /**
     * @return int
     */
    public function version(): int
    {
        return $this->migrations->configuration->versionFromClassName(static::class);
    }

    /**
     * @return string
     */
    abstract public function getDescription(): string;

    /**
     * @param Connection $connection
     * @return void
     * @throws Exception
     */
    abstract public function up(Connection $connection): void;

    /**
     * @param Connection $connection
     * @return void
     * @throws Exception
     */
    abstract public function down(Connection $connection): void;
}