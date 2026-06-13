<?php

class Database
{
    private static ?PDO $mysqlInstance = null;
    private static ?SQLiteConnection $sqliteInstance = null;

    public static function connect(): PDO|SQLiteConnection
    {
        if (getenv('DB_DRIVER') === 'sqlite') {
            if (self::$sqliteInstance === null) {
                $path = getenv('DB_SQLITE_PATH') ?: __DIR__ . '/../data/elearning.db';
                self::$sqliteInstance = SQLiteConnection::connect($path);
            }
            return self::$sqliteInstance;
        }

        if (self::$mysqlInstance === null) {
            $config = require __DIR__ . '/../config/database.php';

            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['dbname'],
                $config['charset']
            );

            self::$mysqlInstance = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return self::$mysqlInstance;
    }
}
