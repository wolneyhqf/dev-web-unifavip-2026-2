<?php
/**
 * Confirmação da reserva — destino do redirecionamento (o "G" do PRG).
 * Esta página é acessada por GET: apertar F5 aqui NÃO reenvia o formulário.
 */

require __DIR__ . '/../src/iniciar.php';

$id = $_SESSION['ultima_reserva'] ?? null;
if (!is_int($id)) {
    redirecionar('reservar.php');
}

// JOIN (Apostila PHP 4.5): dados da reserva + título do livro em uma única consulta.
$stmt = banco()->prepare('
    SELECT r.nome, r.email, r.data_retirada, r.periodo, l.titulo, l.autor
      FROM reservas AS r
      JOIN livros   AS l ON l.id = r.livro_id
     WHERE r.id = ?
');
$stmt->execute([$id]);
$reserva = $stmt->fetch();

if ($reserva === false) {
    redirecionar('reservar.php');
}

$tituloPagina = 'Reserva confirmada';
require __DIR__ . '/../src/partes/topo.php';
?>

<section class="cartao" aria-labelledby="titulo-confirmacao">
    <h1 id="titulo-confirmacao">Reserva confirmada</h1>
    <p>Obrigado, <strong><?= e($reserva['nome']) ?></strong>! Seu livro ficará separado no balcão.</p>

    <dl class="ficha">
        <dt>Livro</dt>
        <dd><?= e($reserva['titulo']) ?> — <?= e($reserva['autor']) ?></dd>
        <dt>Retirada</dt>
        <dd>
            <time datetime="<?= e($reserva['data_retirada']) ?>"><?= e(formatarData($reserva['data_retirada'])) ?></time>,
            <?= e(PERIODOS[$reserva['periodo']]) ?>
        </dd>
        <dt>E-mail</dt>
        <dd><?= e($reserva['email']) ?></dd>
    </dl>

    <p class="dica">Experimente apertar F5: a página recarrega, mas a reserva não é feita de novo (padrão Post-Redirect-Get).</p>

    <p class="acoes">
        <a class="botao" href="<?= e(url('acervo.php')) ?>">Voltar ao acervo</a>
        <a class="botao botao--secundario" href="<?= e(url('reservar.php')) ?>">Fazer outra reserva</a>
    </p>
</section>

<?php require __DIR__ . '/../src/partes/rodape.php'; ?>
