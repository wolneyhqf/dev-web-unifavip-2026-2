<?php
/**
 * Etapa 05 — formulário de reserva processado pelo PHP.
 *
 * A mesma página:
 *   - GET  → mostra o formulário
 *   - POST → normaliza, valida e (se tudo certo) guarda a reserva na SESSÃO
 *            e redireciona (Post-Redirect-Get)
 *
 * Ainda sem banco: as reservas ficam em $_SESSION, ou seja, só existem para
 * este navegador e somem quando a sessão acaba. Na versão final elas vão
 * para o banco SQLite e ficam disponíveis para todos.
 */

require_once __DIR__ . '/dados.php';

// session_start() ANTES de qualquer HTML (envia o cookie PHPSESSID)
session_start();

$livros = livros();
$erros  = [];
$dados  = [
    'nome'          => '',
    'email'         => '',
    'livro'         => (string) ($_GET['livro'] ?? ''),
    'data_retirada' => '',
    'periodo'       => '',
    'generos'       => [],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ---- 1. Normalização ---------------------------------------------------
    $dados['nome']          = trim((string) ($_POST['nome'] ?? ''));
    $dados['email']         = trim((string) ($_POST['email'] ?? ''));
    $dados['livro']         = (string) ($_POST['livro'] ?? '');
    $dados['data_retirada'] = (string) ($_POST['data_retirada'] ?? '');
    $dados['periodo']       = (string) ($_POST['periodo'] ?? '');
    $generos                = $_POST['generos'] ?? []; // checkbox com [] chega como ARRAY

    // ---- 2. Validação (o servidor é quem decide!) -----------------------------
    if (mb_strlen($dados['nome']) < 2 || mb_strlen($dados['nome']) > 120) {
        $erros['nome'] = 'Informe o nome com 2 a 120 caracteres.';
    }

    if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $erros['email'] = 'Informe um e-mail válido.';
    }

    $idLivro = filter_var($dados['livro'], FILTER_VALIDATE_INT);
    if ($idLivro === false || !isset($livros[$idLivro])) {
        $erros['livro'] = 'Escolha um livro da lista.';
    } elseif (disponiveis($livros[$idLivro]) === 0) {
        $erros['livro'] = 'Este livro está esgotado.';
    }

    if (!dataValida($dados['data_retirada']) || $dados['data_retirada'] < date('Y-m-d')) {
        $erros['data_retirada'] = 'Informe uma data válida, de hoje em diante.';
    }

    // in_array com true = comparação estrita
    if (!in_array($dados['periodo'], array_keys(PERIODOS), true)) {
        $erros['periodo'] = 'Escolha um período.';
    }

    if (!is_array($generos)) {
        $erros['generos'] = 'Formato inválido.';
    } else {
        foreach ($generos as $genero) {
            if (!is_string($genero) || !array_key_exists($genero, GENEROS)) {
                $erros['generos'] = 'Gênero inválido.';
                break;
            }
        }
        if (!isset($erros['generos'])) {
            $dados['generos'] = $generos; // mantém marcados ao reexibir o formulário
        }
    }

    // ---- 3. Tudo certo: grava na sessão e redireciona (PRG) -------------------
    if ($erros === []) {
        $_SESSION['reservas'][] = [
            'titulo'  => $livros[$idLivro]['titulo'],
            'nome'    => $dados['nome'],
            'data'    => $dados['data_retirada'],
            'periodo' => $dados['periodo'],
        ];
        $_SESSION['mensagem'] = 'Reserva de "' . $livros[$idLivro]['titulo'] . '" registrada!';

        header('Location: minhas-reservas.php'); // 302: o navegador fará um GET nesta página
        exit;                                     // sem exit, o resto do script continuaria
    }

    http_response_code(422); // dados inválidos
}

$tituloPagina = 'Reservar';
$paginaAtual  = 'reservar.php';
require __DIR__ . '/partes/topo.php';
?>

<h2>Reservar livro</h2>

<?php if ($erros !== []): ?>
    <p class="erro" role="alert">Corrija os campos indicados abaixo.</p>
<?php endif; ?>

<!-- action aponta para esta mesma página. Sem required no HTML de propósito:
     assim você consegue testar a validação do SERVIDOR. -->
<form action="reservar.php" method="post">
    <p>
        <label for="nome">Nome</label>
        <input id="nome" name="nome" type="text" value="<?= e($dados['nome']) ?>">
        <span class="erro"><?= e($erros['nome'] ?? '') ?></span>
    </p>

    <p>
        <label for="email">E-mail</label>
        <input id="email" name="email" type="text" value="<?= e($dados['email']) ?>">
        <span class="erro"><?= e($erros['email'] ?? '') ?></span>
    </p>

    <p>
        <label for="livro">Livro</label>
        <select id="livro" name="livro">
            <option value="">Selecione</option>
            <?php foreach ($livros as $id => $livro): ?>
                <option value="<?= $id ?>" <?= (string) $id === $dados['livro'] ? 'selected' : '' ?>>
                    <?= e($livro['titulo']) ?><?= disponiveis($livro) === 0 ? ' (esgotado)' : '' ?>
                </option>
            <?php endforeach; ?>
        </select>
        <span class="erro"><?= e($erros['livro'] ?? '') ?></span>
    </p>

    <p>
        <label for="data_retirada">Data de retirada</label>
        <input id="data_retirada" name="data_retirada" type="date" value="<?= e($dados['data_retirada']) ?>">
        <span class="erro"><?= e($erros['data_retirada'] ?? '') ?></span>
    </p>

    <fieldset>
        <legend>Período</legend>
        <?php foreach (PERIODOS as $valor => $rotulo): ?>
            <label>
                <input type="radio" name="periodo" value="<?= e($valor) ?>" <?= $dados['periodo'] === $valor ? 'checked' : '' ?>>
                <?= e($rotulo) ?>
            </label>
        <?php endforeach; ?>
        <span class="erro"><?= e($erros['periodo'] ?? '') ?></span>
    </fieldset>

    <fieldset>
        <legend>Novidades sobre (opcional)</legend>
        <?php foreach (GENEROS as $valor => $rotulo): ?>
            <label>
                <input type="checkbox" name="generos[]" value="<?= e($valor) ?>"
                    <?= in_array($valor, $dados['generos'], true) ? 'checked' : '' ?>>
                <?= e($rotulo) ?>
            </label>
        <?php endforeach; ?>
        <span class="erro"><?= e($erros['generos'] ?? '') ?></span>
    </fieldset>

    <button type="submit">Reservar</button>
</form>

<?php require __DIR__ . '/partes/rodape.php'; ?>
