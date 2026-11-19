<?php
/**
 * Apaga o banco SQLite e o recria com os dados de exemplo.
 *
 * Uso (na raiz do projeto):
 *     php bin/resetar-banco.php
 *
 * Pare o servidor (php -S) antes, se estiver usando Windows: o arquivo pode estar em uso.
 */

declare(strict_types=1);

require __DIR__ . '/../src/db.php';

foreach (glob(CAMINHO_BANCO . '*') ?: [] as $arquivo) {
    unlink($arquivo);
}

banco(); // como o arquivo não existe mais, banco() cria tabelas e dados de exemplo

$total = banco()->query('SELECT COUNT(*) FROM livros')->fetchColumn();
echo "Banco recriado em " . realpath(CAMINHO_BANCO) . PHP_EOL;
echo "Livros cadastrados: {$total}" . PHP_EOL;
