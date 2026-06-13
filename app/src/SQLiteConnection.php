<?php

class SQLiteConnection
{
    private static ?self $instance = null;
    private string $path;
    private string $lastInsertId = '0';

    private function __construct(string $path)
    {
        $this->path = $path;
    }

    public static function connect(string $path): self
    {
        if (self::$instance === null) {
            self::$instance = new self($path);
            self::$instance->execPragma('PRAGMA journal_mode=WAL');
            self::$instance->execPragma('PRAGMA foreign_keys=ON');
        }
        return self::$instance;
    }

    public function prepare(string $sql): SQLiteStatement
    {
        return new SQLiteStatement($this->path, $sql, $this);
    }

    public function query(string $sql): SQLiteStatement
    {
        $stmt = new SQLiteStatement($this->path, $sql, $this);
        $stmt->execute();
        return $stmt;
    }

    public function exec(string $sql): int
    {
        $this->execSql($sql);
        return 0;
    }

    public function lastInsertId(): string
    {
        return $this->lastInsertId;
    }

    public function setLastInsertId(string $id): void
    {
        $this->lastInsertId = $id;
    }

    private function execPragma(string $sql): void
    {
        $db = escapeshellarg($this->path);
        $qs = escapeshellarg($sql);
        exec("sqlite3 {$db} {$qs} 2>&1");
    }

    private function execSql(string $sql): void
    {
        $db = escapeshellarg($this->path);
        $tmpf = tempnam(sys_get_temp_dir(), 'sql_');
        file_put_contents($tmpf, SQLiteConnection::translateSql($sql) . ";\n");
        exec("sqlite3 {$db} < {$tmpf} 2>&1", $output, $code);
        unlink($tmpf);
        if ($code !== 0) {
            throw new RuntimeException('Erreur SQLite : ' . implode("\n", array_slice($output, 0, 3)));
        }
    }

    public static function translateSql(string $sql): string
    {
        $sql = preg_replace('/\bINSERT\s+IGNORE\b/i', 'INSERT OR IGNORE', $sql);
        $sql = preg_replace('/\bNOW\(\)/i', "datetime('now')", $sql);

        if (preg_match('/INSERT\s+INTO\s+(\w+)\s*\(([^)]+)\)\s*VALUES.*\bON\s+DUPLICATE\s+KEY\s+UPDATE\s+(.+)$/is', $sql, $m)) {
            $table = strtolower($m[1]);
            $updates = trim($m[3]);
            $updates = preg_replace('/VALUES\s*\(\s*(\w+)\s*\)/i', 'excluded.$1', $updates);
            $updates = preg_replace('/\bNOW\(\)/i', "datetime('now')", $updates);
            $conflictMap = [
                'reviews' => 'user_id, course_id',
                'lesson_progress' => 'user_id, lesson_id',
                'enrollments' => 'user_id, course_id',
            ];
            $conflict = $conflictMap[$table] ?? null;
            if ($conflict) {
                $sql = preg_replace(
                    '/\bON\s+DUPLICATE\s+KEY\s+UPDATE\s+.+$/is',
                    "ON CONFLICT({$conflict}) DO UPDATE SET {$updates}",
                    $sql
                );
            }
        }

        return $sql;
    }
}

class SQLiteStatement
{
    private string $dbPath;
    private string $sql;
    private SQLiteConnection $conn;
    private ?array $results = null;
    private int $position = 0;

    public function __construct(string $dbPath, string $sql, SQLiteConnection $conn)
    {
        $this->dbPath = $dbPath;
        $this->sql = $sql;
        $this->conn = $conn;
    }

    public function execute(?array $params = null): bool
    {
        $sql = $this->interpolate($params ?? []);
        $sql = SQLiteConnection::translateSql($sql);
        $isSelect = preg_match('/^\s*(?:SELECT|PRAGMA)/i', $sql);
        $isInsert = preg_match('/^\s*INSERT\s+/i', $sql);

        $db = escapeshellarg($this->dbPath);
        $tmpf = tempnam(sys_get_temp_dir(), 'sql_');

        if ($isSelect) {
            file_put_contents($tmpf, $sql . ";\n");
            $output = shell_exec("sqlite3 -json {$db} < {$tmpf} 2>&1") ?: '';
            unlink($tmpf);
            $trimmed = trim($output);
            $this->results = $trimmed === '' ? [] : (json_decode($trimmed, true) ?: []);
            $this->position = 0;
        } else {
            if ($isInsert) {
                file_put_contents($tmpf, $sql . ";\nSELECT last_insert_rowid();\n");
                exec("sqlite3 {$db} < {$tmpf} 2>&1", $output, $code);
                if ($code === 0 && !empty($output)) {
                    $this->conn->setLastInsertId(trim(end($output)));
                }
            } else {
                file_put_contents($tmpf, $sql . ";\n");
                exec("sqlite3 {$db} < {$tmpf} 2>&1", $output, $code);
            }
            unlink($tmpf);
            if ($code !== 0) {
                throw new RuntimeException('Erreur SQLite : ' . implode("\n", array_slice($output, 0, 3)));
            }
            $this->results = null;
            $this->position = 0;
        }

        return true;
    }

    public function fetch(int $mode = PDO::FETCH_ASSOC): mixed
    {
        if ($this->results === null || $this->position >= count($this->results)) {
            return false;
        }
        $row = $this->results[$this->position];
        $this->position++;

        if ($mode === PDO::FETCH_COLUMN) {
            return is_array($row) ? reset($row) : $row;
        }
        return $row;
    }

    public function fetchAll(int $mode = PDO::FETCH_ASSOC): array
    {
        if ($this->results === null) {
            return [];
        }
        $this->position = count($this->results);
        return $this->results;
    }

    public function fetchColumn(int $column = 0): mixed
    {
        $row = $this->fetch();
        if ($row === false) {
            return false;
        }
        $values = array_values($row);
        return $values[$column] ?? false;
    }

    public function rowCount(): int
    {
        return $this->results ? count($this->results) : 0;
    }

    public function closeCursor(): bool
    {
        $this->results = null;
        $this->position = 0;
        return true;
    }

    private function interpolate(array $params): string
    {
        if (empty($params)) {
            return $this->sql;
        }
        $parts = explode('?', $this->sql);
        $sql = '';
        foreach ($parts as $i => $part) {
            $sql .= $part;
            if (array_key_exists($i, $params)) {
                $v = $params[$i];
                if ($v === null) {
                    $sql .= 'NULL';
                } elseif (is_int($v) || is_float($v)) {
                    $sql .= $v;
                } else {
                    $sql .= "'" . str_replace("'", "''", $v) . "'";
                }
            }
        }
        return $sql;
    }
}
