<?php
declare(strict_types=1);
namespace Datum\Core;
final class Http
{
    public static function start(): void
    {
        ini_set('session.use_strict_mode', '1');
        session_name('datum_session');
        session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>getenv('SESSION_SECURE') !== 'false','httponly'=>true,'samesite'=>'Strict']);
        session_start();
        $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
        header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; connect-src 'self'; font-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'; object-src 'none'");
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        header('Cache-Control: no-store');
        if (getenv('SESSION_SECURE') !== 'false') { header('Strict-Transport-Security: max-age=31536000'); }
    }
    public static function json(mixed $data, int $status = 200): never
    {
        http_response_code($status); header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_THROW_ON_ERROR); exit;
    }
    public static function fail(string $message, int $status = 400): never { self::json(['error'=>$message], $status); }
    public static function input(): array
    {
        if ((int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 1048576) { self::fail('Request too large', 413); }
        $result = json_decode(file_get_contents('php://input'), true);
        if (!is_array($result)) { self::fail('Expected a JSON object'); }
        return $result;
    }
    public static function csrf(): void
    {
        if (!hash_equals($_SESSION['csrf'], $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')) { self::fail('Refresh the page and try again.', 403); }
    }
    public static function text(array $data, string $field, int $max, bool $required = true): string
    {
        $value = $data[$field] ?? '';
        if (!is_string($value)) { self::fail('Invalid field: ' . $field, 422); }
        $value = trim($value);
        if (($required && $value === '') || mb_strlen($value) > $max) { self::fail('Check field: ' . $field, 422); }
        return $value;
    }
    public static function limit(string $action, int $max = 20, int $seconds = 900): void
    {
        $key = getenv('APP_KEY');
        if (!$key || strlen($key) < 32) { self::fail('Application configuration incomplete', 503); }
        $now=time();
        $bucket=hash_hmac('sha256', $action . ':' . ($_SERVER['REMOTE_ADDR'] ?? 'cli') . ':' . intdiv($now,$seconds), $key);
        Db::run('INSERT INTO rate_limits(bucket,hits,expires_at) VALUES(?,1,?) ON DUPLICATE KEY UPDATE hits=hits+1', [$bucket,$now+$seconds]);
        if ((int)Db::one('SELECT hits FROM rate_limits WHERE bucket=?',[$bucket])['hits'] > $max) { header('Retry-After: '.$seconds); self::fail('Too many requests. Please try later.',429); }
    }
}
