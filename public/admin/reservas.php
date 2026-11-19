<?php
/**
 * Lista de reservas com o título do livro.
 * Apostila PHP 4.5 — JOIN entre reservas e livros, com filtros opcionais por GET:
 *   admin/reservas.php?livro_id=1
 *   admin/reservas.php?situacao=ativa
 */

require __DIR__ . '/../../src/iniciar.php';
exigirLogin();

$livroId  = inteiroPositivo($_GET['livro_id'] ?? null);
$situacao = $_GET['situacao'] ?? '';
if (!is_string($situacao) || !array_key_exists($situacao, SITUACOES)) {
    $situacao = '';
}

$condicoes  = [];
$parametros = [];
if ($livroId !== null) {
    $condicoes[]  = 'r.livro_id = ?';
    $parametros[] = $livroId;
}
if ($situacao !== '') {
    $condicoes[]  = 'r.situacao = ?';
    $parametros[] = $situacao;
}

$stmt = banco()->prepare('
    SELECT r.id, r.nome, r.email, r.data_retirada, r.periodo, r.situacao,
           r.generos_interesse, r.observacoes, l.id AS livro_id, l.titulo
      FROM reservas AS r
      JOIN livros   AS l ON l.id = r.livro_id
    ' . ($condicoes ? 'WHERE ' . implode(' AND ', $condicoes) : '') . '
     ORDER BY r.data_retirada DESC, r.id DESC
');
$stmt->execute($parametros);
$reservas = $stmt->fetchAll();

$livros = banco()->query('SELECT id, titulo FROM livros ORDER BY titulo')->fetchAll();

// Ações possíveis a partir de cada situação (transições permitidas).
$acoes = [
    'ativa'    => ['retirar' => 'Registrar retirada', 'cancelar' => 'Cancelar'],
    'retirada' => ['devolver' => 'Registrar devolução'],
];

$tituloPagina = 'Reservas';
$paginaAtual  = 'admin';
$scripts      = ['js/admin.js'];
require __DIR__ . '/../../src/partes/topo.php';
?>

<h1>Reservas</h1>

<form class="filtros" action="<?= e(url('admin/reservas.php')) ?>" method="get">
    <div class="campo">
        <label for="filtro-livro">Livro</label>
        <select id="filtro-livro" name="livro_id">
            <option value="">Todos</option>
            <?php foreach ($livros as $livro): ?>
                <option value="<?= e($livro['id']) ?>" <?= $livro['id'] === $livroId ? 'selected' : '' ?>><?= e($livro['titulo']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="campo">
        <label for="filtro-situacao">Situação</label>
        <select id="filtro-situacao" name="situacao">
            <option value="">Todas</option>
            <?php foreach (SITUACOES as $valor => $rotulo): ?>
                <option value="<?= e($valor) ?>" <?= $valor === $situacao ? 'selected' : '' ?>><?= e($rotulo) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filtros__acoes">
        <button type="submit" class="botao">Filtrar</button>
    </div>
</form>

<?php if ($reservas === []): ?>
    <p class="vazio">Nenhuma reserva encontrada.</p>
<?php else: ?>
    <div class="tabela-rolavel">
        <table class="tabela">
            <caption><?= count($reservas) ?> reserva(s)</caption>
            <thead>
                <tr>
                    <th scope="col">Pessoa</th>
                    <th scope="col">Livro</th>
                    <th scope="col">Retirada</th>
                    <th scope="col">Situação</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <th scope="row">
                            <?= e($reserva['nome']) ?>
                            <span class="tabela__detalhe"><?= e($reserva['email']) ?></span>
                            <?php if ($reserva['observacoes'] !== ''): ?>
                                <span class="tabela__detalhe">Obs.: <?= e($reserva['observacoes']) ?></span>
                            <?php endif; ?>
                        </th>
                        <td><?= e($reserva['titulo']) ?></td>
                        <td>
                            <time datetime="<?= e($reserva['data_retirada']) ?>"><?= e(formatarData($reserva['data_retirada'])) ?></time>
                            <span class="tabela__detalhe"><?= e(PERIODOS[$reserva['periodo']]) ?></span>
                        </td>
                        <td><span class="situacao situacao--<?= e($reserva['situacao']) ?>"><?= e(SITUACOES[$reserva['situacao']]) ?></span></td>
                        <td>
                            <div class="acoes-linha">
                                <?php foreach ($acoes[$reserva['situacao']] ?? [] as $acao => $rotulo): ?>
                                    <form action="<?= e(url('admin/atualizar-reserva.php')) ?>" method="post"
                                        <?= $acao === 'cancelar' ? 'data-confirmar="Cancelar a reserva de ' . e($reserva['nome']) . '?"' : '' ?>>
                                        <?= campoCsrf() ?>
                                        <input type="hidden" name="id" value="<?= e($reserva['id']) ?>">
                                        <input type="hidden" name="acao" value="<?= e($acao) ?>">
                                        <button type="submit" class="botao botao--pequeno <?= $acao === 'cancelar' ? 'botao--perigo' : 'botao--secundario' ?>">
                                            <?= e($rotulo) ?>
                                        </button>
                                    </form>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../../src/partes/rodape.php'; ?>
