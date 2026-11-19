<?php
/**
 * Troca do tema claro/escuro SEM JavaScript.
 *
 * Exemplo de COOKIE x SESSÃO (Apostila PHP 3.5):
 *  - O tema é uma preferência simples e não sensível → fica em um COOKIE,
 *    guardado no navegador do visitante (ele pode ver e editar no DevTools).
 *  - Já "quem está logado" e as mensagens flash ficam na SESSÃO, no servidor.
 */

require __DIR__ . '/../src/iniciar.php';

exigirPost();
verificarCsrf();

$tema = textoPost('tema');
if (!in_array($tema, ['claro', 'escuro'], true)) {
    $tema = 'claro';
}

// setcookie também envia um cabeçalho (Set-Cookie): precisa vir antes de qualquer HTML.
setcookie('tema', $tema, [
    'expires'  => time() + 60 * 60 * 24 * 365, // 1 ano
    'path'     => '/',
    'samesite' => 'Lax',
    // httponly NÃO é usado aqui de propósito: js/tema.js também lê e grava este cookie.
]);

// Volta para a página onde a pessoa estava. Só aceitamos caminhos do próprio site
// (começando com "/", mas não com "//" ou "/\", que levariam a outro domínio).
$voltar = textoPost('voltar');
if (!str_starts_with($voltar, '/') || str_starts_with($voltar, '//') || str_starts_with($voltar, '/\\')) {
    $voltar = url('index.php');
}
header('Location: ' . $voltar);
exit;
