<?php
/**
 * Autenticação da área administrativa usando sessão.
 * Apostila PHP 3.4/3.5 — o servidor guarda QUEM está logado em $_SESSION;
 * o navegador guarda apenas o identificador da sessão (cookie PHPSESSID).
 */

declare(strict_types=1);

/** Devolve o login do bibliotecário autenticado, ou null. */
function usuarioLogado(): ?string
{
    return $_SESSION['usuario'] ?? null;
}

/** Usado no topo das páginas de admin/: sem login, vai para a tela de login. */
function exigirLogin(): void
{
    if (usuarioLogado() === null) {
        definirFlash('aviso', 'Entre com seu usuário para acessar a área do bibliotecário.');
        redirecionar('admin/login.php');
    }
}

/**
 * Confere login e senha no banco. Devolve true se estiverem corretos.
 * A senha digitada é comparada com o hash usando password_verify().
 */
function autenticar(string $login, string $senha): bool
{
    $stmt = banco()->prepare('SELECT login, senha_hash FROM usuarios WHERE login = ?');
    $stmt->execute([$login]);
    $usuario = $stmt->fetch();

    if ($usuario === false || !password_verify($senha, $usuario['senha_hash'])) {
        return false;
    }

    // Gera um novo ID de sessão no login (evita "fixação de sessão").
    session_regenerate_id(true);
    $_SESSION['usuario'] = $usuario['login'];
    return true;
}

function encerrarSessao(): void
{
    $_SESSION = [];
    session_regenerate_id(true);
}
