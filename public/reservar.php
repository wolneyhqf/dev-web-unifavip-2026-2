<?php
/**
 * Formulário de reserva (exibição).
 * O envio vai para processar-reserva.php, que valida e grava.
 *
 * Aceita ?livro=ID para já deixar o livro selecionado (vindo do acervo).
 */

require __DIR__ . '/../src/iniciar.php';

$livros = banco()->query(SQL_LIVROS_COM_DISPONIBILIDADE . ' GROUP BY l.id ORDER BY l.titulo')->fetchAll();

// Valores iniciais dos campos (o formulário começa vazio).
$dados = [
    'nome'          => '',
    'email'         => '',
    'livro_id'      => (string) (inteiroPositivo($_GET['livro'] ?? null) ?? ''),
    'data_retirada' => '',
    'periodo'       => '',
    'generos'       => [],
    'observacoes'   => '',
];
$erros = [];

$tituloPagina = 'Reservar livro';
$paginaAtual  = 'reservar';
$scripts      = ['js/reserva.js'];
require __DIR__ . '/../src/partes/topo.php';
?>

<h1>Reservar livro</h1>
<p>Preencha os dados abaixo. O livro ficará separado no balcão até a data escolhida.</p>

<?php require __DIR__ . '/../src/partes/form-reserva.php'; ?>

<?php require __DIR__ . '/../src/partes/rodape.php'; ?>
