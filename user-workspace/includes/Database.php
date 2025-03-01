<?php
class Database {
    private static $instance = null;
    private $connection = null;
    private $statement = null;
    private $inTransaction = false;

    /**
     * Constructor - Establishes database connection
     */
    public function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            $this->logError('Connection failed: ' . $e->getMessage());
            throw new Exception('Database connection failed');
        }
    }

    /**
     * Get database instance (Singleton pattern)
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Begin a transaction
     */
    public function beginTransaction(): bool {
        if (!$this->inTransaction) {
            $this->inTransaction = $this->connection->beginTransaction();
            return $this->inTransaction;
        }
        return false;
    }

    /**
     * Commit a transaction
     */
    public function commit(): bool {
        if ($this->inTransaction) {
            $this->inTransaction = false;
            return $this->connection->commit();
        }
        return false;
    }

    /**
     * Rollback a transaction
     */
    public function rollback(): bool {
        if ($this->inTransaction) {
            $this->inTransaction = false;
            return $this->connection->rollBack();
        }
        return false;
    }

    /**
     * Prepare and execute a query
     */
    public function query(string $sql, array $params = []): Database {
        try {
            $this->statement = $this->connection->prepare($sql);
            $this->statement->execute($params);
            return $this;
        } catch (PDOException $e) {
            $this->logError('Query failed: ' . $e->getMessage() . ' SQL: ' . $sql);
            throw new Exception('Database query failed');
        }
    }

    /**
     * Fetch a single row
     */
    public function fetch(int $fetchMode = PDO::FETCH_ASSOC) {
        return $this->statement->fetch($fetchMode);
    }

    /**
     * Fetch all rows
     */
    public function fetchAll(int $fetchMode = PDO::FETCH_ASSOC): array {
        return $this->statement->fetchAll($fetchMode);
    }

    /**
     * Get the number of affected rows
     */
    public function rowCount(): int {
        return $this->statement->rowCount();
    }

    /**
     * Get the last inserted ID
     */
    public function lastInsertId(): string {
        return $this->connection->lastInsertId();
    }

    /**
     * Insert a record
     */
    public function insert(string $table, array $data): int {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        
        $sql = "INSERT INTO {$table} (" . implode(',', $fields) . ") VALUES ({$placeholders})";
        
        $this->query($sql, $values);
        return (int) $this->lastInsertId();
    }

    /**
     * Update records
     */
    public function update(string $table, array $data, string $where, array $params = []): int {
        $fields = array_keys($data);
        $values = array_values($data);
        
        $set = implode('=?,', $fields) . '=?';
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
        
        $this->query($sql, array_merge($values, $params));
        return $this->rowCount();
    }

    /**
     * Delete records
     */
    public function delete(string $table, string $where, array $params = []): int {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $this->query($sql, $params);
        return $this->rowCount();
    }

    /**
     * Execute a raw SQL query
     */
    public function raw(string $sql, array $params = []) {
        return $this->query($sql, $params);
    }

    /**
     * Get a single value from the first row
     */
    public function getValue(string $sql, array $params = []) {
        $result = $this->query($sql, $params)->fetch(PDO::FETCH_NUM);
        return $result[0] ?? null;
    }

    /**
     * Get a single column as an array
     */
    public function getColumn(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Check if a record exists
     */
    public function exists(string $table, string $where, array $params = []): bool {
        $sql = "SELECT EXISTS(SELECT 1 FROM {$table} WHERE {$where}) as exist";
        return (bool) $this->getValue($sql, $params);
    }

    /**
     * Count records
     */
    public function count(string $table, string $where = '', array $params = []): int {
        $sql = "SELECT COUNT(*) FROM {$table}";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        return (int) $this->getValue($sql, $params);
    }

    /**
     * Create a backup of the database
     */
    public function backup(string $path): bool {
        try {
            $tables = $this->getColumn("SHOW TABLES");
            $output = '';
            
            foreach ($tables as $table) {
                $result = $this->query("SELECT * FROM {$table}")->fetchAll();
                
                $output .= "DROP TABLE IF EXISTS {$table};\n";
                $row2 = $this->getValue("SHOW CREATE TABLE {$table}");
                $output .= $row2 . ";\n\n";
                
                foreach ($result as $row) {
                    $output .= "INSERT INTO {$table} VALUES(";
                    $values = array_values($row);
                    $values = array_map(function($value) {
                        return is_null($value) ? "NULL" : "'" . addslashes($value) . "'";
                    }, $values);
                    $output .= implode(',', $values);
                    $output .= ");\n";
                }
                $output .= "\n";
            }
            
            return file_put_contents($path, $output) !== false;
        } catch (Exception $e) {
            $this->logError('Backup failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Restore database from backup
     */
    public function restore(string $path): bool {
        try {
            $sql = file_get_contents($path);
            $queries = array_filter(array_map('trim', explode(';', $sql)));
            
            $this->beginTransaction();
            
            foreach ($queries as $query) {
                $this->raw($query);
            }
            
            return $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            $this->logError('Restore failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Log database errors
     */
    private function logError(string $message): void {
        $logFile = LOG_PATH . '/database.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
        
        error_log($logMessage, 3, $logFile);
    }

    /**
     * Close the database connection
     */
    public function close(): void {
        $this->statement = null;
        $this->connection = null;
    }

    /**
     * Destructor - Closes the database connection
     */
    public function __destruct() {
        $this->close();
    }
}
