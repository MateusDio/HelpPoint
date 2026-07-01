<?php
require_once __DIR__ . '/../../includes/global/auth.php';
require_once __DIR__ . '/../../config/database.php';
redirectIfNotAdmin();
$pageTitle = 'Chamados';
$currentPage = 'chamados';

$filtroStatus = $_GET['status'] ?? '';
$where = '';
$params = [];
if ($filtroStatus !== '') {
    $where = 'WHERE c.status = :s';
    $params['s'] = $filtroStatus;
}

$sql = "
    SELECT c.*,
           u.nome AS user_nome,
           cat.nome AS categoria_nome,
           e.patrimonio AS equip_patrimonio,
           tp.nome AS equip_tipo
    FROM chamados c
    INNER JOIN user u ON u.id = c.user_id
    INNER JOIN categoria cat ON cat.id = c.categoria_id
    LEFT JOIN equipamentos e ON e.id = c.equipamento_id
    LEFT JOIN tipo tp ON tp.id = e.tipo_id
    $where
    ORDER BY c.id DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$chamados = $stmt->fetchAll();

$sucesso = $_GET['sucesso'] ?? '';

require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/admin/sidebar.php';

function badgeStatus($s) {
    $map = ['Aberto' => 'primary', 'Em Andamento' => 'warning', 'Concluido' => 'success', 'Cancelado' => 'secondary'];
    $cor = $map[$s] ?? 'dark';
    return "<span class='badge bg-$cor'>" . htmlspecialchars($s) . "</span>";
}
function badgeNivel($n) {
    $map = ['Baixo' => 'success', 'Medio' => 'warning', 'Alto' => 'danger'];
    $cor = $map[$n] ?? 'dark';
    return "<span class='badge bg-$cor'>" . htmlspecialchars($n) . "</span>";
}
?>

<div class="admin-page-header">
    <h1><i class="bi bi-ticket"></i> Chamados</h1>
    <form method="GET" class="d-flex gap-2">
        <select name="status" class="form-select" onchange="this.form.submit()">
            <option value="">Todos os status</option>
            <?php foreach (['Aberto','Em Andamento','Concluido','Cancelado'] as $s): ?>
                <option value="<?= $s ?>" <?= $filtroStatus === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<?php if ($sucesso): ?><div class="alert alert-success alert-dismissible fade show">Operacao realizada com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<?php if (empty($chamados)): ?>
    <div class="crud-table p-5 text-center text-muted">
        <i class="bi bi-inbox" style="font-size: 3rem;"></i>
        <p class="mt-2 mb-0">Nenhum chamado registrado</p>
    </div>
<?php else: ?>
<div class="chamado-grid">
    <?php foreach ($chamados as $c): ?>
        <?php
            $statusKey = $c['status'];
            $statusClass = ['Aberto'=>'aberto','Em Andamento'=>'andamento','Concluido'=>'concluido','Cancelado'=>'cancelado'][$statusKey] ?? 'aberto';
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
                    <i class="bi bi-person-circle"></i>
                    <span><?= htmlspecialchars($c['user_nome']) ?></span>
                </div>
                <div class="chamado-info-row">
                    <i class="bi bi-geo-alt"></i>
                    <span><?= htmlspecialchars($c['local'] ?? '—') ?></span>
                </div>
                <div class="chamado-info-row">
                    <i class="bi bi-pc-display"></i>
                    <span><?= $c['equip_patrimonio'] ? htmlspecialchars($c['equip_tipo'].' ('.$c['equip_patrimonio'].')') : '—' ?></span>
                </div>
                <div class="chamado-info-row">
                    <i class="bi bi-calendar-event"></i>
                    <span><?= date('d/m/Y', strtotime($c['data'])) ?> · <?= substr($c['hora'],0,5) ?></span>
                </div>

                <?php if (!empty($c['obs'])): ?>
                <div class="chamado-obs">
                    <?= htmlspecialchars(mb_strimwidth($c['obs'], 0, 140, '...')) ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="chamado-card-foot">
                <span class="nivel-tag nivel-<?= $nivelClass ?>">
                    <i class="bi bi-circle-fill"></i> Nivel <?= htmlspecialchars($c['nivel']) ?>
                </span>
                <div class="chamado-actions">
                    <button class="btn btn-sm btn-outline-primary" onclick='verChamado(<?= json_encode($c) ?>)'>
                        <i class="bi bi-eye"></i> Visualizar
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick='abrirStatus(<?= json_encode($c) ?>)'>
                        <i class="bi bi-arrow-repeat"></i> Status
                    </button>
                    <a href="chamados_process.php?acao=excluir&id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir este chamado?')">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Modal Visualizar -->
