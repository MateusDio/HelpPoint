<?php
session_start();

// URL base do projeto (auto-detecta XAMPP local vs hospedagem)
if (!defined('BASE_URL')) {
    $__host = $_SERVER['HTTP_HOST'] ?? '';
    if (strpos($__host, 'localhost') !== false || strpos($__host, '127.0.0.1') !== false) {
        define('BASE_URL', '/HelpPoint');
    } else {
        define('BASE_URL', '');
    }
}

// Verifica se o usuário está logado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Verifica se o usuário é admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Redireciona para o login se não estiver logado
function redirectIfNotLogged() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/pages/login/index.php');
        exit();
    }
}

// Redireciona para o dashboard se já estiver logado (por role)
function redirectIfLogged() {
    if (isLoggedIn()) {
        if (isAdmin()) {
            header('Location: ' . BASE_URL . '/pages/admin/dashboard.php');
        } else {
            header('Location: ' . BASE_URL . '/pages/dashboard/index.php');
        }
        exit();
    }
}

// Redireciona se nao for admin
function redirectIfNotAdmin() {
    redirectIfNotLogged();
    if (!isAdmin()) {
        header('Location: ' . BASE_URL . '/pages/dashboard/index.php');
        exit();
    }
}
