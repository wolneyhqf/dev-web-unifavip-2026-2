<?php
/**
 * Formulário de livro.
 *   admin/livro-form.php        → cadastrar novo livro
 *   admin/livro-form.php?id=3   → editar o livro 3
 */

require __DIR__ . '/../../src/iniciar.php';
exigirLogin();

$livro = ['id' => '', 'titulo' => '', 'autor' => '', 'genero' => '',
          'ano_publicacao' => '', 'sinopse' => '', 'exemplares' => '1'];
$erros = [];

if (isset($_GET['id'])) {
    $id = inteiroPositivo($_GET['id']);
    $encontrado = $id === null ? null : buscarLivro($id);
    if ($encontrado === null) {
        paginaDeErro(404, 'Livro não encontrado', 'Não existe livro com esse código.');
    }
    // Converte tudo para texto, como se tivesse vindo do formulário.
    $livro = array_map('strval', array_intersect_key($encontrado, $livro));
}

$editando     = $livro['id'] !== '';
$tituloPagina = $editando ? 'Editar livro' : 'Cadastrar livro';
$paginaAtual  = 'admin';
require __DIR__ . '/../../src/partes/topo.php';
?>

<h1><?= e($tituloPagina) ?></h1>

<?php require __DIR__ . '/../../src/partes/form-livro.php'; ?>

<?php require __DIR__ . '/../../src/partes/rodape.php'; ?>
