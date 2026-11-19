/* =====================================================================
   relatorio.js — Apostila 04 (JavaScript fundamentos)

   Este arquivo NÃO mexe na página (não usa o DOM). Ele só contém funções
   que recebem dados e devolvem resultados: variáveis, condições, laços,
   arrays e objetos. Por isso pode ser testado direto no Console:

     resumoAcervo([{ titulo: "A", exemplares: 2, ocupados: 2 }])

   Quem usa estas funções é o js/acervo.js, que pega os dados da tabela.
   ===================================================================== */

/**
 * Quantos exemplares de um livro ainda podem ser reservados.
 * Math.max(0, ...) evita número negativo se algum dado vier inconsistente.
 */
function calcularDisponiveis(livro) {
  return Math.max(0, livro.exemplares - livro.ocupados);
}

/**
 * Texto da situação do livro, usando o operador ternário (condição ? a : b).
 */
function situacaoLivro(livro) {
  const disponiveis = calcularDisponiveis(livro);
  return disponiveis > 0 ? disponiveis + " disponível(is)" : "Esgotado";
}

/**
 * Recebe um array de livros e devolve um OBJETO com o resumo do acervo.
 * Mostra: laço for...of, acumuladores com let e condição if.
 */
function resumoAcervo(livros) {
  let exemplares = 0;
  let disponiveis = 0;
  let esgotados = 0;

  for (const livro of livros) {
    exemplares = exemplares + livro.exemplares;
    disponiveis += calcularDisponiveis(livro);
    if (calcularDisponiveis(livro) === 0) {
      esgotados++;
    }
  }

  return {
    titulos: livros.length,
    exemplares: exemplares,
    disponiveis: disponiveis,
    esgotados: esgotados,
  };
}

/**
 * Prepara um texto para comparação em buscas:
 *  - toLocaleLowerCase: "MACHADO" e "machado" passam a ser iguais;
 *  - normalize + replace: remove acentos ("Memórias" → "memorias");
 *  - trim: ignora espaços nas bordas.
 * O texto original NÃO é alterado: strings são imutáveis, o método devolve outra.
 */
function normalizarTexto(texto) {
  return texto
    .toLocaleLowerCase("pt-BR")
    .normalize("NFD")
    .replace(/[̀-ͯ]/g, "")
    .trim();
}

/**
 * Converte o código do gênero no nome exibido — exemplo de switch.
 */
function nomeDoGenero(codigo) {
  switch (codigo) {
    case "ficcao": return "Ficção";
    case "romance": return "Romance";
    case "fantasia": return "Fantasia";
    case "tecnologia": return "Tecnologia";
    case "infantil": return "Infantil";
    case "biografia": return "Biografia";
    default: return "Desconhecido";
  }
}

/**
 * Imprime no Console um relatório linha a linha (abra o DevTools → Console).
 */
function imprimirRelatorio(livros) {
  console.log("=== Relatório do acervo ===");
  for (let i = 0; i < livros.length; i++) {
    const livro = livros[i];
    console.log((i + 1) + ". " + livro.titulo + " [" + nomeDoGenero(livro.genero) + "]: " + situacaoLivro(livro));
  }
  const resumo = resumoAcervo(livros);
  console.log("Total: " + resumo.titulos + " título(s), " + resumo.disponiveis +
    " de " + resumo.exemplares + " exemplar(es) disponível(is), " +
    resumo.esgotados + " esgotado(s).");
}
