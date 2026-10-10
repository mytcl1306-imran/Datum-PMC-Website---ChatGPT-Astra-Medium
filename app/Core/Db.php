<?php
declare(strict_types=1);
namespace Datum\Core;
use PDO;
final class Db
{
    private static ?PDO $connection = null;
    public static function connection(): PDO
    {
        return self::$connection ??= new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: '3306') . ';dbname=' . getenv('DB_DATABASE') . ';charset=utf8mb4', getenv('DB_USERNAME'), getenv('DB_PASSWORD'), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]);
    }
    public static function run(string $sql, array $params = []): \PDOStatement
    {
        $statement = self::connection()->prepare($sql);
        $statement->execute($params);
        return $statement;
    }
    public static function all(string $sql, array $params = []): array { return self::run($sql, $params)->fetchAll(); }
    public static function one(string $sql, array $params = []): ?array { return self::run($sql, $params)->fetch() ?: null; }
    public static function audit(?int $user, string $action, string $resource, ?int $id = null): void
    {
        self::run('INSERT INTO audit_logs(user_id,action,resource,record_id) VALUES(?,?,?,?)', [$user,$action,$resource,$id]);
    }
}
