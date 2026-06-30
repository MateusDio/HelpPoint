CREATE TABLE IF NOT EXISTS equipamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    n_serie VARCHAR(100) NULL,
    patrimonio VARCHAR(50) NULL UNIQUE,
    status VARCHAR(45) NOT NULL DEFAULT 'Disponivel',
    descricao TEXT NULL,
    tipo_id INT NOT NULL,
    CONSTRAINT fk_equip_tipo FOREIGN KEY (tipo_id) REFERENCES tipo(id)
);
