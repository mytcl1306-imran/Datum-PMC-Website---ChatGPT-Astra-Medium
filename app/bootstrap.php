<?php
declare(strict_types=1);
spl_autoload_register(static function (string $class): void {
    $prefix = 'Datum\\';
    if (str_starts_with($class, $prefix)) {
        $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) { require $file; }
    }
});
if (is_file(dirname(__DIR__) . '/.env')) {
    foreach (file(dirname(__DIR__) . '/.env', FILE_IGNORE_NEW_LINES) as $line) {
        if ($line === '' || str_starts_with(trim($line), '#') || !str_contains($line, '=')) { continue; }
        [$k, $v] = explode('=', $line, 2);
        if (getenv(trim($k)) === false) { putenv(trim($k) . '=' . trim($v)); }
    }
}
date_default_timezone_set('UTC');
function e(mixed $value): string { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function paragraphs(string $value): string {
    return implode('', array_map(static fn($p) => '<p>' . nl2br(e($p)) . '</p>', preg_split('/\n\s*\n/', $value)));
}
