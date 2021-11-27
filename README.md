# Unique Violation Detector [![Build Status](https://github.com/mpyw/unique-violation-detector/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/mpyw/unique-violation-detector/actions) [![Coverage Status](https://coveralls.io/repos/github/mpyw/unique-violation-detector/badge.svg?branch=master)](https://coveralls.io/github/mpyw/unique-violation-detector?branch=master)

Detect **primary/unique key or constraint violation** errors from `PDOException`.

## Installing

```
composer require mpyw/unique-violation-detector
```

## Requirements

| Package | Version |
|:---|:---|
| PHP | <code>^7.1 &#124;&#124; ^8.0</code> |

## Supported PDO Drivers

| Database | Driver | Auto-Discoverable |
|:---|:---|:---:|
| MySQL | `pdo_mysql` | ✅ |
| PostgreSQL | `pdo_pgsql` | ✅ |
| SQLite | `pdo_sqlite` | ✅ |
| SQLServer | `pdo_sqlsrv` | ✅ |
| SQLServer | `pdo_odbc` | |
| SQLServer | `pdo_dblib` | |
| Oracle | `pdo_oci` | ✅ |

## Usage

```php
use Mpyw\UniqueViolationDetector\MySQLDetector;

// Explicitly instantiate a detector
$violated = (new MySQLDetector())->uniqueConstraintViolated($exception);
```

```php
use Mpyw\UniqueViolationDetector\DetectorDiscoverer;

// Discover a detector from a PDO instance
// (Not available for pdo_odbc and pdo_dblib)
$violated = (new DetectorDiscoverer())
    ->discover($pdo)
    ->uniqueConstraintViolated($exception);
```
