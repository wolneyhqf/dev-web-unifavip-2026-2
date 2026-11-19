<?php
/**
 * Topo compartilhado por todas as páginas.
 * Apostila PHP 3.3 — separação de responsabilidades: o esqueleto visual fica
 * em um único arquivo; cada página só define suas variáveis de contexto:
 *
 *   $tituloPagina  texto do <title> (obrigatório)
 *   $paginaAtual   'inicio' | 'acervo' | 'reservar' | 'admin' (marca o menu)
 *   $scripts       lista de arquivos JS da página, ex.: ['js/acervo.js']
 *
 * Apostila 02 (seções 3 e 8) — estrutura mínima do HTML5 e elementos semânticos.
 */

$paginaAtual = $paginaAtual ?? '';
$scripts     = $scripts ?? [];

// Preferência de tema vem de um COOKIE (fica no navegador do visitante).
// Como o visitante pode editar cookies, conferimos contra uma lista permitida.
$tema = in_array($_COOKIE['tema'] ?? '', ['claro', 'escuro'], true) ? $_COOKIE['tema'] : 'claro';

$menu = [
    'inicio'   => ['index.php', 'Início'],
    'acervo'   => ['acervo.php', 'Acervo'],
    'reservar' => ['reservar.php', 'Reservar'],
    'admin'    => ['admin/index.php', 'Área do bibliotecário'],
];
$flash = obterFlash();
?>
<!doctype html>
<html lang="pt-BR" data-tema="<?= e($tema) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Biblioteca do Bairro: consulte o acervo e reserve livros para retirada.">
    <title><?= e($tituloPagina) ?> | Biblioteca do Bairro</title>
    <link rel="icon" href="<?= e(url('img/logo.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(url('css/style.css')) ?>">
    <!-- defer: o script roda depois que o HTML foi todo analisado (Apostila 04, seção 1) -->
    <script src="<?= e(url('js/tema.js')) ?>" defer></script>
    <?php foreach ($scripts as $script): ?>
        <script src="<?= e(url($script)) ?>" defer></script>
    <?php endforeach; ?>
</head>
<body>
    <a class="pular-conteudo" href="#conteudo">Pular para o conteúdo</a>

    <header class="cabecalho">
        <div class="cabecalho__interno">
            <a class="marca" href="<?= e(url('index.php')) ?>">
                <img src="<?= e(url('img/logo.svg')) ?>" alt="" width="40" height="40">
                <span>Biblioteca do Bairro</span>
            </a>

            <nav class="menu" aria-label="Navegação principal">
                <ul>
                    <?php foreach ($menu as $chave => [$link, $rotulo]): ?>
                        <li>
                            <a href="<?= e(url($link)) ?>"
                               <?= $chave === $paginaAtual ? 'aria-current="page"' : '' ?>><?= e($rotulo) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <!-- Funciona SEM JavaScript (envia o formulário para tema.php).
                 Com JavaScript, js/tema.js troca o tema na hora, sem recarregar. -->
            <form class="form-tema" action="<?= e(url('tema.php')) ?>" method="post">
                <?= campoCsrf() ?>
                <input type="hidden" name="tema" value="<?= $tema === 'escuro' ? 'claro' : 'escuro' ?>">
                <input type="hidden" name="voltar" value="<?= e($_SERVER['REQUEST_URI'] ?? '') ?>">
                <button type="submit" class="botao-tema" aria-pressed="<?= $tema === 'escuro' ? 'true' : 'false' ?>">
                    <span aria-hidden="true">◐</span> Tema escuro
                </button>
            </form>
        </div>
    </header>

    <?php if (usuarioLogado() !== null && $paginaAtual === 'admin'): ?>
        <nav class="menu-admin" aria-label="Área do bibliotecário">
            <ul>
                <li><a href="<?= e(url('admin/index.php')) ?>">Livros</a></li>
                <li><a href="<?= e(url('admin/livro-form.php')) ?>">Novo livro</a></li>
                <li><a href="<?= e(url('admin/reservas.php')) ?>">Reservas</a></li>
            </ul>
            <form action="<?= e(url('admin/logout.php')) ?>" method="post">
                <?= campoCsrf() ?>
                <span>Olá, <strong><?= e(usuarioLogado()) ?></strong></span>
                <button type="submit" class="botao botao--pequeno botao--secundario">Sair</button>
            </form>
        </nav>
    <?php endif; ?>

    <main id="conteudo" class="conteudo">
        <?php if ($flash !== null): ?>
            <!-- role="status" faz leitores de tela anunciarem a mensagem -->
            <p class="alerta alerta--<?= e($flash['tipo']) ?>" role="status"><?= e($flash['mensagem']) ?></p>
        <?php endif; ?>
