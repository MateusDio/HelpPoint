-- ============================================
-- HELPPOINT - Tabela CHAMADO_ANEXOS
-- ============================================
CREATE TABLE IF NOT EXISTS chamado_anexos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chamado_id INT NOT NULL,
    nome_arquivo VARCHAR(255) NOT NULL,
    nome_original VARCHAR(255) NOT NULL,
    tamanho INT NOT NULL,
    tipo_mime VARCHAR(100) NOT NULL,
    data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT NOT NULL,
    CONSTRAINT fk_anexo_chamado FOREIGN KEY (chamado_id) REFERENCES chamados(id) ON DELETE CASCADE,
    CONSTRAINT fk_anexo_user FOREIGN KEY (user_id) REFERENCES user(id)
);
