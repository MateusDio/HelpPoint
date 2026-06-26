<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfNotLogged();

header('Content-Type: application/json; charset=utf-8');

$acao   = $_GET['acao'] ?? '';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

function responder(array $dados): void {
    echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function erro(string $mensagem, int $http = 400): void {
    http_response_code($http);
    responder(['sucesso' => false, 'erro' => $mensagem]);
}

function bodyJson(): array {
    $raw = file_get_contents('php://input');
    $dados = json_decode($raw, true);
    return is_array($dados) ? $dados : [];
}

try {
    match ($acao) {
        'servicos'       => listarServicos($pdo),
        'chamados'       => listarChamados($pdo),
        'novo_chamado'   => novoChamado($pdo),
        'fechar_chamado' => fecharChamado($pdo),
        default          => erro('Ação inválida.', 404),
    };
} catch (Throwable $e) {
    erro('Erro interno: ' . $e->getMessage(), 500);
}

// ── Listar serviços ativos ───────────────────────────────────────────────────
function listarServicos(PDO $pdo): void {
    $stmt = $pdo->query("SELECT id, nome FROM servicos WHERE ativo = 1 ORDER BY nome");
    responder(['sucesso' => true, 'dados' => $stmt->fetchAll()]);
}

// ── Listar chamados abertos do usuário logado ────────────────────────────────
function listarChamados(PDO $pdo): void {
    $usuario_id = $_SESSION['usuario_id'] ?? 0;

    $stmt = $pdo->prepare("
        SELECT
            c.id,
            c.titulo,
            c.prioridade,
            c.status,
            c.local,
            c.data_abertura,
            s.nome AS servico,
            u.nome AS usuario
        FROM chamados c
        JOIN servicos s ON s.id = c.servico_id
        JOIN usuarios u ON u.id = c.usuario_id
        WHERE c.usuario_id = ?
          AND c.status NOT IN ('resolvido', 'fechado')
        ORDER BY
            FIELD(c.prioridade, 'critico', 'alto', 'medio', 'baixo'),
            c.data_abertura DESC
    ");
    $stmt->execute([$usuario_id]);
    responder(['sucesso' => true, 'dados' => $stmt->fetchAll()]);
}

// ── Abrir novo chamado ───────────────────────────────────────────────────────
function novoChamado(PDO $pdo): void {
    $body = bodyJson();

    $titulo     = trim($body['titulo']     ?? '');
    $descricao  = trim($body['descricao']  ?? '');
    $prioridade = trim($body['prioridade'] ?? 'baixo');
    $local      = trim($body['local']      ?? '');
    $servico_id = (int) ($body['servico_id'] ?? 0);
    $usuario_id = $_SESSION['usuario_id'] ?? 0;

    if ($titulo === '')    erro('Título é obrigatório.');
    if ($local  === '')    erro('Local é obrigatório.');
    if ($servico_id <= 0)  erro('Serviço inválido.');
    if ($usuario_id <= 0)  erro('Sessão inválida. Faça login novamente.', 401);
    if (!in_array($prioridade, ['baixo','medio','alto','critico'], true))
                           erro('Prioridade inválida.');

    $stmt = $pdo->prepare("
        INSERT INTO chamados (titulo, descricao, prioridade, local, usuario_id, servico_id)
        VALUES (:titulo, :descricao, :prioridade, :local, :usuario_id, :servico_id)
    ");
    $stmt->execute([
        ':titulo'      => $titulo,
        ':descricao'   => $descricao,
        ':prioridade'  => $prioridade,
        ':local'       => $local,
        ':usuario_id'  => $usuario_id,
        ':servico_id'  => $servico_id,
    ]);

    responder(['sucesso' => true, 'id' => (int) $pdo->lastInsertId()]);
}

// ── Fechar chamado (só o dono pode fechar o seu) ────────────────────────────
function fecharChamado(PDO $pdo): void {
    $body = bodyJson();
    $id   = (int) ($body['id'] ?? 0);
    $usuario_id = $_SESSION['usuario_id'] ?? 0;

    if ($id <= 0)         erro('ID inválido.');
    if ($usuario_id <= 0) erro('Sessão inválida.', 401);

    $stmt = $pdo->prepare("
        UPDATE chamados
        SET status = 'fechado'
        WHERE id = ? AND usuario_id = ? AND status NOT IN ('resolvido','fechado')
    ");
    $stmt->execute([$id, $usuario_id]);

    if ($stmt->rowCount() === 0)
        erro('Chamado não encontrado ou já encerrado.');

    responder(['sucesso' => true, 'id' => $id]);
}