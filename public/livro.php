<?php
/**
 * Detalhe de um livro: livro.php?id=3
 * Demonstra leitura de parâmetro GET validado e o status 404 (Apostila 01, 5.5).
 */

require __DIR__ . '/../src/iniciar.php';

$id = inteiroPositivo($_GET['id'] ?? null);
$livro = $id === null ? null : buscarLivro($id);

if ($livro === null) {
    paginaDeErro(404, 'Livro não encontrado', 'Não existe livro com esse código no acervo.');
}

$tituloPagina = $livro['titulo'];
$paginaAtual  = 'acervo';
require __DIR__ . '/../src/partes/topo.php';
?>

<!-- Trilha de navegação: mostra onde a pessoa está -->
<nav aria-label="Você está em" class="trilha">
    <ol>
        <li><a href="<?= e(url('acervo.php')) ?>">Acervo</a></li>
        <li><a href="<?= e(url('acervo.php?genero=' . urlencode($livro['genero']))) ?>"><?= e(GENEROS[$livro['genero']]) ?></a></li>
        <li aria-current="page"><?= e($livro['titulo']) ?></li>
    </ol>
</nav>

<article class="detalhe-livro">
    <div class="capa capa--grande capa--<?= e($livro['genero']) ?>" aria-hidden="true">
        <span><?= e($livro['titulo']) ?></span>
    </div>

    <div class="detalhe-livro__texto">
        <h1><?= e($livro['titulo']) ?></h1>
        <p class="detalhe-livro__autor">por <?= e($livro['autor']) ?></p>

        <dl class="ficha">
            <dt>Gênero</dt>
            <dd><?= e(GENEROS[$livro['genero']]) ?></dd>
            <dt>Ano de publicação</dt>
            <dd><?= e($livro['ano_publicacao']) ?></dd>
            <dt>Exemplares</dt>
            <dd><?= e($livro['disponiveis']) ?> disponível(is) de <?= e($livro['exemplares']) ?></dd>
        </dl>

        <h2>Sinopse</h2>
        <p><?= e($livro['sinopse']) ?></p>

        <?php if ($livro['disponiveis'] > 0): ?>
            <p><a class="botao" href="<?= e(url('reservar.php?livro=' . $livro['id'])) ?>">Reservar este livro</a></p>
        <?php else: ?>
            <p class="alerta alerta--aviso">Todos os exemplares estão reservados no momento.</p>
        <?php endif; ?>
    </div>
</article>

<?php require __DIR__ . '/../src/partes/rodape.php'; ?>
