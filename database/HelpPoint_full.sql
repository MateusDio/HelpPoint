-- HelpPoint Full Database Dump
-- Import this file no phpMyAdmin após criar/selecionar o banco de dados.

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `password_reset`;
DROP TABLE IF EXISTS `email_verification`;
DROP TABLE IF EXISTS `chamado_anexos`;
DROP TABLE IF EXISTS `chamados`;
DROP TABLE IF EXISTS `equipamentos`;
DROP TABLE IF EXISTS `tipo`;
DROP TABLE IF EXISTS `categoria`;
DROP TABLE IF EXISTS `user`;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- Tabela: user
-- =============================================
CREATE TABLE IF NOT EXISTS `user` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `senha` VARCHAR(255) NOT NULL,
    `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    `funcao` VARCHAR(100) NULL,
    `documento` VARCHAR(50) NULL,
    `bio` TEXT NULL,
    `avatar` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela: categoria
-- =============================================
CREATE TABLE IF NOT EXISTS `categoria` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `descricao` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela: tipo
-- =============================================
CREATE TABLE IF NOT EXISTS `tipo` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela: equipamentos
-- =============================================
CREATE TABLE IF NOT EXISTS `equipamentos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `n_serie` VARCHAR(100) NULL,
    `patrimonio` VARCHAR(50) NULL UNIQUE,
    `status` VARCHAR(45) NOT NULL DEFAULT 'Disponivel',
    `descricao` TEXT NULL,
    `tipo_id` INT NOT NULL,
    CONSTRAINT `fk_equip_tipo` FOREIGN KEY (`tipo_id`) REFERENCES `tipo`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela: chamados
-- =============================================
CREATE TABLE IF NOT EXISTS `chamados` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `status` VARCHAR(45) NOT NULL DEFAULT 'Aberto',
    `data` DATE NOT NULL,
    `hora` TIME NOT NULL,
    `nivel` VARCHAR(45) NOT NULL,
    `obs` TEXT NULL,
    `local` VARCHAR(150) NULL,
    `user_id` INT NOT NULL,
    `categoria_id` INT NOT NULL,
    `equipamento_id` INT NULL,
    CONSTRAINT `fk_chamado_user` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`),
    CONSTRAINT `fk_chamado_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categoria`(`id`),
    CONSTRAINT `fk_chamado_equipamento` FOREIGN KEY (`equipamento_id`) REFERENCES `equipamentos`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela: chamado_anexos
-- =============================================
CREATE TABLE IF NOT EXISTS `chamado_anexos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `chamado_id` INT NOT NULL,
    `nome_arquivo` VARCHAR(255) NOT NULL,
    `nome_original` VARCHAR(255) NOT NULL,
    `tamanho` INT NOT NULL,
    `tipo_mime` VARCHAR(100) NOT NULL,
    `data_upload` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `user_id` INT NOT NULL,
    CONSTRAINT `fk_anexo_chamado` FOREIGN KEY (`chamado_id`) REFERENCES `chamados`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_anexo_user` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela: email_verification
-- =============================================
CREATE TABLE IF NOT EXISTS `email_verification` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL UNIQUE,
    `token` VARCHAR(255) NOT NULL UNIQUE,
    `email_temporario` VARCHAR(255) NOT NULL,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expira_em` TIMESTAMP NOT NULL,
    `verificado_em` TIMESTAMP NULL,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE,
    INDEX `idx_token` (`token`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_expira_em` (`expira_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela: password_reset
-- =============================================
CREATE TABLE IF NOT EXISTS `password_reset` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `token` VARCHAR(255) NOT NULL UNIQUE,
    `expira_em` TIMESTAMP NOT NULL,
    `usado_em` TIMESTAMP NULL,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE,
    INDEX `idx_token` (`token`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_expira_em` (`expira_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Seed: tipos de equipamento
-- =============================================
INSERT INTO `tipo` (`nome`) VALUES
('Notebook'),
('Desktop'),
('Monitor'),
('Impressora'),
('Scanner'),
('Projetor'),
('Roteador'),
('Switch'),
('Servidor'),
('Nobreak'),
('Telefone IP'),
('Tablet'),
('Webcam'),
('Teclado'),
('Mouse');

-- =============================================
-- Seed: categorias de chamados
-- =============================================
INSERT INTO `categoria` (`nome`, `descricao`) VALUES
('Hardware',        'Problemas em equipamentos fisicos (PC, monitor, impressora, etc.)'),
('Software',        'Erros, instalacoes ou atualizacoes de programas'),
('Rede e Internet', 'Falhas de conexao, Wi-Fi, cabeamento e roteadores'),
('Email',           'Problemas de envio, recebimento ou configuracao de email'),
('Acesso e Senha',  'Bloqueio de usuario, reset de senha e permissoes'),
('Sistema Interno', 'Erros ou solicitacoes no sistema da empresa'),
('Telefonia',       'Problemas em ramais, telefones IP e voz sobre IP'),
('Impressao',       'Toner, atolamento, fila de impressao e drivers'),
('Backup',          'Solicitacao ou restauracao de copias de seguranca'),
('Seguranca',       'Antivirus, suspeita de invasao, phishing e malware'),
('Solicitacao',     'Pedidos gerais de TI (novo equipamento, software, acesso)'),
('Manutencao',      'Manutencao preventiva ou corretiva agendada');
