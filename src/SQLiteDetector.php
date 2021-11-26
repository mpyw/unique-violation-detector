<?php

declare(strict_types=1);

namespace Mpyw\UniqueViolationDetector;

use PDOException;

class SQLiteDetector implements UniqueViolationDetector
{
    public function uniqueConstraintViolated(PDOException $e): bool
    {
        // SQLite returns SQLSTATE[23000] and 19 (SQLITE_CONSTRAINT) on all constraint violations.
        // So we need to check messages.
        return $e->getCode() === '23000'
            && ($e->errorInfo[1] ?? 0) === 19
            && \preg_match('/^UNIQUE constraint failed/', $e->errorInfo[2] ?? '');
    }
}
