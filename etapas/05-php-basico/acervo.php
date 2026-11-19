<?php
/**
 * Etapa 05 — acervo com filtro por GET (Apostila PHP 2.2 e 2.3).
 * Exemplo: acervo.php?genero=romance
 */

require_once __DIR__ . '/dados.php';

// Leitura segura: ?? evita erro se o parâmetro não existir
$genero = (string) ($_GET['genero'] ?? '');

// Validação contra a lista permitida
if ($genero !== '' && !array_key_exists($genero, GENEROS)) {
    $genero = '';
}

// Filtra o array (sem banco ainda)
$lista = [];
foreach (livros() as $id => $livro) {
    if ($genero === '' || $livro['genero'] === $genero) {
        $lista[$id] = $livro;
    }
}

$tituloPagina = 'Acervo';
$paginaAtual  = 'acervo.php';
require __DIR__ . '/partes/topo.php';
?>

<h2>Acervo<?= $genero !== '' ? ' — ' . e(GENEROS[$genero]) : '' ?></h2>

<!-- method="get": o filtro aparece na URL e pode ser salvo nos favoritos -->
<form action="acervo.php" method="get">
    <p>
        <label for="genero">Gênero</label>
        <select id="genero" name="genero">
            <option value="">Todos</option>
            <?php foreach (GENEROS as $codigo => $nome): ?>
                <option value="<?= e($codigo) ?>" <?= $codigo === $genero ? 'selected' : '' ?>><?= e($nome) ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <button type="submit">Filtrar</button>
</form>

<?php if ($lista === []): ?>
    <p>Nenhum livro neste gênero.</p>
<?php else: ?>
    <table>
        <caption><?= count($lista) ?> livro(s)</caption>
        <thead>
            <tr>
                <th scope="col">Título</th>
                <th scope="col">Autor</th>
                <th scope="col">Disponíveis</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lista as $id => $livro): ?>
                <tr>
                    <th scope="row"><?= e($livro['titulo']) ?></th>
                    <td><?= e($livro['autor']) ?></td>
                    <td>
                        <?php if (disponiveis($livro) > 0): ?>
                            <?= disponiveis($livro) ?> de <?= $livro['exemplares'] ?>
                            — <a href="reservar.php?livro=<?= $id ?>">reservar</a>
                        <?php else: ?>
                            <strong>Esgotado</strong>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/partes/rodape.php'; ?>
