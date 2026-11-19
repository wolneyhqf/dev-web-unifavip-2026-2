<?php
/**
 * Página inicial.
 * Apostila PHP 1.6 — intercalação de PHP com HTML: o PHP busca os dados
 * e o HTML os apresenta; toda saída passa por e() (htmlspecialchars).
 */

require __DIR__ . '/../src/iniciar.php';

// Totais do acervo: uma consulta fixa, sem parâmetros → query() é suficiente.
$totais = banco()->query("
    SELECT (SELECT COUNT(*) FROM livros)                                         AS titulos,
           (SELECT COALESCE(SUM(exemplares), 0) FROM livros)                     AS exemplares,
           (SELECT COUNT(*) FROM reservas WHERE situacao IN ('ativa','retirada')) AS ocupados
")->fetch();
$disponiveis = $totais['exemplares'] - $totais['ocupados'];

// Destaques: os 3 livros com mais exemplares disponíveis.
$destaques = banco()->query(
    SQL_LIVROS_COM_DISPONIBILIDADE . ' GROUP BY l.id ORDER BY disponiveis DESC, l.titulo LIMIT 3'
)->fetchAll();

$tituloPagina = 'Início';
$paginaAtual  = 'inicio';
require __DIR__ . '/../src/partes/topo.php';
?>

<section class="destaque-inicial" aria-labelledby="titulo-inicial">
    <div class="destaque-inicial__texto">
        <h1 id="titulo-inicial">Leitura perto de casa</h1>
        <p>
            Consulte o acervo da <strong>Biblioteca do Bairro</strong>, reserve o livro pela internet
            e retire no balcão no dia e no período que preferir. É gratuito.
        </p>
        <p class="acoes">
            <a class="botao" href="<?= e(url('acervo.php')) ?>">Ver o acervo</a>
            <a class="botao botao--secundario" href="<?= e(url('reservar.php')) ?>">Fazer uma reserva</a>
        </p>
    </div>
    <figure class="destaque-inicial__figura">
        <img src="<?= e(url('img/estante.svg')) ?>"
             alt="Ilustração de uma estante com livros coloridos"
             width="480" height="320">
        <figcaption>Nosso acervo cresce com doações da comunidade.</figcaption>
    </figure>
</section>

<section aria-labelledby="titulo-numeros">
    <h2 id="titulo-numeros">O acervo em números</h2>
    <!-- <dl>: lista de descrição (Apostila 02, 5.3) — termo e valor -->
    <dl class="numeros">
        <div>
            <dt>Títulos</dt>
            <dd><?= e($totais['titulos']) ?></dd>
        </div>
        <div>
            <dt>Exemplares</dt>
            <dd><?= e($totais['exemplares']) ?></dd>
        </div>
        <div>
            <dt>Disponíveis agora</dt>
            <dd><?= e($disponiveis) ?></dd>
        </div>
    </dl>
</section>

<section aria-labelledby="titulo-destaques">
    <h2 id="titulo-destaques">Disponíveis para reserva</h2>
    <div class="grade-cartoes">
        <?php foreach ($destaques as $livro): ?>
            <article class="cartao-livro">
                <div class="capa capa--<?= e($livro['genero']) ?>" aria-hidden="true">
                    <span><?= e($livro['titulo']) ?></span>
                </div>
                <h3><a href="<?= e(url('livro.php?id=' . $livro['id'])) ?>"><?= e($livro['titulo']) ?></a></h3>
                <p class="cartao-livro__autor"><?= e($livro['autor']) ?></p>
                <p class="selo"><?= e($livro['disponiveis']) ?> disponível(is)</p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="duas-colunas" aria-label="Informações da biblioteca">
    <article>
        <h2>Como funciona a reserva</h2>
        <!-- <ol>: a ordem dos passos importa (Apostila 02, 5.2) -->
        <ol>
            <li>Encontre o livro no <a href="<?= e(url('acervo.php')) ?>">acervo</a>.</li>
            <li>Preencha o formulário de reserva com nome, e-mail e data de retirada.</li>
            <li>Retire o livro no balcão em até <?= DIAS_MAXIMOS_RESERVA ?> dias.</li>
        </ol>
    </article>
    <article>
        <h2>Horário de atendimento</h2>
        <ul>
            <?php foreach (PERIODOS as $rotulo): ?>
                <li><?= e($rotulo) ?></li>
            <?php endforeach; ?>
        </ul>
        <p>De segunda a sábado, exceto feriados.</p>
    </article>
</section>

<?php require __DIR__ . '/../src/partes/rodape.php'; ?>
