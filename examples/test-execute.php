<?php
declare(strict_types=1);

use Dibi\Connection;
use Zoltanlaca\DibiMigrations\Configuration;
use Zoltanlaca\DibiMigrations\Exceptions\ConnectionException;
use Zoltanlaca\DibiMigrations\Migrations;

include_once dirname(__DIR__) . '/vendor/autoload.php';

try {
    $connection = new Connection([
        'driver' => 'mysqli',
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'dibi_migrations',
    ]);
} catch (\Dibi\Exception $exception) {
    echo $exception->getMessage(). PHP_EOL;
    exit;
}

$config = New Configuration(
    $connection,
   __DIR__ . '/Migrations',
   'Zoltanlaca\DibiMigrations\Examples\Migrations',
   'migrations'
);

try {
    $migrations = new Migrations($config);
    $migrations->migrateUp();
} catch (ConnectionException $exception) {
    echo $exception->getMessage(). PHP_EOL;
    exit;
}