<?php

declare(strict_types=1);

namespace Mpyw\UniqueViolationDetector\Tests;

use PDOException;

class DetectorTest extends TestCase
{
    public function testDuplicatePrimaryKeyViolated(): void
    {
        $this->pdo->exec('CREATE TABLE users(id INTEGER PRIMARY KEY)');
        $this->pdo->exec('INSERT INTO users(id) VALUES(1)');

        try {
            $this->pdo->exec('INSERT INTO users(id) VALUES(1)');
            $this->fail();
        } catch (PDOException $e) {
            var_dump($e->errorInfo);
            $this->assertTrue($this->detector()->uniqueConstraintViolated($e));
        }
    }

    public function testDuplicateUniqueKeyViolated(): void
    {
        $this->pdo->exec('CREATE TABLE users(id INTEGER PRIMARY KEY, name VARCHAR(255) NOT NULL UNIQUE)');
        $this->pdo->exec("INSERT INTO users(id, name) VALUES(1, 'example')");

        try {
            $this->pdo->exec("INSERT INTO users(id, name) VALUES(2, 'example')");
            $this->fail();
        } catch (PDOException $e) {
            var_dump($e->errorInfo);
            $this->assertTrue($this->detector()->uniqueConstraintViolated($e));
        }
    }

    public function testNonNullConstraintNotViolated(): void
    {
        $this->pdo->exec('CREATE TABLE users(id INTEGER PRIMARY KEY, name VARCHAR(255) NOT NULL)');
        $this->pdo->exec("INSERT INTO users(id, name) VALUES(1, 'example')");

        try {
            $this->pdo->exec('INSERT INTO users(id, name) VALUES(2, NULL)');
            $this->fail();
        } catch (PDOException $e) {
            var_dump($e->errorInfo);
            $this->assertFalse($this->detector()->uniqueConstraintViolated($e));
        }
    }

    public function testForeignKeyConstraintNotViolated(): void
    {
        $this->pdo->exec('CREATE TABLE users(id INTEGER PRIMARY KEY)');
        $this->pdo->exec('CREATE TABLE posts(id INTEGER PRIMARY KEY, user_id INTEGER NOT NULL, FOREIGN KEY (user_id) REFERENCES users(id))');
        $this->pdo->exec('INSERT INTO users(id) VALUES(1)');

        try {
            $this->pdo->exec('INSERT INTO posts(id, user_id) VALUES(1, 99999)');
            $this->fail();
        } catch (PDOException $e) {
            var_dump($e->errorInfo);
            $this->assertFalse($this->detector()->uniqueConstraintViolated($e));
        }
    }

    public function testCheckConstraintNotViolated(): void
    {
        $this->pdo->exec('CREATE TABLE users(id INTEGER PRIMARY KEY, age INTEGER NOT NULL CHECK(age > 0))');
        $this->pdo->exec('INSERT INTO users(id, age) VALUES(1, 20)');

        try {
            $this->pdo->exec('INSERT INTO users(id, age) VALUES(2, -20)');
            $this->fail();
        } catch (PDOException $e) {
            var_dump($e->errorInfo);
            $this->assertFalse($this->detector()->uniqueConstraintViolated($e));
        }
    }
}
