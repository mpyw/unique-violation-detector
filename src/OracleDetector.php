<?php

declare(strict_types=1);

namespace Mpyw\UniqueViolationDetector;

use PDOException;

class OracleDetector implements UniqueViolationDetector
{
    public function uniqueConstraintViolated(PDOException $e): bool
    {
        // SQLSTATE[HY000]: OCIStmtExecute: ORA-00001: unique constraint (...) violated
        return $e->getCode() === 'HY000' && ($e->errorInfo[1] ?? 0) === 1;
    }
}
