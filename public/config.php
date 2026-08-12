<?php
session_start();

function db(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    // Railway MySQL plugin otomatis menyediakan variabel ini.
    // Kalau kamu pakai penyedia database lain, sesuaikan nama variabelnya.
    $host = getenv('MYSQLHOST') ?: '127.0.0.1';
    $port = getenv('MYSQLPORT') ?: '3306';
    $name = getenv('MYSQLDATABASE') ?: 'railway';
    $user = getenv('MYSQLUSER') ?: 'root';
    $pass = getenv('MYSQLPASSWORD') ?: '';

    $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";
    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $ex) {
        http_response_code(500);
        die('<div style="font-family:sans-serif;padding:24px;max-width:520px;margin:40px auto;background:#FCE9E7;border:1px solid #E0483F;border-radius:10px;color:#7a2622">'
            . '<b>Gagal konek ke database.</b><br><br>'
            . htmlspecialchars($ex->getMessage())
            . '<br><br>Cek: sudah tambah plugin MySQL di Railway dan sudah di-<i>link</i> ke service ini? '
            . 'Lihat PANDUAN-SETUP-PHP.md.</div>');
    }
    return $pdo;
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (!current_user()) {
        header('Location: login.php');
        exit;
    }
}

function e($str) {
    return htmlspecialchars((string)($str ?? ''), ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    header('Location: ' . $path);
    exit;
}
