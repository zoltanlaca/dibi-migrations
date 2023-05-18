<?php
declare(strict_types=1);

namespace Zoltanlaca\DibiMigrations;

use Dibi\Connection;
use InvalidArgumentException;
use function array_filter;
use function array_keys;
use function in_array;
use function is_dir;
use function scandir;
use function sprintf;
use function str_replace;
use function strtr;

/**
 * Class Configuration
 */
class Configuration
{
    private Connection $connection;
    private string $directory;
    private string $namespace;

    private string $tableName;

    /**
     * @param Connection $connection
     * @param string $directory
     * @param string $namespace
     * @param string $tableName
     */
    public function __construct(Connection $connection, string $directory, string $namespace, string $tableName)
    {
        $this->connection = $connection;

        if(!is_dir($directory)){
            throw new InvalidArgumentException(
                sprintf('Directory path [%s] is not reachable!', $directory)
            );
        }

        $this->directory = $directory;
        $this->namespace = $namespace;
        $this->tableName = $tableName;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return Connection
     */
    public function connection(): Connection
    {
        return $this->connection;
    }

    /**
     * @param int|null $version
     * @return array
     */
    public function versionsUp(?int $version): array
    {
        $migratedVersionList = $this->migratedVersions();

        return array_filter($this->versions(), function (string $className) use ($version, $migratedVersionList): bool {
            $ver = $this->versionFromClassName($className);
            return !in_array($ver, $migratedVersionList) && (is_null($version) || $ver <= $version);
        });
    }

    /**
     * @param int|null $version
     * @return array
     */
    public function versionsDown(?int $version): array
    {
        $migratedVersionList = $this->migratedVersions();

        return array_filter($this->versions(), function (string $className) use ($version, $migratedVersionList): bool {
            $ver = $this->versionFromClassName($className);
            return in_array($ver, $migratedVersionList) && (is_null($version) || $ver > $version);
        });
    }

    /**
     * @return array
     */
    private function migratedVersions(): array
    {
        return array_keys(
                $this->connection->select('version')
                    ->from($this->getTableName())
                    ->fetchAssoc('version')
        );
    }

    /**
     * @return array
     */
    public function versions(): array
    {
        $files = [];

        foreach (scandir($this->directory) as $fileName){

            if(in_array($fileName, ['.', '..'])){
                continue;
            }

            $version = $this->versionFromFileName($fileName);
            $files[$version] = $this->classFromFileName($fileName);
        }

        return $files;
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function classFromFileName(string $fileName): string
    {
        return sprintf('\\%s\\%s', $this->getNamespace(), str_replace('.php', '', $fileName));
    }

    /**
     * @param string $fileName
     * @return int
     */
    private function versionFromFileName(string $fileName): int
    {
        return (int)strtr($fileName, [
            'Version' => '',
            '.php' => '',
        ]);
    }

    /**
     * @param string $className
     * @return int
     */
    public function versionFromClassName(string $className): int
    {
        return (int)strtr($className, [
            $this->getNamespace() => '',
            '\\' . $this->getNamespace() => '',
            '\Version' => '',
        ]);
    }
}