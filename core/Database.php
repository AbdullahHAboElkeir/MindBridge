<?php

/**
 * Database — Singleton PDO wrapper
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $dsn = 'mysql:host=' . DB_HOST
             . ';dbname='    . DB_NAME
             . ';charset='   . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die(json_encode([
                'error' => true,
                'message' => 'Database connection failed: ' . $e->getMessage()
            ]));
        }
    }

    /** Prevent cloning */
    private function __clone() {}

    /** Get singleton instance */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /** Get the PDO connection */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    /** Prepare & execute a query — returns PDOStatement */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /** Fetch a single row */
    public function fetchOne(string $sql, array $params = []): array|false
    {
        return $this->query($sql, $params)->fetch();
    }

    /** Fetch all rows */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /** Insert a row and return last insert ID */
    public function insert(string $sql, array $params = []): int
    {
        $this->query($sql, $params);
        return (int) $this->pdo->lastInsertId();
    }

    /** Execute update/delete — returns affected row count */
    public function execute(string $sql, array $params = []): int
    {
        return $this->query($sql, $params)->rowCount();
    }

    /** Begin transaction */
    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    /** Commit transaction */
    public function commit(): void
    {
        $this->pdo->commit();
    }

    /** Rollback transaction */
    public function rollback(): void
    {
        $this->pdo->rollBack();
    }
}
