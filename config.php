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

/** Toggle online ordering and Zalo order CTAs without changing product content. */
function prime_ordering_open(): bool {
    return false;
}

function prime_base_path(): string {
    static $base = null;
    if ($base === null) {
        $configured = rtrim(prime_env('BASE_URL', ''), '/');
        if ($configured !== '') return $base = $configured;
        $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
        $projectRoot = realpath(__DIR__);
        if ($docRoot && $projectRoot && str_starts_with($projectRoot, $docRoot)) {
            $base = str_replace('\\', '/', substr($projectRoot, strlen($docRoot)));
        } else {
            // Fallback for shared hosting/symlink deployments where the
            // filesystem root is outside DOCUMENT_ROOT.
            $requestDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
            $base = preg_replace('#/admin$#', '', $requestDir) ?: '';
        }
    }
    return $base;
}

function prime_asset_url(?string $url): string {
    $url = (string) $url;
    if ($url === '' || preg_match('#^https?://#i', $url)) return $url;
    // Older local records were stored with the XAMPP project prefix. Convert
    // them at render time to the actual server base path.
    if (str_starts_with($url, '/Prime-2/uploads/')) return prime_base_path().'/uploads/'.substr($url, strlen('/Prime-2/uploads/'));
    if (str_starts_with($url, 'Prime-2/uploads/')) return prime_base_path().'/uploads/'.substr($url, strlen('Prime-2/uploads/'));
    if (str_starts_with($url, '/uploads/')) return prime_base_path().$url;
    return $url;
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
