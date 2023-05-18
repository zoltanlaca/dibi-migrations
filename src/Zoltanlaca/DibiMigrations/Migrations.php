<?php
declare(strict_types=1);

namespace Zoltanlaca\DibiMigrations;

use DateTime;
use Dibi\Exception;
use Zoltanlaca\DibiMigrations\Exceptions\CannotCreateMigrationException;
use Zoltanlaca\DibiMigrations\Exceptions\ConnectionException;
use function file_get_contents;
use function file_put_contents;
use function is_file;
use function is_null;
use function sprintf;
use function strtr;
use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

/**
 * Class Migrations
 * @package Zoltanlaca\DibiMigrations
 */
final class Migrations
{
    public Configuration $configuration;

    /**
     * @param Configuration $configuration
     * @throws ConnectionException
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->boot();
    }

    /**
     * @return void
     * @throws ConnectionException
     */
    private function boot(): void
    {
        try {
            $migrationTable = $this->configuration->connection()
                ->query('SHOW TABLES LIKE %s', $this->configuration->getTableName())
                ->fetch();

            if (is_null($migrationTable)) {
                $this->configuration->connection()
                    ->query("CREATE TABLE %n (
	                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	                `version` BIGINT NOT NULL,
	                `created_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP()),
	                PRIMARY KEY (`id`),
	                INDEX `version` (`version`)
                )
                COLLATE='utf8mb4_general_ci';", $this->configuration->getTableName()
                    );
            }
        } catch (Exception $exception){
            throw new ConnectionException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param int $version
     * @return void
     * @throws ConnectionException
     */
    private function afterUp(int $version): void
    {
        try {
            $this->configuration->connection()
                ->insert($this->configuration->getTableName(), [
                    'version' => $version,
                ])->execute();
        } catch (Exception $exception){
            throw new ConnectionException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param int $version
     * @return void
     * @throws ConnectionException
     */
    private function afterDown(int $version): void
    {
        try {
            $this->configuration->connection()
                ->delete($this->configuration->getTableName())
                ->where('version = %i', $version)
                ->execute();
        } catch (Exception $exception){
            throw new ConnectionException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param int|null $version
     * @return void
     * @throws ConnectionException
     */
    public function migrateUp(?int $version = null): void
    {
        /** @var string $className */
        foreach ($this->configuration->versionsUp($version) as $migrationVersion => $className) {

            /** @var AbstractMigration $class */
            $class = new $className($this);
            $this->consoleWrite($migrationVersion, $class->getDescription());

            $this->consoleWrite($migrationVersion, 'Executing...');

            try {
                $class->up($this->configuration->connection());
            } catch (Exception $exception){
                throw new ConnectionException($exception->getMessage(), $exception->getCode(), $exception);
            }

            $this->afterUp($migrationVersion);

            $this->consoleWrite($migrationVersion, 'Done');
        }
    }

    /**
     * @param int $version
     * @param string $message
     * @return void
     */
    public function consoleWrite(int $version, string $message): void
    {
        echo sprintf('[%s] v.%d, %s',
            (new DateTime())->format('Y-m-d H:i:s'),
            $version,
            $message
        ) . PHP_EOL;
    }

    /**
     * @param int|null $version
     * @return void
     * @throws ConnectionException
     */
    public function migrateDown(?int $version = null): void
    {
        /** @var string $className */
        foreach ($this->configuration->versionsDown($version) as $migrationVersion => $className) {
            /** @var AbstractMigration $class */
            $class = new $className($this);
            $this->consoleWrite($migrationVersion, $class->getDescription());

            $this->consoleWrite($migrationVersion, 'Executing...');

            try {
                $class->down($this->configuration->connection());
            } catch (Exception $exception){
                throw new ConnectionException($exception->getMessage(), $exception->getCode(), $exception);
            }

            $this->afterDown($migrationVersion);

            $this->consoleWrite($migrationVersion, 'Done');
        }
    }

    /**
     * @return string
     * @throws CannotCreateMigrationException
     */
    public function create(): string
    {
        $template = file_get_contents(__DIR__ . '/Template/MigrationTemplate.php');

        $className = sprintf('Version%s', (new DateTime())->format('YmdHis'));


        $template = strtr($template, [
            'MigrationTemplate' => $className,
            __NAMESPACE__ . '\Template' => $this->configuration->getNamespace(),
        ]);

        $fullPath = $this->configuration->getDirectory() . DIRECTORY_SEPARATOR . $className . '.php';

        file_put_contents($fullPath, $template);

        if (!is_file($fullPath)) {
            throw new CannotCreateMigrationException();
        }

        return $fullPath;
    }
}