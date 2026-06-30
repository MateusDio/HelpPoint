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
