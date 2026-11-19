<?php
/**
 * Etapa 05 — dados e funções compartilhadas (Apostila PHP, Módulos 1 a 3).
 *
 * Ainda NÃO há banco de dados: o acervo é um array escrito aqui mesmo.
 * Na versão final (pasta public/ na raiz do projeto) estes dados vêm do SQLite.
 */

declare(strict_types=1);

date_default_timezone_set('America/Sao_Paulo');

// Lista permitida de gêneros: chave = valor enviado; valor = rótulo exibido.
const GENEROS = [
    'ficcao'     => 'Ficção',
    'romance'    => 'Romance',
    'fantasia'   => 'Fantasia',
    'infantil'   => 'Infantil',
    'tecnologia' => 'Tecnologia',
];

const PERIODOS = [
    'manha' => 'Manhã',
    'tarde' => 'Tarde',
    'noite' => 'Noite',
];

/**
 * Array associativo indexado pelo id do livro.
 * Cada livro é outro array associativo (Apostila PHP 1.5).
 */
function livros(): array
{
    return [
        1  => ['titulo' => 'Dom Casmurro', 'autor' => 'Machado de Assis', 'genero' => 'romance', 'exemplares' => 3, 'reservados' => 2],
        3  => ['titulo' => 'O Alienista', 'autor' => 'Machado de Assis', 'genero' => 'ficcao', 'exemplares' => 2, 'reservados' => 0],
        4  => ['titulo' => 'Triste Fim de Policarpo Quaresma', 'autor' => 'Lima Barreto', 'genero' => 'romance', 'exemplares' => 2, 'reservados' => 0],
        7  => ['titulo' => 'Alice no País das Maravilhas', 'autor' => 'Lewis Carroll', 'genero' => 'fantasia', 'exemplares' => 2, 'reservados' => 0],
        9  => ['titulo' => 'O Pequeno Príncipe', 'autor' => 'Antoine de Saint-Exupéry', 'genero' => 'infantil', 'exemplares' => 2, 'reservados' => 2],
        11 => ['titulo' => 'Código Limpo', 'autor' => 'Robert C. Martin', 'genero' => 'tecnologia', 'exemplares' => 1, 'reservados' => 0],
    ];
}

/** Escape de saída: TODO texto que vai para o HTML passa por aqui (evita XSS). */
function e(string|int $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function disponiveis(array $livro): int
{
    return max(0, $livro['exemplares'] - $livro['reservados']);
}

/** Confere se a data existe de verdade (rejeita 2026-02-31). */
function dataValida(string $data): bool
{
    $objeto = DateTimeImmutable::createFromFormat('!Y-m-d', $data);
    return $objeto !== false && $objeto->format('Y-m-d') === $data;
}
