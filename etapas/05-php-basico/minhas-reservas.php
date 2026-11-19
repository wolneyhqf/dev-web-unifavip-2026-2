<?php
/**
 * Etapa 05 — reservas guardadas na SESSÃO (Apostila PHP 3.4 a 3.6).
 *
 * Experimente:
 *  1. Faça uma reserva e aperte F5 aqui: nada é reenviado (PRG).
 *  2. Abra o site em uma janela anônima: a lista estará vazia,
 *     porque cada navegador tem a sua própria sessão.
 *  3. No DevTools (Application → Cookies) veja o cookie PHPSESSID:
 *     o navegador guarda só o IDENTIFICADOR; os dados ficam no servidor.
 */

require_once __DIR__ . '/dados.php';
session_start();

// "Limpar" altera estado → só por POST, seguido de redirecionamento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    unset($_SESSION['reservas']);
    $_SESSION['mensagem'] = 'Lista de reservas apagada.';
    header('Location: minhas-reservas.php');
    exit;
}

// Mensagem "flash": lida uma vez e apagada em seguida
$mensagem = $_SESSION['mensagem'] ?? null;
unset($_SESSION['mensagem']);

$reservas = $_SESSION['reservas'] ?? [];

$tituloPagina = 'Minhas reservas';
$paginaAtual  = 'minhas-reservas.php';
require __DIR__ . '/partes/topo.php';
?>

<h2>Minhas reservas</h2>

<?php if ($mensagem !== null): ?>
    <p id="resultado" role="status"><?= e($mensagem) ?></p>
<?php endif; ?>

<?php if ($reservas === []): ?>
    <p>Você ainda não fez reservas nesta sessão. <a href="reservar.php">Fazer uma reserva</a>.</p>
<?php else: ?>
    <ol>
        <?php foreach ($reservas as $reserva): ?>
            <li>
                <strong><?= e($reserva['titulo']) ?></strong> — para <?= e($reserva['nome']) ?>,
                retirada em <?= e(date('d/m/Y', strtotime($reserva['data']))) ?>
                (<?= e(PERIODOS[$reserva['periodo']]) ?>)
            </li>
        <?php endforeach; ?>
    </ol>

    <form action="minhas-reservas.php" method="post">
        <button type="submit">Limpar lista</button>
    </form>
<?php endif; ?>

<?php require __DIR__ . '/partes/rodape.php'; ?>
