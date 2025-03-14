<?php
declare(strict_types=1);

use Dibi\Connection;
use Zoltanlaca\DibiMigrations\Configuration;
use Zoltanlaca\DibiMigrations\Exceptions\ConnectionException;
use Zoltanlaca\DibiMigrations\Migrations;

include_once dirname(__DIR__) . '/vendor/autoload.php';

try {
    $connection = new Connection(
        config: [
            'driver' => 'mysqli',
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'dibi_migrations',
        ]
    );
} catch (\Dibi\Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
    exit;
}

$config = new Configuration(
    connection: $connection,
    directory: __DIR__ . '/Migrations',
    namespace: 'Zoltanlaca\DibiMigrations\Examples\Migrations',
    tableName: 'migrations'
);

try {
    $migrations = new Migrations(
        configuration: $config
    );
    $migrations->migrateUp();
} catch (ConnectionException $exception) {
    echo $exception->getMessage() . PHP_EOL;
    exit;
}