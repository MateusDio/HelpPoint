
-- --------------------------------------------
-- Tabela: user
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    funcao VARCHAR(100) NULL,
    documento VARCHAR(50) NULL,
    bio TEXT NULL,
    avatar VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------
-- Tabela: categoria
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS categoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT NULL
);

-- --------------------------------------------
-- Tabela: tipo
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS tipo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

-- --------------------------------------------
-- Tabela: equipamentos
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS equipamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    n_serie VARCHAR(100) NULL,
    patrimonio VARCHAR(50) NULL UNIQUE,
    status VARCHAR(45) NOT NULL DEFAULT 'Disponivel',
    descricao TEXT NULL,
    tipo_id INT NOT NULL,
    CONSTRAINT fk_equip_tipo FOREIGN KEY (tipo_id) REFERENCES tipo(id)
);

-- --------------------------------------------
-- Tabela: chamados
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS chamados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status VARCHAR(45) NOT NULL DEFAULT 'Aberto',
    data DATE NOT NULL,
    hora TIME NOT NULL,
    nivel VARCHAR(45) NOT NULL,
    obs TEXT NULL,
    local VARCHAR(150) NULL,
    user_id INT NOT NULL,
    categoria_id INT NOT NULL,
    equipamento_id INT NULL,
    CONSTRAINT fk_chamado_user FOREIGN KEY (user_id) REFERENCES user(id),
    CONSTRAINT fk_chamado_categoria FOREIGN KEY (categoria_id) REFERENCES categoria(id),
    CONSTRAINT fk_chamado_equipamento FOREIGN KEY (equipamento_id) REFERENCES equipamentos(id)
);