<div class="modal fade" id="modalVerChamado" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chamado #<span id="vcIdLabel"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6"><strong>Usuario:</strong> <span id="vcUser"></span></div>
                    <div class="col-md-6"><strong>Categoria:</strong> <span id="vcCat"></span></div>
                    <div class="col-md-6"><strong>Equipamento:</strong> <span id="vcEquip"></span></div>
                    <div class="col-md-6"><strong>Local:</strong> <span id="vcLocal"></span></div>
                    <div class="col-md-6"><strong>Nivel:</strong> <span id="vcNivel"></span></div>
                    <div class="col-md-6"><strong>Status:</strong> <span id="vcStatus"></span></div>
                    <div class="col-md-12"><strong>Data/Hora:</strong> <span id="vcData"></span></div>
                </div>
                <hr>
                <strong>Descricao do problema:</strong>
                <div class="p-3 bg-light rounded mt-2" id="vcObs"></div>
                <hr>
                <strong>Anexos:</strong>
                <div id="vcAnexos" class="mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Alterar Status -->
<div class="modal fade" id="modalStatus" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content">
            <form action="chamados_process.php" method="POST">
                <input type="hidden" name="acao" value="status">
                <input type="hidden" name="id" id="stId">
                <div class="modal-header">
                    <h5 class="modal-title">Alterar Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Chamado <strong>#<span id="stIdLabel"></span></strong></p>
                    <div class="status-options">
                        <?php foreach (['Aberto'=>'primary','Em Andamento'=>'warning','Concluido'=>'success','Cancelado'=>'secondary'] as $s => $cor): ?>
                            <label class="status-opt">
                                <input type="radio" name="status" value="<?= $s ?>" class="stRadio">
                                <span class="status-pill status-<?= strtolower(str_replace(' ', '-', $s)) ?>"><?= $s ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function verChamado(c) {
    document.getElementById('vcIdLabel').textContent = c.id;
    document.getElementById('vcUser').textContent = c.user_nome;
    document.getElementById('vcCat').textContent = c.categoria_nome;
    document.getElementById('vcEquip').textContent = c.equip_patrimonio ? (c.equip_tipo + ' (' + c.equip_patrimonio + ')') : '—';
    document.getElementById('vcLocal').textContent = c.local || '—';
    document.getElementById('vcNivel').textContent = c.nivel;
    document.getElementById('vcStatus').textContent = c.status;
    document.getElementById('vcData').textContent = c.data + ' as ' + c.hora;
    document.getElementById('vcObs').textContent = c.obs || '(sem observacao)';
    
    // Carregar anexos via AJAX
    const anexosDiv = document.getElementById('vcAnexos');
    anexosDiv.innerHTML = '<small class="text-muted">Carregando...</small>';
    
    fetch('chamados/anexos_list.php?chamado_id=' + c.id)
        .then(r => r.text())
        .then(html => anexosDiv.innerHTML = html || '<small class="text-muted">Sem anexos</small>')
        .catch(e => anexosDiv.innerHTML = '<small class="text-danger">Erro ao carregar anexos</small>');
    
    new bootstrap.Modal(document.getElementById('modalVerChamado')).show();
}

function abrirStatus(c) {
    document.getElementById('stId').value = c.id;
    document.getElementById('stIdLabel').textContent = c.id;
    document.querySelectorAll('.stRadio').forEach(r => r.checked = (r.value === c.status));
    new bootstrap.Modal(document.getElementById('modalStatus')).show();
}
</script>

<?php require_once __DIR__ . '/../../includes/admin/footer.php'; ?>
