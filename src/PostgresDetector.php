<?php

declare(strict_types=1);

namespace Mpyw\UniqueViolationDetector;

use PDOException;

class PostgresDetector implements UniqueViolationDetector
{
    public function uniqueConstraintViolated(PDOException $e): bool
    {
        // SQLSTATE[23505]: Unique violation: 7 ERROR:  duplicate key value violates unique constraint
        return $e->getCode() === '23505';
    }
}
