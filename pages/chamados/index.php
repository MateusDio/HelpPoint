<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotLogged();
if (isAdmin()) {
    header('Location: /HelpPoint/pages/admin/chamados.php');
    exit();
}
$pageTitle = 'Meus Chamados';
$currentPage = 'chamados';

$userId = (int)$_SESSION['user_id'];

$categorias = $pdo->query("SELECT * FROM categoria ORDER BY nome")->fetchAll();
$equipamentos = $pdo->query("
    SELECT e.id, e.patrimonio, e.n_serie, t.nome AS tipo_nome
    FROM equipamentos e
    INNER JOIN tipo t ON t.id = e.tipo_id
    ORDER BY t.nome
")->fetchAll();

$stmt = $pdo->prepare("
    SELECT c.*, cat.nome AS categoria_nome
    FROM chamados c
    INNER JOIN categoria cat ON cat.id = c.categoria_id
    WHERE c.user_id = :uid
    ORDER BY c.id DESC
");
$stmt->execute(['uid' => $userId]);
$chamados = $stmt->fetchAll();

$erro = $_GET['erro'] ?? '';
$sucesso = $_GET['sucesso'] ?? '';

require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/user/sidebar.php';

function badgeStatusCli($s) {
    $map = ['Aberto' => 'primary', 'Em Andamento' => 'warning', 'Concluido' => 'success', 'Cancelado' => 'secondary'];
    $cor = $map[$s] ?? 'dark';
    return "<span class='badge bg-$cor'>" . htmlspecialchars($s) . "</span>";
}
?>

<div class="admin-page-header">
    <h1><i class="bi bi-ticket"></i> Meus Chamados</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalChamado">
        <i class="bi bi-plus-lg"></i> Novo Chamado
    </button>
</div>

<?php if ($sucesso === 'criado'): ?><div class="alert alert-success alert-dismissible fade show">Chamado criado com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($erro === 'campos'): ?><div class="alert alert-danger alert-dismissible fade show">Preencha os campos obrigatorios!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<?php if (empty($chamados)): ?>
    <div class="crud-table p-5 text-center text-muted">
        <i class="bi bi-inbox" style="font-size: 3rem;"></i>
        <p class="mt-2 mb-0">Voce ainda nao abriu chamados</p>
    </div>
<?php else: ?>
<div class="chamado-grid">
    <?php foreach ($chamados as $c):
        $statusClass = ['Aberto'=>'aberto','Em Andamento'=>'andamento','Concluido'=>'concluido','Cancelado'=>'cancelado'][$c['status']] ?? 'aberto';
        $nivelClass = ['Baixo'=>'baixo','Medio'=>'medio','Alto'=>'alto'][$c['nivel']] ?? 'medio';
    ?>
        <div class="chamado-card chamado-status-<?= $statusClass ?>">
            <div class="chamado-card-head">
                <div class="chamado-card-id">
                    <span class="chamado-hash">#<?= $c['id'] ?></span>
                    <span class="chamado-categoria"><i class="bi bi-tag-fill"></i> <?= htmlspecialchars($c['categoria_nome']) ?></span>
                </div>
                <span class="chamado-status-pill"><?= htmlspecialchars($c['status']) ?></span>
            </div>
            <div class="chamado-card-body">
                <div class="chamado-info-row">
                    <i class="bi bi-geo-alt"></i>
                    <span><?= htmlspecialchars($c['local'] ?? '—') ?></span>
                </div>
                <div class="chamado-info-row">
                    <i class="bi bi-calendar-event"></i>
                    <span><?= date('d/m/Y', strtotime($c['data'])) ?> · <?= substr($c['hora'],0,5) ?></span>
                </div>
                <?php if (!empty($c['obs'])): ?>
                <div class="chamado-obs"><?= htmlspecialchars(mb_strimwidth($c['obs'], 0, 160, '...')) ?></div>
                <?php endif; ?>
            </div>
            <div class="chamado-card-foot">
                <span class="nivel-tag nivel-<?= $nivelClass ?>">
                    <i class="bi bi-circle-fill"></i> Nivel <?= htmlspecialchars($c['nivel']) ?>
                </span>
                <button class="btn btn-sm btn-outline-primary" onclick='verChamadoCli(<?= json_encode($c) ?>)'>
                    <i class="bi bi-eye"></i> Visualizar
                </button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal Visualizar Cliente -->
<div class="modal fade" id="modalVerChamadoCli" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chamado #<span id="vcCliId"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Categoria:</strong> <span id="vcCliCat"></span></p>
                <p><strong>Status:</strong> <span id="vcCliStatus"></span></p>
                <p><strong>Nivel:</strong> <span id="vcCliNivel"></span></p>
                <p><strong>Local:</strong> <span id="vcCliLocal"></span></p>
                <p><strong>Data:</strong> <span id="vcCliData"></span></p>
                <hr>
                <strong>Descricao:</strong>
                <div class="p-3 bg-light rounded mt-2" id="vcCliObs"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
function verChamadoCli(c) {
    document.getElementById('vcCliId').textContent = c.id;
    document.getElementById('vcCliCat').textContent = c.categoria_nome;
    document.getElementById('vcCliStatus').textContent = c.status;
    document.getElementById('vcCliNivel').textContent = c.nivel;
    document.getElementById('vcCliLocal').textContent = c.local || '—';
    document.getElementById('vcCliData').textContent = c.data + ' as ' + c.hora;
    document.getElementById('vcCliObs').textContent = c.obs || '(sem observacao)';
    new bootstrap.Modal(document.getElementById('modalVerChamadoCli')).show();
}
</script>
<?php endif; ?>

<div class="modal fade" id="modalChamado" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="chamados_process.php" method="POST" id="formNovoChamado">
                <input type="hidden" name="acao" value="criar">
                <input type="hidden" name="categoria_id" id="catSelecionadaId">
                <input type="hidden" name="equipamento_id" id="equipSelecionadoId" value="">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Novo Chamado</h5>
                        <small class="text-muted" id="wizardStepLabel">Etapa 1 de 3 — Categoria</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="wizard-progress">
                    <div class="wizard-bar"><div class="wizard-bar-fill" id="wizardBar" style="width:33%"></div></div>
                </div>

                <!-- ETAPA 1: Categoria -->
                <div class="modal-body wizard-step" id="step1">
                    <label class="form-label fw-semibold mb-2">Selecione a categoria</label>
                    <div class="cat-search mb-3">
                        <i class="bi bi-search"></i>
                        <input type="text" class="form-control" id="buscaCategoria" placeholder="Pesquisar categoria...">
                    </div>
                    <div class="cat-cards" id="catCards">
                        <?php foreach ($categorias as $cat): ?>
                            <div class="cat-card" data-id="<?= $cat['id'] ?>" data-nome="<?= htmlspecialchars(strtolower($cat['nome'])) ?>" onclick="escolherCategoria(this)">
                                <div class="cat-card-icon"><i class="bi bi-tag"></i></div>
                                <div class="cat-card-body">
                                    <div class="cat-card-title"><?= htmlspecialchars($cat['nome']) ?></div>
                                    <?php if (!empty($cat['descricao'])): ?>
                                        <div class="cat-card-desc"><?= htmlspecialchars($cat['descricao']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="cat-card-check"><i class="bi bi-check-circle-fill"></i></div>
                            </div>
                        <?php endforeach; ?>
                        <div class="text-center text-muted py-3 d-none" id="semResultado">Nenhuma categoria encontrada</div>
                    </div>
                </div>

                <!-- ETAPA 2: Equipamento -->
                <div class="modal-body wizard-step d-none" id="step2">
                    <div class="alert alert-light border d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-tag-fill text-primary"></i>
                        <span>Categoria: <strong id="catSelecionadaNome">—</strong></span>
                        <button type="button" class="btn btn-sm btn-link ms-auto p-0" onclick="irPara(1)">Alterar</button>
                    </div>

                    <label class="form-label fw-semibold mb-2">Selecione o equipamento <small class="text-muted">(opcional)</small></label>
                    <div class="cat-search mb-3">
                        <i class="bi bi-search"></i>
                        <input type="text" class="form-control" id="buscaEquipamento" placeholder="Pesquisar equipamento...">
                    </div>
                    <div class="cat-cards" id="equipCards">
                        <div class="cat-card" data-id="" data-nome="" onclick="escolherEquipamento(this, true)">
                            <div class="cat-card-icon"><i class="bi bi-slash-circle"></i></div>
                            <div class="cat-card-body">
                                <div class="cat-card-title">Nenhum equipamento</div>
                                <div class="cat-card-desc">Pular esta etapa</div>
                            </div>
                            <div class="cat-card-check"><i class="bi bi-check-circle-fill"></i></div>
                        </div>
                        <?php foreach ($equipamentos as $e):
                            $labelEquip = $e['tipo_nome'] . ' - ' . ($e['patrimonio'] ?? $e['n_serie'] ?? '#'.$e['id']);
                        ?>
                            <div class="cat-card" data-id="<?= $e['id'] ?>" data-nome="<?= htmlspecialchars(strtolower($labelEquip)) ?>" onclick="escolherEquipamento(this, false)">
                                <div class="cat-card-icon"><i class="bi bi-pc-display"></i></div>
                                <div class="cat-card-body">
                                    <div class="cat-card-title"><?= htmlspecialchars($e['tipo_nome']) ?></div>
                                    <div class="cat-card-desc">
                                        <?php if (!empty($e['patrimonio'])): ?>Patrimonio: <?= htmlspecialchars($e['patrimonio']) ?><?php endif; ?>
                                        <?php if (!empty($e['n_serie'])): ?> · N/S: <?= htmlspecialchars($e['n_serie']) ?><?php endif; ?>
                                    </div>
                                </div>
                                <div class="cat-card-check"><i class="bi bi-check-circle-fill"></i></div>
                            </div>
                        <?php endforeach; ?>
                        <div class="text-center text-muted py-3 d-none" id="semResultadoEquip">Nenhum equipamento encontrado</div>
                    </div>
                </div>

                <!-- ETAPA 3: Detalhes -->
                <div class="modal-body wizard-step d-none" id="step3">
                    <div class="alert alert-light border d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-tag-fill text-primary"></i>
                        <span>Categoria: <strong id="catSelecionadaNome2">—</strong></span>
                        <button type="button" class="btn btn-sm btn-link ms-auto p-0" onclick="irPara(1)">Alterar</button>
                    </div>
                    <div class="alert alert-light border d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-pc-display text-primary"></i>
                        <span>Equipamento: <strong id="equipSelecionadoNome">Nenhum</strong></span>
                        <button type="button" class="btn btn-sm btn-link ms-auto p-0" onclick="irPara(2)">Alterar</button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nivel <span class="text-danger">*</span></label>
                        <div class="nivel-options">
                            <label class="nivel-opt">
                                <input type="radio" name="nivel" value="Baixo">
                                <span class="nivel-pill nivel-baixo"><i class="bi bi-circle-fill"></i> Baixo</span>
                            </label>
                            <label class="nivel-opt">
                                <input type="radio" name="nivel" value="Medio" checked>
                                <span class="nivel-pill nivel-medio"><i class="bi bi-circle-fill"></i> Medio</span>
                            </label>
                            <label class="nivel-opt">
                                <input type="radio" name="nivel" value="Alto">
                                <span class="nivel-pill nivel-alto"><i class="bi bi-circle-fill"></i> Alto</span>
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Local</label>
                        <input type="text" class="form-control" name="local" placeholder="Ex: Sala 12, 2o andar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descricao do problema <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="obs" rows="4" placeholder="Conte com detalhes o que esta acontecendo..." required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-muted" id="btnVoltar" onclick="voltarEtapa()" style="display:none">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnAvancar" onclick="avancarEtapa()" disabled>
                        Avancar <i class="bi bi-arrow-right"></i>
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnEnviar" style="display:none">
                        <i class="bi bi-send"></i> Enviar Chamado
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let etapaAtual = 1;

function escolherCategoria(el) {
    document.querySelectorAll('#catCards .cat-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('catSelecionadaId').value = el.dataset.id;
    const nome = el.querySelector('.cat-card-title').textContent;
    document.getElementById('catSelecionadaNome').textContent = nome;
    document.getElementById('catSelecionadaNome2').textContent = nome;
    document.getElementById('btnAvancar').disabled = false;
}

function escolherEquipamento(el, isNone) {
    document.querySelectorAll('#equipCards .cat-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('equipSelecionadoId').value = el.dataset.id;
    document.getElementById('equipSelecionadoNome').textContent = isNone ? 'Nenhum' : el.querySelector('.cat-card-title').textContent;
    document.getElementById('btnAvancar').disabled = false;
}

function atualizarUI() {
    [1,2,3].forEach(n => document.getElementById('step'+n).classList.add('d-none'));
    document.getElementById('step'+etapaAtual).classList.remove('d-none');
    document.getElementById('wizardBar').style.width = (etapaAtual * 33.33) + '%';
    document.getElementById('wizardStepLabel').textContent =
        etapaAtual === 1 ? 'Etapa 1 de 3 — Categoria' :
        etapaAtual === 2 ? 'Etapa 2 de 3 — Equipamento' :
                           'Etapa 3 de 3 — Detalhes';

    document.getElementById('btnVoltar').style.display = etapaAtual > 1 ? 'inline-block' : 'none';
    document.getElementById('btnAvancar').style.display = etapaAtual < 3 ? 'inline-block' : 'none';
    document.getElementById('btnEnviar').style.display = etapaAtual === 3 ? 'inline-block' : 'none';

    if (etapaAtual === 1) {
        document.getElementById('btnAvancar').disabled = !document.getElementById('catSelecionadaId').value;
    } else if (etapaAtual === 2) {
        const selecionado = document.querySelector('#equipCards .cat-card.selected');
        document.getElementById('btnAvancar').disabled = !selecionado;
    }
}

function avancarEtapa() {
    if (etapaAtual < 3) { etapaAtual++; atualizarUI(); }
}
function voltarEtapa() {
    if (etapaAtual > 1) { etapaAtual--; atualizarUI(); }
}
function irPara(n) { etapaAtual = n; atualizarUI(); }

function filtrar(inputId, containerId, semId) {
    document.getElementById(inputId).addEventListener('input', function(e) {
        const termo = e.target.value.toLowerCase().trim();
        let v = 0;
        document.querySelectorAll('#' + containerId + ' .cat-card').forEach(c => {
            if (c.dataset.id === '') { c.style.display = ''; return; }
            const match = c.dataset.nome.includes(termo);
            c.style.display = match ? '' : 'none';
            if (match) v++;
        });
        document.getElementById(semId).classList.toggle('d-none', v !== 0);
    });
}
filtrar('buscaCategoria', 'catCards', 'semResultado');
filtrar('buscaEquipamento', 'equipCards', 'semResultadoEquip');

document.getElementById('modalChamado').addEventListener('hidden.bs.modal', function() {
    document.getElementById('formNovoChamado').reset();
    document.getElementById('catSelecionadaId').value = '';
    document.getElementById('equipSelecionadoId').value = '';
    document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('selected'));
    etapaAtual = 1;
    atualizarUI();
    document.getElementById('buscaCategoria').value = '';
    document.getElementById('buscaEquipamento').value = '';
    document.querySelectorAll('.cat-card').forEach(c => c.style.display = '');
    document.getElementById('semResultado').classList.add('d-none');
    document.getElementById('semResultadoEquip').classList.add('d-none');
});
</script>

<?php require_once __DIR__ . '/../../includes/user/footer.php'; ?>
