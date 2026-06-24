<?php
//NAO APAGAR ESSA LINHA, ELA É RESPONSAVEL POR VERIFICAR SE O USUARIO ESTA LOGADO
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfNotLogged();
if (isAdmin()) {
    header('Location: /HelpPoint/pages/admin/chamados.php');
    exit();
}
$pageTitle = 'Meus Chamados';
$currentPage = 'chamados';
require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/user/sidebar.php';
?>

