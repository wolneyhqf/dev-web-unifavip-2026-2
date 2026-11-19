<?php
/**
 * Muda a situação de uma reserva (retirar, devolver ou cancelar).
 * Apostila PHP 4.4 — Update com validação do id e dos novos dados.
 */

require __DIR__ . '/../../src/iniciar.php';
exigirLogin();
exigirPost();
verificarCsrf();

// Cada ação só vale a partir de uma situação específica: de => para.
$transicoes = [
    'retirar'  => ['ativa', 'retirada'],
    'devolver' => ['retirada', 'devolvida'],
    'cancelar' => ['ativa', 'cancelada'],
];

$id   = inteiroPositivo($_POST['id'] ?? null);
$acao = textoPost('acao');

if ($id === null || !array_key_exists($acao, $transicoes)) {
    paginaDeErro(422, 'Ação inválida', 'A ação solicitada não é reconhecida.');
}

[$de, $para] = $transicoes[$acao];

// O "AND situacao = :de" garante que a mudança só acontece se a reserva ainda
// estiver na situação esperada (ex.: não dá para devolver o que foi cancelado).
$stmt = banco()->prepare('UPDATE reservas SET situacao = :para WHERE id = :id AND situacao = :de');
$stmt->execute(['para' => $para, 'id' => $id, 'de' => $de]);

if ($stmt->rowCount() === 0) {
    // Nenhuma linha alterada: id inexistente ou situação diferente da esperada.
    paginaDeErro(409, 'Não foi possível atualizar',
        'A reserva não existe ou já está em outra situação. Atualize a lista e tente novamente.');
}

definirFlash('sucesso', 'Reserva atualizada: ' . mb_strtolower(SITUACOES[$para]) . '.');
redirecionar('admin/reservas.php');
