<?php
/**
 * Topo compartilhado (Apostila PHP 3.3).
 * Cada página define $tituloPagina e $paginaAtual ANTES de incluir este arquivo.
 */
$paginas = [
    'index.php'          => 'Início',
    'acervo.php'         => 'Acervo',
    'reservar.php'       => 'Reservar',
    'minhas-reservas.php' => 'Minhas reservas',
];
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($tituloPagina) ?> | Biblioteca do Bairro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body id="topo">
    <header>
        <h1>Biblioteca do Bairro</h1>
        <nav aria-label="Navegação principal">
            <ul>
                <?php foreach ($paginas as $arquivo => $rotulo): ?>
                    <li>
                        <a href="<?= e($arquivo) ?>" <?= $arquivo === $paginaAtual ? 'aria-current="page"' : '' ?>><?= e($rotulo) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </header>
    <main>
