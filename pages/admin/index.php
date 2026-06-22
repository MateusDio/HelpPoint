<?php
// Redireciona para o dashboard admin
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfNotAdmin();
header('Location: dashboard.php');
exit();
