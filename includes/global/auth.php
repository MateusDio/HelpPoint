<?php
session_start();

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
        header('Location: /HelpPoint/pages/login/index.php');
        exit();
    }
}

// Redireciona para o dashboard se já estiver logado (por role)
function redirectIfLogged() {
    if (isLoggedIn()) {
        if (isAdmin()) {
            header('Location: /HelpPoint/pages/admin/dashboard.php');
        } else {
            header('Location: /HelpPoint/pages/dashboard/index.php');
        }
        exit();
    }
}

// Redireciona se nao for admin
function redirectIfNotAdmin() {
    redirectIfNotLogged();
    if (!isAdmin()) {
        header('Location: /HelpPoint/pages/dashboard/index.php');
        exit();
    }
}
