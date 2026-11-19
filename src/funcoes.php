<?php
/**
 * Funções auxiliares usadas por todas as páginas.
 * Apostila PHP, Módulo 5.2 — Arquivo de funções auxiliares.
 *
 * Agrupar estas rotinas evita repetir o mesmo código em cada página
 * e garante que todas as páginas tratem os dados da mesma forma.
 */

declare(strict_types=1);

// ---------------------------------------------------------------------
// Listas permitidas (whitelists)
// Apostila PHP 2.5 — select, radio e checkbox devem ser conferidos contra
// uma lista de valores aceitos. A chave é o valor enviado; o valor é o rótulo.
// ---------------------------------------------------------------------
const GENEROS = [
    'ficcao'     => 'Ficção',
    'romance'    => 'Romance',
    'fantasia'   => 'Fantasia',
    'tecnologia' => 'Tecnologia',
    'infantil'   => 'Infantil',
    'biografia'  => 'Biografia',
];

const PERIODOS = [
    'manha' => 'Manhã (9h às 12h)',
    'tarde' => 'Tarde (13h às 17h)',
    'noite' => 'Noite (18h às 21h)',
];

const SITUACOES = [
    'ativa'     => 'Aguardando retirada',
    'retirada'  => 'Retirada',
    'devolvida' => 'Devolvida',
    'cancelada' => 'Cancelada',
];

// Uma reserva pode ser feita para hoje ou para até este número de dias à frente.
const DIAS_MAXIMOS_RESERVA = 15;

// ---------------------------------------------------------------------
// Saída segura
// ---------------------------------------------------------------------

/**
 * Escapa um texto antes de colocá-lo no HTML.
 * Apostila PHP 1.6 e 2.4 — htmlspecialchars neutraliza ataques XSS:
 * "<script>" vira "&lt;script&gt;" e aparece como texto, sem executar.
 */
function e(string|int|float|null $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

// ---------------------------------------------------------------------
// Validação
// ---------------------------------------------------------------------

/**
 * Converte um valor recebido em inteiro >= 1, ou devolve null se for inválido.
 * filter_var é mais rigoroso que (int): "3abc" é rejeitado em vez de virar 3.
 */
function inteiroPositivo(mixed $valor): ?int
{
    $numero = filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $numero === false ? null : $numero;
}

/**
 * Confere se o texto é uma data real no formato AAAA-MM-DD.
 * O truque de formatar de volta e comparar rejeita datas como 2026-02-31.
 */
function dataValida(string $data): bool
{
    $objeto = DateTimeImmutable::createFromFormat('!Y-m-d', $data);
    return $objeto !== false && $objeto->format('Y-m-d') === $data;
}

/** Converte AAAA-MM-DD em DD/MM/AAAA para exibição. */
function formatarData(string $data): string
{
    $objeto = DateTimeImmutable::createFromFormat('!Y-m-d', $data);
    return $objeto ? $objeto->format('d/m/Y') : $data;
}

/** Lê um campo de texto de $_POST já normalizado (sem espaços nas bordas). */
function textoPost(string $campo): string
{
    $valor = $_POST[$campo] ?? '';
    return is_string($valor) ? trim($valor) : '';
}

// ---------------------------------------------------------------------
// HTTP
// ---------------------------------------------------------------------

/**
 * Interrompe o script se o método HTTP não for POST.
 * Apostila PHP 2.3 — páginas que alteram dados só aceitam POST (405).
 */
function exigirPost(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: POST');
        paginaDeErro(405, 'Método não permitido', 'Esta página só pode ser acessada pelo envio de um formulário.');
    }
}

/**
 * Monta uma URL a partir da pasta public/.
 *
 * Por que isso existe? Com "php -S localhost:8000 -t public" o site fica na
 * raiz (/acervo.php). Já no XAMPP ele pode ficar em /web-exemplo/public/acervo.php.
 * Esta função descobre o "prefixo" automaticamente para que os links
 * funcionem nos dois casos, inclusive a partir da pasta admin/.
 *
 * Exemplo: url('admin/index.php') → "/admin/index.php" ou "/web-exemplo/public/admin/index.php"
 */
