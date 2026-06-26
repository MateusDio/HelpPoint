-- ============================================================
--  HELP POINT — Banco de Dados MySQL
--  Tabelas: usuarios, servicos, chamados, historico, comentarios
-- ============================================================

CREATE DATABASE IF NOT EXISTS chamadobanco
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE chamadobanco;

-- ------------------------------------------------------------
-- 1. USUÁRIOS
-- ------------------------------------------------------------
CREATE TABLE usuarios (
  id           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  nome         VARCHAR(120)    NOT NULL,
  email        VARCHAR(180)    NOT NULL UNIQUE,
  senha_hash   VARCHAR(255)    NOT NULL,
  perfil       ENUM('usuario','tecnico','admin') NOT NULL DEFAULT 'usuario',
  setor        VARCHAR(100)        NULL,
  ativo        TINYINT(1)      NOT NULL DEFAULT 1,
  criado_em    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_email (email),
  INDEX idx_perfil (perfil)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 2. SERVIÇOS (categorias de chamado)
-- ------------------------------------------------------------
CREATE TABLE servicos (
  id           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  nome         VARCHAR(100)    NOT NULL,
  descricao    TEXT                NULL,
  ativo        TINYINT(1)      NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

INSERT INTO servicos (nome) VALUES
  ('Hardware'),
  ('Software'),
  ('Rede'),
  ('Acesso / Senha'),
  ('Impressora'),
  ('Outros');

-- ------------------------------------------------------------
-- 3. CHAMADOS
-- ------------------------------------------------------------
CREATE TABLE chamados (
  id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  titulo          VARCHAR(200)    NOT NULL,
  descricao       TEXT                NULL,
  prioridade      ENUM('baixo','medio','alto','critico') NOT NULL DEFAULT 'baixo',
  status          ENUM('aberto','em_andamento','aguardando','resolvido','fechado') NOT NULL DEFAULT 'aberto',
  local           VARCHAR(150)        NULL,
  data_abertura   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  data_previsao   DATETIME            NULL,
  data_resolucao  DATETIME            NULL,
  usuario_id      INT UNSIGNED    NOT NULL,
  tecnico_id      INT UNSIGNED        NULL,
  servico_id      INT UNSIGNED    NOT NULL,
  criado_em       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_status    (status),
  INDEX idx_prioridade (prioridade),
  INDEX idx_usuario   (usuario_id),
  INDEX idx_tecnico   (tecnico_id),
  CONSTRAINT fk_chamado_usuario  FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE RESTRICT,
  CONSTRAINT fk_chamado_tecnico  FOREIGN KEY (tecnico_id) REFERENCES usuarios (id) ON DELETE SET NULL,
  CONSTRAINT fk_chamado_servico  FOREIGN KEY (servico_id) REFERENCES servicos (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 4. HISTÓRICO DE ALTERAÇÕES
-- ------------------------------------------------------------
CREATE TABLE historico (
  id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  chamado_id      INT UNSIGNED    NOT NULL,
  usuario_id      INT UNSIGNED    NOT NULL,
  campo_alterado  VARCHAR(80)     NOT NULL,
  valor_anterior  TEXT                NULL,
  valor_novo      TEXT                NULL,
  criado_em       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_chamado (chamado_id),
  CONSTRAINT fk_historico_chamado  FOREIGN KEY (chamado_id) REFERENCES chamados  (id) ON DELETE CASCADE,
  CONSTRAINT fk_historico_usuario  FOREIGN KEY (usuario_id) REFERENCES usuarios  (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 5. COMENTÁRIOS
-- ------------------------------------------------------------
CREATE TABLE comentarios (
  id           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  chamado_id   INT UNSIGNED    NOT NULL,
  usuario_id   INT UNSIGNED    NOT NULL,
  texto        TEXT            NOT NULL,
  interno      TINYINT(1)      NOT NULL DEFAULT 0,  -- 1 = visível só para técnicos
  criado_em    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_chamado (chamado_id),
  CONSTRAINT fk_comentario_chamado  FOREIGN KEY (chamado_id) REFERENCES chamados  (id) ON DELETE CASCADE,
  CONSTRAINT fk_comentario_usuario  FOREIGN KEY (usuario_id) REFERENCES usuarios  (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 6. TRIGGER — registra histórico ao atualizar status
-- ------------------------------------------------------------
DELIMITER $$

CREATE TRIGGER trg_historico_status
BEFORE UPDATE ON chamados
FOR EACH ROW
BEGIN
  IF OLD.status <> NEW.status THEN
    INSERT INTO historico (chamado_id, usuario_id, campo_alterado, valor_anterior, valor_novo)
    VALUES (OLD.id, NEW.tecnico_id, 'status', OLD.status, NEW.status);
  END IF;

  IF OLD.prioridade <> NEW.prioridade THEN
    INSERT INTO historico (chamado_id, usuario_id, campo_alterado, valor_anterior, valor_novo)
    VALUES (OLD.id, NEW.tecnico_id, 'prioridade', OLD.prioridade, NEW.prioridade);
  END IF;

  IF (OLD.tecnico_id IS NULL AND NEW.tecnico_id IS NOT NULL)
     OR (OLD.tecnico_id <> NEW.tecnico_id) THEN
    INSERT INTO historico (chamado_id, usuario_id, campo_alterado, valor_anterior, valor_novo)
    VALUES (OLD.id, NEW.tecnico_id, 'tecnico_id',
            CAST(OLD.tecnico_id AS CHAR), CAST(NEW.tecnico_id AS CHAR));
  END IF;
END$$

DELIMITER ;

-- ------------------------------------------------------------
-- 7. DADOS DE EXEMPLO
-- ------------------------------------------------------------

-- Usuários (senhas em produção devem ser bcrypt)
INSERT INTO usuarios (nome, email, senha_hash, perfil, setor) VALUES
  ('Admin Sistema',   'admin@chamadobanco.com',   '$2b$10$exemplo_hash_admin',   'admin',   'TI'),
  ('Carlos Técnico',  'carlos@chamadobanco.com',  '$2b$10$exemplo_hash_carlos',  'tecnico', 'TI'),
  ('Ana Usuária',     'ana@chamadobanco.com',     '$2b$10$exemplo_hash_ana',     'usuario', 'Financeiro'),
  ('Pedro Usuário',   'pedro@chamadobanco.com',   '$2b$10$exemplo_hash_pedro',   'usuario', 'RH');

-- Chamados de exemplo
INSERT INTO chamados (titulo, descricao, prioridade, status, local, usuario_id, tecnico_id, servico_id) VALUES
  ('Computador não liga', 'Desktop da sala 101 não liga desde ontem.', 'alto',   'em_andamento', 'Sala 101',  3, 2, 1),
  ('Sem acesso ao sistema', 'Não consigo logar no ERP após troca de senha.', 'critico','aberto', 'Andar 3',   4, NULL, 4),
  ('Impressora travada', 'Impressora da recepção parou de imprimir.', 'medio', 'aberto', 'Recepção', 3, NULL, 5);

-- Comentários de exemplo
INSERT INTO comentarios (chamado_id, usuario_id, texto, interno) VALUES
  (1, 2, 'Verificado: fonte do computador com defeito. Aguardando peça.', 0),
  (1, 2, 'Nota interna: solicitar peça ao fornecedor X.', 1),
  (2, 4, 'Urgente, preciso acessar o sistema para fechar o mês.', 0);

-- ------------------------------------------------------------
-- 8. VIEWS ÚTEIS
-- ------------------------------------------------------------

-- Resumo de chamados com nomes
CREATE OR REPLACE VIEW vw_chamados AS
SELECT
  c.id,
  c.titulo,
  c.prioridade,
  c.status,
  c.local,
  c.data_abertura,
  c.data_resolucao,
  s.nome          AS servico,
  u.nome          AS solicitante,
  t.nome          AS tecnico
FROM chamados c
JOIN servicos  s ON s.id = c.servico_id
JOIN usuarios  u ON u.id = c.usuario_id
LEFT JOIN usuarios t ON t.id = c.tecnico_id;

-- Chamados abertos por prioridade
CREATE OR REPLACE VIEW vw_chamados_abertos AS
SELECT prioridade, COUNT(*) AS total
FROM chamados
WHERE status NOT IN ('resolvido','fechado')
GROUP BY prioridade
ORDER BY FIELD(prioridade,'critico','alto','medio','baixo');