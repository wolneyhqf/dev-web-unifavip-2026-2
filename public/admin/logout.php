<?php
/**
 * Sair da área do bibliotecário.
 * Sair também altera estado (encerra a sessão), por isso é feito por POST.
 */

require __DIR__ . '/../../src/iniciar.php';

exigirPost();
verificarCsrf();

encerrarSessao();
definirFlash('sucesso', 'Você saiu da área do bibliotecário.');
redirecionar('index.php');
