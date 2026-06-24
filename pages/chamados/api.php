<?php
// ============================================================
//  HELP POINT — API REST simples
//  GET  api.php?acao=chamados          → lista chamados ativos
//  GET  api.php?acao=servicos          → lista serviços
//  POST api.php?acao=novo_chamado      → cria chamado
//  POST api.php?acao=fechar_chamado    → fecha chamado (id no body)
// ============================================================

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once 'db.php';

$acao = $_GET['acao'] ?? '';

try {
    $pdo = getDB();

    // --------------------------------------------------------
    // GET: listar chamados ativos
    // --------------------------------------------------------
    if ($acao === 'chamados' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->query("
            SELECT
                c.id,
                c.titulo,
                c.prioridade,
                c.status,
                c.local,
                c.data_abertura,
                s.nome  AS servico,
                u.nome  AS solicitante,
                t.nome  AS tecnico
            FROM chamados c
            JOIN servicos  s ON s.id = c.servico_id
            JOIN usuarios  u ON u.id = c.usuario_id
            LEFT JOIN usuarios t ON t.id = c.tecnico_id
            WHERE c.status NOT IN ('resolvido','fechado')
            ORDER BY
                FIELD(c.prioridade,'critico','alto','medio','baixo'),
                c.data_abertura DESC
            LIMIT 50
        ");
        echo json_encode(['sucesso' => true, 'dados' => $stmt->fetchAll()]);
        exit;
    }

    // --------------------------------------------------------
    // GET: listar serviços
    // --------------------------------------------------------
    if ($acao === 'servicos' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->query("SELECT id, nome FROM servicos WHERE ativo = 1 ORDER BY nome");
        echo json_encode(['sucesso' => true, 'dados' => $stmt->fetchAll()]);
        exit;
    }

    // --------------------------------------------------------
    // POST: criar novo chamado
    // --------------------------------------------------------
    if ($acao === 'novo_chamado' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $body = json_decode(file_get_contents('php://input'), true);

        $titulo     = trim($body['titulo']     ?? '');
        $descricao  = trim($body['descricao']  ?? '');
        $prioridade = $body['prioridade']       ?? 'baixo';
        $local      = trim($body['local']       ?? '');
        $servico_id = (int)($body['servico_id'] ?? 0);
        $usuario_id = (int)($body['usuario_id'] ?? 1); // padrão: usuário 1 (demo)

        // Validações básicas
        if (!$titulo || !$local || !$servico_id) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'erro' => 'Título, local e serviço são obrigatórios.']);
            exit;
        }

        $prioridades_validas = ['baixo', 'medio', 'alto', 'critico'];
        if (!in_array($prioridade, $prioridades_validas)) {
            $prioridade = 'baixo';
        }

        $stmt = $pdo->prepare("
            INSERT INTO chamados (titulo, descricao, prioridade, local, usuario_id, servico_id)
            VALUES (:titulo, :descricao, :prioridade, :local, :usuario_id, :servico_id)
        ");
        $stmt->execute([
            ':titulo'     => $titulo,
            ':descricao'  => $descricao,
            ':prioridade' => $prioridade,
            ':local'      => $local,
            ':usuario_id' => $usuario_id,
            ':servico_id' => $servico_id,
        ]);

        $novo_id = $pdo->lastInsertId();

        // Registrar no histórico
        $pdo->prepare("
            INSERT INTO historico (chamado_id, usuario_id, campo_alterado, valor_anterior, valor_novo)
            VALUES (:cid, :uid, 'status', NULL, 'aberto')
        ")->execute([':cid' => $novo_id, ':uid' => $usuario_id]);

        echo json_encode(['sucesso' => true, 'id' => $novo_id, 'mensagem' => 'Chamado aberto com sucesso!']);
        exit;
    }

    // --------------------------------------------------------
    // POST: fechar chamado
    // --------------------------------------------------------
    if ($acao === 'fechar_chamado' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $body = json_decode(file_get_contents('php://input'), true);
        $id   = (int)($body['id'] ?? 0);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'erro' => 'ID inválido.']);
            exit;
        }

        $pdo->prepare("
            UPDATE chamados SET status = 'fechado', data_resolucao = NOW() WHERE id = :id
        ")->execute([':id' => $id]);

        echo json_encode(['sucesso' => true, 'mensagem' => 'Chamado fechado.']);
        exit;
    }

    // Ação não encontrada
    http_response_code(404);
    echo json_encode(['sucesso' => false, 'erro' => 'Ação não encontrada.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => 'Erro no banco: ' . $e->getMessage()]);
}