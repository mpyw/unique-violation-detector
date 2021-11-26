<?php

declare(strict_types=1);

namespace Mpyw\UniqueViolationDetector;

use PDOException;

class SQLServerDetector implements UniqueViolationDetector
{
    public function uniqueConstraintViolated(PDOException $e): bool
    {
        switch ($e->getCode()) {
            // The following drivers correctly return error codes.
            //
            // - pdo_sqlsrv (SQLSTATE[23000]: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]...)
            // - pdo_odbc (SQLSTATE[23000]: Integrity constraint violation: (...) [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]...)
            case '23000':
                return \in_array($e->errorInfo[1] ?? 0, [2627, 2601], true);

            // pdo_dblib returns SQLSTATE[HY000] and 20018 (General Error) on all constraint violations.
            // So we need to check messages.
            case 'HY000':
            default:
                return ($e->errorInfo[1] ?? 0) === 20018
                    && \preg_match(
                        '/^(?:Violation of (?:PRIMARY|UNIQUE) KEY constraint|Cannot insert duplicate key row)/',
                        $e->errorInfo[2] ?? ''
                    );
        }
    }
}
