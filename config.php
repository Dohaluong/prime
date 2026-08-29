<?php
/* DB credentials come from design/.env (DB_HOST, DB_NAME, DB_USER, DB_PASS) so
   real passwords never get committed to git. That file is gitignored — on each
   server (local XAMPP, production) it must be created there directly, not
   pushed. Falls back to the local XAMPP defaults when the keys are absent. */

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
    $host = prime_env('DB_HOST', 'localhost');
    $name = prime_env('DB_NAME', 'prime2026');
    $user = prime_env('DB_USER', 'root');
    $pass = prime_env('DB_PASS', '');
    try {
        $pdo = new PDO('mysql:host='.$host.';dbname='.$name.';charset=utf8mb4', $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    } catch (Throwable $e) { $pdo = null; }
    return $pdo;
}
