<?php

declare(strict_types=1);

namespace Mpyw\UniqueViolationDetector;

use PDO;

class DetectorDiscoverer
{
    /**
     * @throws DiscoveryFailedException
     */
    public function discover(PDO $pdo): UniqueViolationDetector
    {
        switch ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME)) {
            case 'mysql':
                return new MySQLDetector();
            case 'pgsql':
                return new PostgresDetector();
            case 'sqlite':
                return new SQLiteDetector();
            case 'sqlsrv':
                return new SQLServerDetector();
            case 'oci':
                return new OracleDetector();
            default:
                throw new DiscoveryFailedException('Failed to automatically discover a detector.');
        }
    }
}
