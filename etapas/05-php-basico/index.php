<?php
/**
 * Etapa 05 — página inicial.
 * Apostila PHP 1.3 a 1.6: variáveis, operadores, laços e PHP misturado com HTML.
 *
 * Abra com o servidor embutido (NÃO com duplo clique):
 *     php -S localhost:8000 -t etapas/05-php-basico
 * e acesse http://localhost:8000
 * Depois use Ctrl+U no navegador: você verá só HTML, nenhum código PHP.
 */

require_once __DIR__ . '/dados.php';

// Calculando totais com um laço foreach
$totalExemplares  = 0;
$totalDisponiveis = 0;
foreach (livros() as $livro) {
    $totalExemplares  += $livro['exemplares'];
    $totalDisponiveis += disponiveis($livro);
}

$hora = (int) date('H');
if ($hora < 12) {
    $saudacao = 'Bom dia';
} elseif ($hora < 18) {
    $saudacao = 'Boa tarde';
} else {
    $saudacao = 'Boa noite';
}

$tituloPagina = 'Início';
$paginaAtual  = 'index.php';
require __DIR__ . '/partes/topo.php';
?>

<section>
    <h2><?= e($saudacao) ?>! Bem-vindo(a) à biblioteca.</h2>
    <p>
        Temos <strong><?= count(livros()) ?></strong> títulos e
        <strong><?= $totalExemplares ?></strong> exemplares,
        dos quais <strong><?= $totalDisponiveis ?></strong> estão disponíveis agora.
    </p>
    <p><a href="acervo.php">Ver o acervo</a></p>
</section>

<section>
    <h2>Gêneros do acervo</h2>
    <ul>
        <?php foreach (GENEROS as $codigo => $nome): ?>
            <!-- Link com parâmetro GET: acervo.php?genero=romance -->
            <li><a href="acervo.php?genero=<?= e($codigo) ?>"><?= e($nome) ?></a></li>
        <?php endforeach; ?>
    </ul>
</section>

<?php require __DIR__ . '/partes/rodape.php'; ?>
