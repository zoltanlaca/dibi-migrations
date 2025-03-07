<?php
declare(strict_types=1);

namespace Zoltanlaca\DibiMigrations;

use Dibi\Connection;
use Dibi\Exception;

abstract class AbstractMigration
{
    public function __construct(
        protected Migrations $migrations
    )
    {
    }

    public function consoleWrite(string $message): void
    {
        $this->migrations->consoleWrite($this->version(), $message);
    }

    public function version(): int
    {
        return $this->migrations->configuration->versionFromClassName(static::class);
    }

    abstract public function getDescription(): string;

    /**
     * @throws Exception
     */
    abstract public function up(Connection $connection): void;

    /**
     * @throws Exception
     */
    abstract public function down(Connection $connection): void;
}