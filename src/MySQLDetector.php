<?php

declare(strict_types=1);

namespace Mpyw\UniqueViolationDetector;

use PDOException;

class MySQLDetector implements UniqueViolationDetector
{
    public function uniqueConstraintViolated(PDOException $e): bool
    {
        // SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry
        return $e->getCode() === '23000' && ($e->errorInfo[1] ?? 0) === 1062;
    }
}
