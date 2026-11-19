<?php
/**
 * Formulário de reserva — usado por reservar.php (primeira exibição) e por
 * processar-reserva.php (reexibição com erros, mantendo o que foi digitado).
 *
 * Variáveis esperadas:
 *   $dados  array com os valores dos campos
 *   $erros  array campo => mensagem (vazio na primeira exibição)
 *   $livros lista de livros com disponibilidade
 *
 * Apostila 02 (seção 10) — formulários: label, fieldset/legend, tipos de input.
 * Apostila PHP 2.1 — o atributo "name" é o que chega em $_POST; o "id" serve
 * apenas para o label, o CSS e o JavaScript.
 */

$hoje   = new DateTimeImmutable('today');
$limite = $hoje->modify('+' . DIAS_MAXIMOS_RESERVA . ' days');

/** Atributos de acessibilidade de um campo que pode ter erro. */
$atributosErro = function (string $campo) use ($erros): string {
    return isset($erros[$campo])
        ? 'aria-invalid="true" aria-describedby="erro-' . $campo . '"'
        : 'aria-describedby="erro-' . $campo . '"';
};
/** Parágrafo da mensagem de erro (sempre existe, para o JS poder preenchê-lo). */
$mensagemErro = function (string $campo) use ($erros): string {
    return '<p class="erro-campo" id="erro-' . $campo . '">' . e($erros[$campo] ?? '') . '</p>';
};
?>

<?php if ($erros !== []): ?>
    <div class="alerta alerta--erro" role="alert">
        <p><strong>Não foi possível registrar a reserva.</strong> Corrija os itens abaixo:</p>
        <ul>
            <?php foreach ($erros as $mensagem): ?>
                <li><?= e($mensagem) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form id="form-reserva" class="formulario" action="<?= e(url('processar-reserva.php')) ?>" method="post">
    <?= campoCsrf() ?>

    <fieldset>
        <legend>Seus dados</legend>

        <div class="campo">
            <label for="nome">Nome completo</label>
            <input id="nome" name="nome" type="text" required minlength="2" maxlength="120"
                   autocomplete="name" value="<?= e($dados['nome']) ?>" <?= $atributosErro('nome') ?>>
            <?= $mensagemErro('nome') ?>
        </div>

        <div class="campo">
            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" required maxlength="190"
                   autocomplete="email" value="<?= e($dados['email']) ?>" <?= $atributosErro('email') ?>>
            <?= $mensagemErro('email') ?>
        </div>
    </fieldset>

    <fieldset>
        <legend>Reserva</legend>

        <div class="campo">
            <label for="livro_id">Livro</label>
            <select id="livro_id" name="livro_id" required <?= $atributosErro('livro_id') ?>>
                <option value="">Selecione um livro</option>
                <?php foreach ($livros as $livro): ?>
                    <option value="<?= e($livro['id']) ?>"
                        <?= (string) $livro['id'] === (string) $dados['livro_id'] ? 'selected' : '' ?>
                        <?= $livro['disponiveis'] < 1 ? 'disabled' : '' ?>>
                        <?= e($livro['titulo']) ?> — <?= e($livro['autor']) ?>
                        <?= $livro['disponiveis'] < 1 ? '(esgotado)' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?= $mensagemErro('livro_id') ?>
        </div>

        <div class="campo">
            <label for="data_retirada">Data de retirada</label>
            <input id="data_retirada" name="data_retirada" type="date" required
                   min="<?= $hoje->format('Y-m-d') ?>" max="<?= $limite->format('Y-m-d') ?>"
                   value="<?= e($dados['data_retirada']) ?>" <?= $atributosErro('data_retirada') ?>>
            <p class="dica">Entre hoje e <?= $limite->format('d/m/Y') ?>.</p>
            <?= $mensagemErro('data_retirada') ?>
        </div>

        <!-- Radio: todos com o MESMO name; só o marcado é enviado (Apostila PHP 2.5) -->
        <fieldset class="grupo-opcoes" aria-describedby="erro-periodo">
            <legend>Período de retirada</legend>
            <?php foreach (PERIODOS as $valor => $rotulo): ?>
                <label class="opcao">
                    <input type="radio" name="periodo" value="<?= e($valor) ?>" required
                        <?= $dados['periodo'] === $valor ? 'checked' : '' ?>>
                    <?= e($rotulo) ?>
                </label>
            <?php endforeach; ?>
            <?= $mensagemErro('periodo') ?>
        </fieldset>
    </fieldset>

    <fieldset>
        <legend>Preferências (opcional)</legend>

        <!-- Checkbox múltiplo: name com [] faz o PHP receber um ARRAY (Apostila PHP 2.5) -->
        <fieldset class="grupo-opcoes grupo-opcoes--colunas" aria-describedby="erro-generos">
            <legend>Quero receber novidades sobre</legend>
            <?php foreach (GENEROS as $valor => $rotulo): ?>
                <label class="opcao">
                    <input type="checkbox" name="generos[]" value="<?= e($valor) ?>"
                        <?= in_array($valor, $dados['generos'], true) ? 'checked' : '' ?>>
                    <?= e($rotulo) ?>
                </label>
            <?php endforeach; ?>
            <?= $mensagemErro('generos') ?>
        </fieldset>

        <div class="campo">
            <label for="observacoes">Observações</label>
            <textarea id="observacoes" name="observacoes" rows="4" maxlength="300"
                      <?= $atributosErro('observacoes') ?>><?= e($dados['observacoes']) ?></textarea>
            <!-- Atualizado pelo JavaScript a cada tecla (evento "input") -->
            <p class="dica" id="contador-observacoes" aria-live="polite">Até 300 caracteres.</p>
            <?= $mensagemErro('observacoes') ?>
        </div>
    </fieldset>

    <button type="submit" class="botao">Confirmar reserva</button>
</form>
