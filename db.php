<?php

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


const DB_HOST = '127.0.0.1';
const DB_NAME = 'nextech_portal_24RP05014';
const DB_USER = 'root';
const DB_PASS = '';


function db()
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Surface a friendly message instead of a raw exception.
            exit('Database connection failed. Please check configuration.');
        }
    }

    return $pdo;
}


 //Require the user to be logged in; redirect otherwise.
 
function require_login()
{
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}


function clean_string($value)
{
    $value = trim((string)$value);
    return preg_replace('/[[:cntrl:]]/', '', $value) ?? '';
}

