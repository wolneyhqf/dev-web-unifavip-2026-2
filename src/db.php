<?php
/**
 * Conexão com o banco de dados.
 * Apostila PHP, Módulo 4.2 — Conexão centralizada com PDO.
 *
 * Toda a aplicação obtém a conexão chamando banco(). Assim, se um dia o
 * projeto migrar para MySQL, só este arquivo (e o schema) precisam mudar.
 */

declare(strict_types=1);

const CAMINHO_BANCO = __DIR__ . '/../dados/biblioteca.sqlite';

function banco(): PDO
{
    // "static" guarda a conexão entre chamadas: abrimos só UMA conexão por requisição.
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $bancoNovo = !file_exists(CAMINHO_BANCO);
    if ($bancoNovo && !is_dir(dirname(CAMINHO_BANCO))) {
        mkdir(dirname(CAMINHO_BANCO), 0775, true);
    }

    // No MySQL o DSN seria: 'mysql:host=127.0.0.1;dbname=biblioteca;charset=utf8mb4'
    // No SQLite basta o caminho do arquivo (o SQLite já usa UTF-8).
    $pdo = new PDO('sqlite:' . CAMINHO_BANCO, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // erros viram exceções (try/catch)
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // linhas como arrays associativos
        PDO::ATTR_EMULATE_PREPARES   => false,                  // prepared statements nativos
        PDO::ATTR_TIMEOUT            => 5,                      // espera até 5 s se o banco estiver travado
    ]);

    // O SQLite vem com as chaves estrangeiras DESLIGADAS por padrão.
    // Sem esta linha, o ON DELETE RESTRICT do schema seria ignorado!
    $pdo->exec('PRAGMA foreign_keys = ON');

    if ($bancoNovo) {
        criarBanco($pdo);
    }

    return $pdo;
}

/**
 * Cria as tabelas e insere os dados de exemplo (primeira execução).
 */
function criarBanco(PDO $pdo): void
{
    $pdo->exec((string) file_get_contents(__DIR__ . '/../sql/schema.sql'));
    $pdo->exec((string) file_get_contents(__DIR__ . '/../sql/seed.sql'));
}
