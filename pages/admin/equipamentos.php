```php
<?php
require_once __DIR__ . '/../../includes/global/auth.php';
redirectIfNotAdmin();

require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Equipamentos';
$currentPage = 'equipamentos';

/*
|--------------------------------------------------------------------------
| CADASTRAR EQUIPAMENTO
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $codigo = trim($_POST['codigo']);
    $estado_fisico = $_POST['estado_fisico'];
    $patrimonio = trim($_POST['patrimonio']);
    $categoria_id = (int) $_POST['categoria_id'];

    $sql = "
        INSERT INTO equipamentos
        (
            nome,
            descricao,
            codigo,
            estado_fisico,
            patrimonio,
            categoria_id
        )
        VALUES
        (
            :nome,
            :descricao,
            :codigo,
            :estado_fisico,
            :patrimonio,
            :categoria_id
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':nome' => $nome,
        ':descricao' => $descricao,
        ':codigo' => $codigo,
        ':estado_fisico' => $estado_fisico,
        ':patrimonio' => $patrimonio,
        ':categoria_id' => $categoria_id
    ]);

    header("Location: equipamentos.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| BUSCAR CATEGORIAS
|--------------------------------------------------------------------------
*/
$categorias = $pdo->query("
    SELECT *
    FROM categoria
    ORDER BY nome
")->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| LISTAR EQUIPAMENTOS
|--------------------------------------------------------------------------
*/
$equipamentos = $pdo->query("
    SELECT
        e.*,
        c.nome AS categoria
    FROM equipamentos e
    INNER JOIN categoria c
        ON c.id = e.categoria_id
    ORDER BY e.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../../includes/global/header.php';
require_once __DIR__ . '/../../includes/admin/sidebar.php';
?>

<div class="admin-page-header">
    <h1>
        <i class="bi bi-pc-display"></i>
        Equipamentos
    </h1>

    <button
        class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#modalEquipamento">

        <i class="bi bi-plus-lg"></i>
        Novo Equipamento
    </button>
</div>

<div class="crud-table">
    <table class="table table-hover">

        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th>N/S</th>
                <th>Patrimônio</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>

        <?php if(count($equipamentos) > 0): ?>

            <?php foreach($equipamentos as $equip): ?>

            <tr>

                <td><?= $equip['id']; ?></td>

                <td><?= htmlspecialchars($equip['nome']); ?></td>

                <td><?= htmlspecialchars($equip['categoria']); ?></td>

                <td><?= htmlspecialchars($equip['codigo']); ?></td>

                <td><?= htmlspecialchars($equip['patrimonio']); ?></td>

                <td>
                    <span class="badge bg-success">
                        <?= htmlspecialchars($equip['estado_fisico']); ?>
                    </span>
                </td>

            </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    Nenhum equipamento cadastrado
                </td>
            </tr>

        <?php endif; ?>

        </tbody>

    </table>
</div>

<!-- MODAL -->
<div class="modal fade" id="modalEquipamento" tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Novo Equipamento
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <form method="POST">

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">
                            Nome
                        </label>

                        <input
                            type="text"
                            name="nome"
                            class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Categoria
                        </label>

                        <select
                            name="categoria_id"
                            class="form-select"
                            required>

                            <option value="">
                                Selecione
                            </option>

                            <?php foreach($categorias as $categoria): ?>

                            <option value="<?= $categoria['id']; ?>">
                                <?= htmlspecialchars($categoria['nome']); ?>
                            </option>

                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Número de Série
                        </label>

                        <input
                            type="text"
                            name="codigo"
                            class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Patrimônio
                        </label>

                        <input
                            type="text"
                            name="patrimonio"
                            class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Estado Físico
                        </label>

                        <select
                            name="estado_fisico"
                            class="form-select">

                            <option value="Novo">Novo</option>
                            <option value="Bom">Bom</option>
                            <option value="Regular">Regular</option>
                            <option value="Ruim">Ruim</option>
                            <option value="Danificado">Danificado</option>

                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Descrição
                        </label>

                        <textarea
                            name="descricao"
                            class="form-control"
                            rows="3"></textarea>
                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        Salvar
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../../includes/admin/footer.php'; ?>
```
