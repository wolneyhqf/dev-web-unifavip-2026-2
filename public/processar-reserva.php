<?php
/**
 * Controlador da reserva — recebe o POST do formulário.
 *
 * Etapas (Apostila PHP 2.4 — normalização, validação, escape):
 *   1. Aceitar só POST e conferir o token CSRF.
 *   2. NORMALIZAR os dados (trim, minúsculas no e-mail).
 *   3. VALIDAR no servidor — mesmo que o HTML e o JavaScript já tenham validado,
 *      qualquer pessoa pode burlar o navegador (DevTools, curl...).
 *   4. Gravar dentro de uma TRANSAÇÃO com trava (Apostila PHP 4.6 e 5.3),
 *      para que duas pessoas não fiquem com o último exemplar.
 *   5. Post-Redirect-Get (Apostila PHP 3.6): sucesso → redireciona para a confirmação.
 *
 * Em caso de erro, o formulário é exibido novamente com as mensagens e com
 * o que a pessoa já tinha digitado, usando o status HTTP adequado:
 *   422 = dados inválidos   |   409 = conflito (esgotado ou reserva repetida)
 */

require __DIR__ . '/../src/iniciar.php';

// --- 1. Método e CSRF ---------------------------------------------------
exigirPost();
verificarCsrf();

// --- 2. Normalização ----------------------------------------------------
$generosRecebidos = $_POST['generos'] ?? [];
$dados = [
    'nome'          => textoPost('nome'),
    'email'         => mb_strtolower(textoPost('email')),
    'livro_id'      => textoPost('livro_id'),
    'data_retirada' => textoPost('data_retirada'),
    'periodo'       => textoPost('periodo'),
    'generos'       => is_array($generosRecebidos) ? array_values(array_filter($generosRecebidos, 'is_string')) : [],
    'observacoes'   => textoPost('observacoes'),
];

// --- 3. Validação -------------------------------------------------------
$erros = [];

if (mb_strlen($dados['nome']) < 2 || mb_strlen($dados['nome']) > 120) {
    $erros['nome'] = 'Informe o nome com 2 a 120 caracteres.';
}

if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL) || strlen($dados['email']) > 190) {
    $erros['email'] = 'Informe um e-mail válido, como nome@exemplo.com.';
}

$livroId = inteiroPositivo($dados['livro_id']);
if ($livroId === null) {
    $erros['livro_id'] = 'Selecione um livro da lista.';
}

$hoje   = new DateTimeImmutable('today');
$limite = $hoje->modify('+' . DIAS_MAXIMOS_RESERVA . ' days');
if (!dataValida($dados['data_retirada'])) {
    $erros['data_retirada'] = 'Informe uma data de retirada válida.';
} else {
    $dataRetirada = new DateTimeImmutable($dados['data_retirada']);
    if ($dataRetirada < $hoje || $dataRetirada > $limite) {
        $erros['data_retirada'] = 'A retirada deve ser entre hoje e ' . $limite->format('d/m/Y') . '.';
    }
}

// Radio e checkbox: o valor recebido precisa estar na lista permitida (Apostila PHP 2.5).
// in_array com o 3º parâmetro true faz comparação ESTRITA (tipo e valor).
if (!array_key_exists($dados['periodo'], PERIODOS)) {
    $erros['periodo'] = 'Escolha um período de retirada.';
}

if (!is_array($generosRecebidos)) {
    $erros['generos'] = 'Formato inválido para os gêneros de interesse.';
} else {
    foreach ($dados['generos'] as $genero) {
        if (!in_array($genero, array_keys(GENEROS), true)) {
            $erros['generos'] = 'Um dos gêneros escolhidos não existe.';
            break;
        }
    }
}

if (mb_strlen($dados['observacoes']) > 300) {
    $erros['observacoes'] = 'As observações devem ter no máximo 300 caracteres.';
}

