<?php
/**
 * Formulário de cadastro/edição de livro — usado por admin/livro-form.php
 * e por admin/salvar-livro.php (reexibição com erros).
 *
 * Variáveis esperadas:
 *   $livro  array com os valores dos campos ('id' vazio = cadastro novo)
 *   $erros  array campo => mensagem
 */

$editando  = $livro['id'] !== '';
$anoAtual  = (int) date('Y');
$descricao = fn (string $campo): string => isset($erros[$campo])
    ? 'aria-invalid="true" aria-describedby="erro-' . $campo . '"'
    : '';
$mensagem  = fn (string $campo): string => isset($erros[$campo])
    ? '<p class="erro-campo" id="erro-' . $campo . '">' . e($erros[$campo]) . '</p>'
    : '';
?>

<?php if ($erros !== []): ?>
    <div class="alerta alerta--erro" role="alert">
        <p><strong>Não foi possível salvar.</strong> Corrija os itens destacados.</p>
    </div>
<?php endif; ?>

<form class="formulario" action="<?= e(url('admin/salvar-livro.php')) ?>" method="post">
    <?= campoCsrf() ?>
    <!-- Campo oculto: em branco = INSERT; com número = UPDATE -->
    <input type="hidden" name="id" value="<?= e($livro['id']) ?>">

    <div class="campo">
        <label for="titulo">Título</label>
        <input id="titulo" name="titulo" type="text" required maxlength="150"
               value="<?= e($livro['titulo']) ?>" <?= $descricao('titulo') ?>>
        <?= $mensagem('titulo') ?>
    </div>

    <div class="campo">
        <label for="autor">Autor(a)</label>
        <input id="autor" name="autor" type="text" required maxlength="120"
               value="<?= e($livro['autor']) ?>" <?= $descricao('autor') ?>>
        <?= $mensagem('autor') ?>
    </div>

    <div class="linha-campos">
        <div class="campo">
            <label for="genero">Gênero</label>
            <select id="genero" name="genero" required <?= $descricao('genero') ?>>
                <option value="">Selecione</option>
                <?php foreach (GENEROS as $valor => $rotulo): ?>
                    <option value="<?= e($valor) ?>" <?= $livro['genero'] === $valor ? 'selected' : '' ?>><?= e($rotulo) ?></option>
                <?php endforeach; ?>
            </select>
            <?= $mensagem('genero') ?>
        </div>

        <div class="campo">
            <label for="ano_publicacao">Ano de publicação</label>
            <input id="ano_publicacao" name="ano_publicacao" type="number" required min="1450" max="<?= $anoAtual ?>"
                   value="<?= e($livro['ano_publicacao']) ?>" <?= $descricao('ano_publicacao') ?>>
            <?= $mensagem('ano_publicacao') ?>
        </div>

        <div class="campo">
            <label for="exemplares">Exemplares</label>
            <input id="exemplares" name="exemplares" type="number" required min="1" max="999"
                   value="<?= e($livro['exemplares']) ?>" <?= $descricao('exemplares') ?>>
            <?= $mensagem('exemplares') ?>
        </div>
    </div>

    <div class="campo">
        <label for="sinopse">Sinopse</label>
        <textarea id="sinopse" name="sinopse" rows="4" maxlength="600" <?= $descricao('sinopse') ?>><?= e($livro['sinopse']) ?></textarea>
        <?= $mensagem('sinopse') ?>
    </div>

    <p class="acoes">
        <button type="submit" class="botao"><?= $editando ? 'Salvar alterações' : 'Cadastrar livro' ?></button>
        <a class="botao botao--secundario" href="<?= e(url('admin/index.php')) ?>">Cancelar</a>
    </p>
</form>
