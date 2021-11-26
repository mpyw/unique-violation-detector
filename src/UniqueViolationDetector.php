<?php

declare(strict_types=1);

namespace Mpyw\UniqueViolationDetector;

use PDOException;

interface UniqueViolationDetector
{
    public function uniqueConstraintViolated(PDOException $e): bool;
}
