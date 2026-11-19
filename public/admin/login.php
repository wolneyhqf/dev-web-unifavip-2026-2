<?php
/**
 * Login da área do bibliotecário.
 * A mesma página exibe o formulário (GET) e processa o envio (POST).
 *
 * Usuário de demonstração: bibliotecario / biblio123
 */

require __DIR__ . '/../../src/iniciar.php';

if (usuarioLogado() !== null) {
    redirecionar('admin/index.php');
}

$login = '';
$erro  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarCsrf();
    $login = textoPost('login');
    $senha = $_POST['senha'] ?? '';   // a senha NÃO passa por trim: espaços podem fazer parte dela

    if (is_string($senha) && autenticar($login, $senha)) {
        definirFlash('sucesso', 'Bem-vindo(a), ' . $login . '!');
        redirecionar('admin/index.php');   // PRG também no login
    }

    // Mensagem genérica de propósito: não revelamos se o erro foi no login ou na senha.
    $erro = 'Usuário ou senha incorretos.';
    http_response_code(401); // 401 = não autenticado (Apostila 01, 5.5)
}

$tituloPagina = 'Entrar';
$paginaAtual  = 'admin';
require __DIR__ . '/../../src/partes/topo.php';
?>

<section class="cartao cartao--estreito" aria-labelledby="titulo-login">
    <h1 id="titulo-login">Área do bibliotecário</h1>

    <?php if ($erro !== ''): ?>
        <p class="alerta alerta--erro" role="alert"><?= e($erro) ?></p>
    <?php endif; ?>

    <form class="formulario" action="<?= e(url('admin/login.php')) ?>" method="post">
        <?= campoCsrf() ?>
        <div class="campo">
            <label for="login">Usuário</label>
            <input id="login" name="login" type="text" required autocomplete="username" value="<?= e($login) ?>">
        </div>
        <div class="campo">
            <label for="senha">Senha</label>
            <input id="senha" name="senha" type="password" required autocomplete="current-password">
        </div>
        <button type="submit" class="botao">Entrar</button>
    </form>

    <p class="dica">Ambiente de estudo — usuário <code>bibliotecario</code>, senha <code>biblio123</code>.</p>
</section>

<?php require __DIR__ . '/../../src/partes/rodape.php'; ?>
