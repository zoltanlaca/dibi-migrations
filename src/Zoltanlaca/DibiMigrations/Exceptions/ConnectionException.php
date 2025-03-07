<?php
declare(strict_types=1);

namespace Zoltanlaca\DibiMigrations\Exceptions;
use Dibi\Exception;

class ConnectionException extends DibiMigrationException
{
    public static function fromException(Exception $e): ConnectionException
    {
        return new ConnectionException(
            message: $e->getMessage(),
            code: $e->getCode(),
            previous: $e
        );
    }
}