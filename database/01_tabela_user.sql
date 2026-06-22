-- ============================================
-- HELPPOINT - Tabela USER
-- ============================================
-- Responsavel: Arthur
-- Roles: 'user' (cliente) | 'admin'
-- ============================================

CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    fun VARCHAR(100),
    doc VARCHAR(50),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
