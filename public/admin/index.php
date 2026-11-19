<?php
/**
 * Painel do bibliotecário: lista de livros com ações de editar e excluir.
 * Apostila PHP 4.4 — CRUD: esta é a parte "Read" (listagem).
 */

require __DIR__ . '/../../src/iniciar.php';
exigirLogin();

$livros = banco()->query(SQL_LIVROS_COM_DISPONIBILIDADE . ' GROUP BY l.id ORDER BY l.titulo')->fetchAll();

$tituloPagina = 'Livros';
$paginaAtual  = 'admin';
$scripts      = ['js/admin.js'];
require __DIR__ . '/../../src/partes/topo.php';
?>

<div class="titulo-com-acao">
    <h1>Livros do acervo</h1>
    <a class="botao" href="<?= e(url('admin/livro-form.php')) ?>">Cadastrar livro</a>
</div>

<div class="tabela-rolavel">
    <table class="tabela">
        <caption>Livros cadastrados (<?= count($livros) ?>)</caption>
        <thead>
            <tr>
                <th scope="col">Título</th>
                <th scope="col">Gênero</th>
                <th scope="col">Exemplares</th>
                <th scope="col">Ocupados</th>
                <th scope="col">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($livros as $livro): ?>
                <tr>
                    <th scope="row">
                        <?= e($livro['titulo']) ?>
                        <span class="tabela__detalhe"><?= e($livro['autor']) ?></span>
                    </th>
                    <td><?= e(GENEROS[$livro['genero']]) ?></td>
                    <td><?= e($livro['exemplares']) ?></td>
                    <td>
                        <a href="<?= e(url('admin/reservas.php?livro_id=' . $livro['id'])) ?>">
                            <?= e($livro['ocupados']) ?><span class="visualmente-oculto"> — ver reservas de <?= e($livro['titulo']) ?></span>
                        </a>
                    </td>
                    <td>
                        <div class="acoes-linha">
                            <a class="botao botao--pequeno botao--secundario"
                               href="<?= e(url('admin/livro-form.php?id=' . $livro['id'])) ?>">
                                Editar<span class="visualmente-oculto"> <?= e($livro['titulo']) ?></span>
                            </a>
                            <!-- Exclusão SEMPRE por POST, nunca por link GET (Apostila PHP 4.4).
                                 data-confirmar é lido por js/admin.js para pedir confirmação. -->
                            <form action="<?= e(url('admin/excluir-livro.php')) ?>" method="post"
                                  data-confirmar="Excluir o livro &quot;<?= e($livro['titulo']) ?>&quot;?">
                                <?= campoCsrf() ?>
                                <input type="hidden" name="id" value="<?= e($livro['id']) ?>">
                                <button type="submit" class="botao botao--pequeno botao--perigo">
                                    Excluir<span class="visualmente-oculto"> <?= e($livro['titulo']) ?></span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../src/partes/rodape.php'; ?>
