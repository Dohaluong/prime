<?php
ob_start();
require_once __DIR__.'/../config.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
function require_admin(): void { if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; } }
function current_admin(): array { return $_SESSION['admin_user'] ?? []; }
