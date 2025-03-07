# dibi-migrations
Dibi Database Migrations Library

## Installation
````bash
composer require zoltanlaca/dibi-migrations
````

## Basic configuration

````php
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
    echo $exception->getMessage(). PHP_EOL;
    exit;
}

$config = New Configuration(
   connection: $connection,
   directory: __DIR__ . '/Migrations',
   namespace: 'Zoltanlaca\DibiMigrations\Examples\Migrations',
   tableName: 'migrations'
);

try {
    $migrations = new Migrations($config);
    $migrations->migrateUp();
} catch (ConnectionException $exception) {
    echo $exception->getMessage(). PHP_EOL;
    exit;
}
````

## Migrate to newest version
````php
$migrations->migrateUp();
````

## Migrate to defined version only
````php
$migrations->migrateUp(20230518085154);
````

## Revert migration to first version
````php
$migrations->migrateDown();
````

## Revert migration to defined version
````php
$migrations->migrateUp(20230518085154);
````