<?php
/**
 * Arquivo de inicialização: TODA página de public/ começa com
 *     require __DIR__ . '/../src/iniciar.php';
 *
 * Apostila PHP 3.1 e 3.2 — require_once + __DIR__ geram caminhos estáveis,
 * que funcionam não importa de qual pasta o script foi chamado.
 *
 * Os arquivos de src/ ficam FORA da pasta public/, então o navegador não
 * consegue acessá-los por URL: só o PHP os enxerga pelo sistema de arquivos.
 */

declare(strict_types=1);

// Em desenvolvimento queremos ver todos os erros. Em produção, display_errors = 0.
error_reporting(E_ALL);
ini_set('display_errors', '1');

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/funcoes.php';
require_once __DIR__ . '/auth.php';

// Apostila PHP 3.4 — session_start() precisa vir ANTES de qualquer saída HTML,
// pois envia o cabeçalho Set-Cookie (PHPSESSID).
session_start([
    'cookie_httponly' => true,   // JavaScript não consegue ler o cookie de sessão
    'cookie_samesite' => 'Lax',  // o cookie não acompanha POSTs vindos de outros sites
    'use_strict_mode' => true,   // recusa IDs de sessão inventados pelo cliente
]);
