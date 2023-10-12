<?php

declare(strict_types=1);

namespace infrastructure\components;

use Exception;

final class Db
{
    private static \PDO $connection;

    /**
     * Не разрешен вызов извне, чтобы предотвратить создание нескольких экземпляров,
     * чтобы использовать синглтон, необходимо получить экземпляр из Db::getConnection()
     */
    private function __construct()
    {
    }

    /**
     * Предотвращает клонирование экземпляра класса (что создаст второй экземпляр)
     */
    private function __clone()
    {
    }

    /**
     * Предотвращает unserialized (что создаст второй экземпляр)
     * @return mixed
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    /**
     * @return \PDO
     */
    public static function getConnection(): \PDO
    {
        if (!isset(self::$connection)) {
            try {
                $dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE');
                self::$connection = new \PDO($dsn, getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
                self::$connection->exec('set names utf8');
                return self::$connection;
            } catch (\PDOException $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
                die();
            }
        }

        return self::$connection;
    }

}