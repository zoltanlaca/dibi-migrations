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

readonly class Configuration
{
    public function __construct(
        private Connection $connection,
        private string     $directory,
        private string     $namespace,
        private string     $tableName
    )
    {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException(
                message: sprintf(
                    format: 'Directory path [%s] is not reachable!',
                    values: $directory
                )
            );
        }
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function connection(): Connection
    {
        return $this->connection;
    }

    /**
     * @param int|null $version
     * @return array<int, string>
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
     * @return int[]
     */
    private function migratedVersions(): array
    {
        return array_keys(
            $this->connection->select('version')
                ->from($this->getTableName())
                ->fetchAssoc('version')
        );
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return array<int, string>
     */
    public function versions(): array
    {
        $files = [];
        $fileList = scandir($this->directory);
        if ($fileList === false) {
            throw new InvalidArgumentException(
                message: sprintf(
                    format: 'Directory path [%s] is not reachable!',
                    values: $this->directory
                )
            );
        }

        foreach ($fileList as $fileName) {

            if (in_array($fileName, ['.', '..'])) {
                continue;
            }

            $version = $this->versionFromFileName($fileName);
            $files[$version] = $this->classFromFileName($fileName);
        }

        return $files;
    }

    private function versionFromFileName(string $fileName): int
    {
        return (int)strtr($fileName, [
            'Version' => '',
            '.php' => '',
        ]);
    }

    private function classFromFileName(string $fileName): string
    {
        return sprintf('\\%s\\%s', $this->getNamespace(), str_replace('.php', '', $fileName));
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function versionFromClassName(string $className): int
    {
        return (int)strtr($className, [
            $this->getNamespace() => '',
            '\\' . $this->getNamespace() => '',
            '\Version' => '',
        ]);
    }

    /**
     * @param int|null $version
     * @return array<int, string>
     */
    public function versionsDown(?int $version): array
    {
        $migratedVersionList = $this->migratedVersions();

        return array_filter($this->versions(), function (string $className) use ($version, $migratedVersionList): bool {
            $ver = $this->versionFromClassName($className);
            return in_array($ver, $migratedVersionList) && (is_null($version) || $ver > $version);
        });
    }
}