// --- 4. Gravação com transação -------------------------------------------
$status = 200;
if ($erros !== []) {
    $status = 422; // Unprocessable Content: entendemos o pedido, mas os dados são inválidos
} else {
    $pdo = banco();
    $emTransacao = false;
    try {
        // BEGIN IMMEDIATE: reserva o direito de escrita no banco AGORA.
        // Se outra pessoa estiver reservando ao mesmo tempo, esta requisição
        // espera (até o timeout configurado em db.php) até a outra terminar.
        // No MySQL o equivalente seria beginTransaction() + SELECT ... FOR UPDATE.
        $pdo->exec('BEGIN IMMEDIATE');
        $emTransacao = true;

        // a) O livro existe? Quantos exemplares estão ocupados?
        $stmt = $pdo->prepare("
            SELECT l.exemplares,
                   (SELECT COUNT(*) FROM reservas
                     WHERE livro_id = l.id AND situacao IN ('ativa', 'retirada')) AS ocupados
              FROM livros AS l
             WHERE l.id = ?
        ");
        $stmt->execute([$livroId]);
        $livro = $stmt->fetch();

        if ($livro === false) {
            $erros['livro_id'] = 'O livro escolhido não existe mais no acervo.';
            $status = 422;
        } elseif ($livro['ocupados'] >= $livro['exemplares']) {
            $erros['livro_id'] = 'Que pena! Todos os exemplares deste livro já foram reservados.';
            $status = 409; // Conflict
        } else {
            // b) Esta pessoa já reservou este livro antes?
            $stmt = $pdo->prepare('SELECT 1 FROM reservas WHERE livro_id = ? AND email = ?');
            $stmt->execute([$livroId, $dados['email']]);
            if ($stmt->fetchColumn() !== false) {
                $erros['email'] = 'Já existe uma reserva deste livro para este e-mail.';
                $status = 409;
            }
        }

        if ($erros === []) {
            // c) Tudo certo: grava a reserva (prepared statement — Apostila PHP 4.3)
            $stmt = $pdo->prepare('
                INSERT INTO reservas (livro_id, nome, email, data_retirada, periodo, generos_interesse, observacoes)
                VALUES (:livro_id, :nome, :email, :data_retirada, :periodo, :generos, :observacoes)
            ');
            $stmt->execute([
                'livro_id'      => $livroId,
                'nome'          => $dados['nome'],
                'email'         => $dados['email'],
                'data_retirada' => $dados['data_retirada'],
                'periodo'       => $dados['periodo'],
                'generos'       => implode(',', $dados['generos']),
                'observacoes'   => $dados['observacoes'],
            ]);
            $novoId = (int) $pdo->lastInsertId();
            $pdo->exec('COMMIT');      // confirma tudo de uma vez
        } else {
            $pdo->exec('ROLLBACK');    // nada foi gravado; libera a trava
        }
        $emTransacao = false;
    } catch (PDOException $erro) {
        if ($emTransacao) {
            $pdo->exec('ROLLBACK');
        }
        // SQLSTATE 23000 = violação de restrição (UNIQUE, FOREIGN KEY...).
        // É a "última linha de defesa": o banco garante a regra mesmo se o PHP falhar.
        if ($erro->getCode() === '23000') {
            $erros['email'] = 'Já existe uma reserva deste livro para este e-mail.';
            $status = 409;
        } else {
            throw $erro; // erro inesperado: deixa o PHP mostrar (ambiente de desenvolvimento)
        }
    }
}

// --- 5a. Sucesso: Post-Redirect-Get --------------------------------------
if ($erros === []) {
    // Guardamos o id na SESSÃO para a página de confirmação saber o que mostrar.
    $_SESSION['ultima_reserva'] = $novoId;
    definirFlash('sucesso', 'Reserva registrada com sucesso!');
    redirecionar('reserva-confirmada.php'); // 302 + Location + exit
}

// --- 5b. Erro: reexibe o formulário com o status adequado ----------------
http_response_code($status);
$livros = banco()->query(SQL_LIVROS_COM_DISPONIBILIDADE . ' GROUP BY l.id ORDER BY l.titulo')->fetchAll();

$tituloPagina = 'Reservar livro';
$paginaAtual  = 'reservar';
$scripts      = ['js/reserva.js'];
require __DIR__ . '/../src/partes/topo.php';
?>

<h1>Reservar livro</h1>

<?php require __DIR__ . '/../src/partes/form-reserva.php'; ?>

<?php require __DIR__ . '/../src/partes/rodape.php'; ?>