function url(string $caminho = ''): string
{
    static $base = null;
    if ($base === null) {
        $pastaPublica = str_replace('\\', '/', (string) realpath(__DIR__ . '/../public'));
        $pastaScript  = str_replace('\\', '/', dirname((string) realpath($_SERVER['SCRIPT_FILENAME'])));
        $subpasta     = substr($pastaScript, strlen($pastaPublica));           // "" ou "/admin"
        $pastaUrl     = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        $base         = substr($pastaUrl, 0, strlen($pastaUrl) - strlen($subpasta));
    }
    return $base . '/' . ltrim($caminho, '/');
}

/**
 * Redireciona o navegador e encerra o script.
 * Apostila PHP 3.6 — peça central do padrão Post-Redirect-Get.
 * O "exit" é obrigatório: sem ele o PHP continuaria executando o resto da página.
 */
function redirecionar(string $caminho): never
{
    header('Location: ' . url($caminho));
    exit;
}

/**
 * Responde com um código de status HTTP e uma página de erro amigável.
 * Apostila 01 (5.5) e PHP 2.6 — o status comunica o resultado ao navegador.
 */
function paginaDeErro(int $status, string $titulo, string $mensagem): never
{
    http_response_code($status);
    $tituloPagina = $titulo;
    require __DIR__ . '/partes/topo.php';
    ?>
    <section class="cartao cartao--erro" aria-labelledby="titulo-erro">
        <p class="codigo-status">Erro <?= $status ?></p>
        <h1 id="titulo-erro"><?= e($titulo) ?></h1>
        <p><?= e($mensagem) ?></p>
        <p><a class="botao botao--secundario" href="<?= e(url('index.php')) ?>">Voltar para o início</a></p>
    </section>
    <?php
    require __DIR__ . '/partes/rodape.php';
    exit;
}

// ---------------------------------------------------------------------
// Sessão: mensagens "flash" e dados antigos de formulário
// Apostila PHP 3.4 — $_SESSION guarda dados no SERVIDOR entre requisições.
// ---------------------------------------------------------------------

/** Guarda uma mensagem para ser exibida UMA vez na próxima página. */
function definirFlash(string $tipo, string $mensagem): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensagem' => $mensagem];
}

/** Lê e apaga a mensagem flash (por isso ela aparece só uma vez). */
function obterFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

// ---------------------------------------------------------------------
// Proteção CSRF (Cross-Site Request Forgery)
// Apostila PHP 5.5 — "passos para produção".
//
// Um site malicioso poderia criar um formulário que envia POST para o nosso
// site usando a sessão do visitante. Para impedir, cada formulário leva um
// código secreto (token) que só o nosso site conhece; sem ele, recusamos o envio.
// ---------------------------------------------------------------------

function tokenCsrf(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/** Campo oculto para colocar dentro de cada <form method="post">. */
function campoCsrf(): string
{
    return '<input type="hidden" name="csrf" value="' . e(tokenCsrf()) . '">';
}

function verificarCsrf(): void
{
    $enviado = $_POST['csrf'] ?? '';
    // hash_equals compara em tempo constante (evita ataques de temporização).
    if (!is_string($enviado) || !hash_equals(tokenCsrf(), $enviado)) {
        paginaDeErro(403, 'Envio recusado', 'O formulário expirou ou não veio deste site. Volte, atualize a página e tente novamente.');
    }
}

// ---------------------------------------------------------------------
// Consultas reutilizadas
// ---------------------------------------------------------------------

/**
 * SQL que lista livros com a quantidade de exemplares disponíveis.
 * Apostila PHP 4.5 — JOIN. Usamos LEFT JOIN para que livros SEM reservas
 * também apareçam (com JOIN simples eles sumiriam do resultado).
 * Reservas "ativa" e "retirada" ocupam um exemplar; as demais não.
 */
const SQL_LIVROS_COM_DISPONIBILIDADE = "
    SELECT l.id, l.titulo, l.autor, l.genero, l.ano_publicacao, l.sinopse, l.exemplares,
           COUNT(r.id) AS ocupados,
           l.exemplares - COUNT(r.id) AS disponiveis
      FROM livros AS l
      LEFT JOIN reservas AS r
             ON r.livro_id = l.id
            AND r.situacao IN ('ativa', 'retirada')
";

/** Busca um livro (com disponibilidade) pelo id, ou null se não existir. */
function buscarLivro(int $id): ?array
{
    $stmt = banco()->prepare(SQL_LIVROS_COM_DISPONIBILIDADE . ' WHERE l.id = ? GROUP BY l.id');
    $stmt->execute([$id]);
    $livro = $stmt->fetch();
    return $livro === false ? null : $livro;
}
