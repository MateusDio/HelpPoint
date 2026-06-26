<?php
// ============================================
// CONFIGURAÇÃO DO BANCO DE DADOS
// ============================================
// TODO: Definir nome do banco, usuário e senha
// com a equipe antes de rodar o projeto.
// ============================================

$host = 'localhost';
$dbname = 'equipamento';       // TODO: definir com a equipe
$username = 'root';           // padrão do XAMPP
$password = '';                   // padrão do XAMPP (sem senha)

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao conectar com o banco de dados: " . $e->getMessage());
}
