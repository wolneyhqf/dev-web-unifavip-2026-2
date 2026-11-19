<?php
/**
 * Salva um livro: INSERT (cadastro) ou UPDATE (edição).
 * Apostila PHP 4.4 — Create e Update: validar TUDO antes de gravar.
 */

require __DIR__ . '/../../src/iniciar.php';
exigirLogin();
exigirPost();
verificarCsrf();

// --- Normalização ----------------------------------------------------------
$livro = [
    'id'             => textoPost('id'),
    'titulo'         => textoPost('titulo'),
    'autor'          => textoPost('autor'),
    'genero'         => textoPost('genero'),
    'ano_publicacao' => textoPost('ano_publicacao'),
    'sinopse'        => textoPost('sinopse'),
    'exemplares'     => textoPost('exemplares'),
];

// --- Validação --------------------------------------------------------------
$erros = [];

$id = null;
if ($livro['id'] !== '') {
    $id = inteiroPositivo($livro['id']);
    $atual = $id === null ? null : buscarLivro($id);
    if ($atual === null) {
        paginaDeErro(404, 'Livro não encontrado', 'O livro que você tentou editar não existe.');
    }
}

if (mb_strlen($livro['titulo']) < 2 || mb_strlen($livro['titulo']) > 150) {
    $erros['titulo'] = 'O título deve ter de 2 a 150 caracteres.';
}
if (mb_strlen($livro['autor']) < 2 || mb_strlen($livro['autor']) > 120) {
    $erros['autor'] = 'O nome do autor deve ter de 2 a 120 caracteres.';
}
if (!array_key_exists($livro['genero'], GENEROS)) {
    $erros['genero'] = 'Escolha um gênero da lista.';
}

$ano = filter_var($livro['ano_publicacao'], FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1450, 'max_range' => (int) date('Y')]]);
if ($ano === false) {
    $erros['ano_publicacao'] = 'Informe um ano entre 1450 e ' . date('Y') . '.';
}

$exemplares = inteiroPositivo($livro['exemplares']);
if ($exemplares === null || $exemplares > 999) {
    $erros['exemplares'] = 'Informe um número de exemplares entre 1 e 999.';
} elseif ($id !== null && $exemplares < $atual['ocupados']) {
    // Regra de negócio: não dá para ter menos exemplares do que livros já reservados.
    $erros['exemplares'] = 'Este livro tem ' . $atual['ocupados'] . ' exemplar(es) reservado(s) ou emprestado(s); '
                         . 'o total não pode ser menor que isso.';
}

if (mb_strlen($livro['sinopse']) > 600) {
    $erros['sinopse'] = 'A sinopse deve ter no máximo 600 caracteres.';
}

// --- Erro: reexibe o formulário com status 422 ----------------------------
if ($erros !== []) {
    http_response_code(422);
    $tituloPagina = $id === null ? 'Cadastrar livro' : 'Editar livro';
    $paginaAtual  = 'admin';
    require __DIR__ . '/../../src/partes/topo.php';
    echo '<h1>' . e($tituloPagina) . '</h1>';
    require __DIR__ . '/../../src/partes/form-livro.php';
    require __DIR__ . '/../../src/partes/rodape.php';
    exit;
}

// --- Gravação (prepared statements) --------------------------------------
$valores = [
    'titulo'     => $livro['titulo'],
    'autor'      => $livro['autor'],
    'genero'     => $livro['genero'],
    'ano'        => $ano,
    'sinopse'    => $livro['sinopse'],
    'exemplares' => $exemplares,
];

if ($id === null) {
    $stmt = banco()->prepare('
        INSERT INTO livros (titulo, autor, genero, ano_publicacao, sinopse, exemplares)
        VALUES (:titulo, :autor, :genero, :ano, :sinopse, :exemplares)
    ');
    $stmt->execute($valores);
    definirFlash('sucesso', 'Livro "' . $livro['titulo'] . '" cadastrado.');
} else {
    $stmt = banco()->prepare('
        UPDATE livros
           SET titulo = :titulo, autor = :autor, genero = :genero,
               ano_publicacao = :ano, sinopse = :sinopse, exemplares = :exemplares
         WHERE id = :id
    ');
    $stmt->execute($valores + ['id' => $id]);
    definirFlash('sucesso', 'Livro "' . $livro['titulo'] . '" atualizado.');
}

// Post-Redirect-Get
redirecionar('admin/index.php');
