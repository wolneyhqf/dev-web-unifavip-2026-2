<?php
/**
 * Exclui um livro.
 * Apostila PHP 4.1 e 4.4 — Delete só por POST; a chave estrangeira com
 * ON DELETE RESTRICT impede apagar livros que tenham reservas.
 */

require __DIR__ . '/../../src/iniciar.php';
exigirLogin();
exigirPost();
verificarCsrf();

$id = inteiroPositivo($_POST['id'] ?? null);
$livro = $id === null ? null : buscarLivro($id);
if ($livro === null) {
    paginaDeErro(404, 'Livro não encontrado', 'O livro que você tentou excluir não existe.');
}

try {
    $stmt = banco()->prepare('DELETE FROM livros WHERE id = ?');
    $stmt->execute([$id]);
} catch (PDOException $erro) {
    // SQLSTATE 23000: o banco recusou por causa da chave estrangeira.
    // Repare que NÃO conferimos as reservas antes no PHP: deixamos o próprio
    // banco garantir a integridade e apenas traduzimos o erro para a pessoa.
    if ($erro->getCode() === '23000') {
        paginaDeErro(409, 'Exclusão não permitida',
            'O livro "' . $livro['titulo'] . '" possui reservas registradas (inclusive antigas) e não pode ser excluído.');
    }
    throw $erro;
}

definirFlash('sucesso', 'Livro "' . $livro['titulo'] . '" excluído.');
redirecionar('admin/index.php');
