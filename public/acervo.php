<?php
/**
 * Acervo: lista de livros com filtro.
 *
 * Demonstra DUAS formas de filtrar:
 *  1. No SERVIDOR, com GET (Apostila PHP 2.2): o formulário envia ?genero=...&busca=...
 *     e o PHP consulta o banco. Funciona até com JavaScript desligado e a URL
 *     pode ser salva nos favoritos.
 *  2. No NAVEGADOR, com JavaScript (Apostila 05, seção 5): js/acervo.js esconde
 *     as linhas da tabela enquanto a pessoa digita, sem nova requisição.
 */

require __DIR__ . '/../src/iniciar.php';

// --- 1. Ler e normalizar os parâmetros GET -----------------------------
$genero = $_GET['genero'] ?? '';
$busca  = $_GET['busca'] ?? '';
$genero = is_string($genero) ? $genero : '';
$busca  = is_string($busca) ? trim($busca) : '';

// --- 2. Validar: gênero precisa estar na lista permitida ----------------
if ($genero !== '' && !array_key_exists($genero, GENEROS)) {
    $genero = ''; // valor desconhecido: ignoramos o filtro em vez de quebrar a página
}

// --- 3. Montar a consulta com prepared statement ------------------------
// Apostila PHP 4.3 — NUNCA concatenar o valor digitado no SQL.
// Montamos só a ESTRUTURA com marcadores (:genero, :busca); os valores vão separados.
$condicoes  = [];
$parametros = [];
if ($genero !== '') {
    $condicoes[]          = 'l.genero = :genero';
    $parametros['genero'] = $genero;
}
if ($busca !== '') {
    $condicoes[]         = '(l.titulo LIKE :busca OR l.autor LIKE :busca)';
    $parametros['busca'] = '%' . $busca . '%';
}
$sql = SQL_LIVROS_COM_DISPONIBILIDADE
     . ($condicoes ? ' WHERE ' . implode(' AND ', $condicoes) : '')
     . ' GROUP BY l.id ORDER BY l.titulo';

$stmt = banco()->prepare($sql);
$stmt->execute($parametros);
$livros = $stmt->fetchAll();

$tituloPagina = 'Acervo';
$paginaAtual  = 'acervo';
// relatorio.js (funções puras, Apostila 04) vem ANTES de acervo.js, que as utiliza.
$scripts = ['js/relatorio.js', 'js/acervo.js'];
require __DIR__ . '/../src/partes/topo.php';
?>

<h1>Acervo</h1>

<form class="filtros" action="<?= e(url('acervo.php')) ?>" method="get" role="search">
    <div class="campo">
        <label for="busca">Título ou autor</label>
        <input id="busca" name="busca" type="search" value="<?= e($busca) ?>"
               placeholder="Ex.: Machado" autocomplete="off">
    </div>
    <div class="campo">
        <label for="genero">Gênero</label>
        <select id="genero" name="genero">
            <option value="">Todos</option>
            <?php foreach (GENEROS as $valor => $rotulo): ?>
                <option value="<?= e($valor) ?>" <?= $valor === $genero ? 'selected' : '' ?>><?= e($rotulo) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filtros__acoes">
        <button type="submit" class="botao">Filtrar</button>
        <?php if ($genero !== '' || $busca !== ''): ?>
            <a class="botao botao--secundario" href="<?= e(url('acervo.php')) ?>">Limpar</a>
        <?php endif; ?>
    </div>
</form>

<!-- aria-live="polite": leitores de tela anunciam quando o texto muda (Apostila 05, seção 3) -->
<p id="contador-livros" class="contador" aria-live="polite">
    <?= count($livros) ?> livro(s) encontrado(s).
</p>

<?php if ($livros === []): ?>
    <p class="vazio">Nenhum livro corresponde ao filtro. <a href="<?= e(url('acervo.php')) ?>">Ver todos</a>.</p>
<?php else: ?>
    <!-- A tabela fica dentro de um contêiner com rolagem horizontal em telas pequenas -->
    <div class="tabela-rolavel">
        <table class="tabela" id="tabela-acervo">
            <caption>Livros do acervo e exemplares disponíveis</caption>
            <thead>
                <tr>
                    <th scope="col">Título</th>
                    <th scope="col">Autor</th>
                    <th scope="col">Gênero</th>
                    <th scope="col">Ano</th>
                    <th scope="col">Disponíveis</th>
                    <th scope="col"><span class="visualmente-oculto">Ação</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livros as $livro): ?>
                    <!-- Atributos data-* (Apostila 02, 11.2) guardam dados para o JavaScript -->
                    <tr data-titulo="<?= e($livro['titulo']) ?>"
                        data-autor="<?= e($livro['autor']) ?>"
                        data-genero="<?= e($livro['genero']) ?>"
                        data-exemplares="<?= e($livro['exemplares']) ?>"
                        data-ocupados="<?= e($livro['ocupados']) ?>">
                        <th scope="row">
                            <a href="<?= e(url('livro.php?id=' . $livro['id'])) ?>"><?= e($livro['titulo']) ?></a>
                        </th>
                        <td><?= e($livro['autor']) ?></td>
                        <td><?= e(GENEROS[$livro['genero']]) ?></td>
                        <td><?= e($livro['ano_publicacao']) ?></td>
                        <td>
                            <?php if ($livro['disponiveis'] > 0): ?>
                                <span class="selo"><?= e($livro['disponiveis']) ?> de <?= e($livro['exemplares']) ?></span>
                            <?php else: ?>
                                <span class="selo selo--esgotado">Esgotado</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($livro['disponiveis'] > 0): ?>
                                <a class="botao botao--pequeno" href="<?= e(url('reservar.php?livro=' . $livro['id'])) ?>">
                                    Reservar<span class="visualmente-oculto"> <?= e($livro['titulo']) ?></span>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../src/partes/rodape.php'; ?>
