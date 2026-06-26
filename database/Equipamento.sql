CREATE TABLE categoria (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT
);

CREATE TABLE equipamentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    estado_fisico ENUM(
        'Novo',
        'Bom',
        'Regular',
        'Ruim',
        'Danificado'
    ) NOT NULL,
    patrimonio VARCHAR(50) UNIQUE,
    categoria_id INT NOT NULL,

    FOREIGN KEY (categoria_id) REFERENCES categoria(id)
);