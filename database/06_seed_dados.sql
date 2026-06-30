

-- --------------------------------------------
-- TIPOS (de equipamento)
-- --------------------------------------------
INSERT INTO tipo (nome) VALUES
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

-- --------------------------------------------
-- CATEGORIAS (de chamados)
-- --------------------------------------------
INSERT INTO categoria (nome, descricao) VALUES
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
