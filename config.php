<?php
/* Change these values for the local XAMPP MySQL instance. The pages still run
   with the sample catalogue when the database has not been imported yet. */
const DB_HOST = 'localhost';
const DB_NAME = 'prime2026';
const DB_USER = 'root';
const DB_PASS = '';

function prime_env(string $key, string $default = ''): string {
    static $vars = null;
    if ($vars === null) {
        $vars = [];
        $path = __DIR__.'/design/.env';
        if (is_file($path)) {
            foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) continue;
                [$k, $v] = explode('=', $line, 2);
                $vars[trim($k)] = trim($v);
            }
        }
    }
    return $vars[$key] ?? $default;
}

function db(): ?PDO {
    static $pdo = false;
    if ($pdo !== false) return $pdo;
    try {
        $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    } catch (Throwable $e) { $pdo = null; }
    return $pdo;
}
