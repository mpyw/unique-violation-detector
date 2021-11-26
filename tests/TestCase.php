<?php

declare(strict_types=1);

namespace Mpyw\UniqueViolationDetector\Tests;

use Mpyw\UniqueViolationDetector\DetectorDiscoverer;
use Mpyw\UniqueViolationDetector\DiscoveryFailedException;
use Mpyw\UniqueViolationDetector\SQLServerDetector;
use Mpyw\UniqueViolationDetector\UniqueViolationDetector;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var string
     */
    protected $driver;

    /**
     * @var PDO
     */
    protected $pdo;

    public function setUp(): void
    {
        $this->driver = getenv('DB') ?: 'sqlite';

        try {
            $this->pdo = self::initPdo($this->driver);

            if ($this->driver === 'sqlite') {
                $this->pdo->exec('PRAGMA foreign_keys=true;');
            } else {
                $this->pdo->exec('DROP TABLE IF EXISTS posts');
                $this->pdo->exec('DROP TABLE IF EXISTS users');
            }
        } catch (PDOException $e) {
            if ($e->getMessage() === 'could not find driver') {
                $this->markTestSkipped('PDO driver is not available in the environment');
            }

            throw $e;
        }
    }

    protected function detector(): UniqueViolationDetector
    {
        try {
            return (new DetectorDiscoverer())->discover($this->pdo);
        } catch (DiscoveryFailedException $e) {
            switch ($this->driver) {
                case 'odbc:sqlsrv':
                case 'dblib:sqlsrv':
                    return new SQLServerDetector();
                default:
                    $this->fail('invalid driver');
            }
        }
    }

    private static function initPdo(string $driver): PDO
    {
        switch ($driver) {
            case 'mysql':
                return new PDO('mysql:host=127.0.0.1;port=3306;dbname=testing', 'testing', 'testing', [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
            case 'pgsql':
                return new PDO('pgsql:host=127.0.0.1;port=5432;dbname=testing', 'testing', 'testing', [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
            case 'sqlite':
                return new PDO('sqlite::memory:', null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
            case 'sqlsrv':
                return new PDO('sqlsrv:Server=127.0.0.1,1433;Database=testing', 'sa', 'Password!', [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
            case 'odbc:sqlsrv':
                return new PDO('odbc:Driver={ODBC Driver 17 for SQL Server};Server=127.0.0.1,1433;Database=testing;UID=sa;PWD=Password!', 'sa', 'Password!', [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
            case 'dblib:sqlsrv':
                return new PDO('dblib:host=127.0.0.1:1433;dbname=testing', 'sa', 'Password!', [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
            default:
                throw new RuntimeException('Unsupported Driver.');
        }
    }
}
