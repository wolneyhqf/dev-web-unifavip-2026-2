/* =====================================================================
   acervo.js — Apostila 05 (DOM e eventos)

   Busca instantânea: enquanto a pessoa digita, as linhas da tabela que não
   combinam ficam escondidas. Não há nova requisição ao servidor: só
   escondemos o que JÁ está na página.

   Sem JavaScript, o mesmo campo continua funcionando pelo botão "Filtrar"
   (o formulário é enviado por GET e o PHP filtra no banco).

   Usa as funções de js/relatorio.js (carregado antes deste arquivo).
   ===================================================================== */

// querySelector devolve o elemento ou null se não encontrar (Apostila 05, seção 1)
const campoBusca = document.querySelector("#busca");
const contador = document.querySelector("#contador-livros");
const linhas = document.querySelectorAll("#tabela-acervo tbody tr");

// Se a página não tem tabela (nenhum livro encontrado), não há o que fazer.
if (campoBusca !== null && linhas.length > 0) {
  // ---- 1. Transformar as linhas da tabela em um array de objetos ----------
  // Os dados vêm dos atributos data-* de cada <tr> (element.dataset).
  // dataset sempre devolve TEXTO: convertemos com Number() (Apostila 04, seção 2).
  const livros = [];
  for (const linha of linhas) {
    livros.push({
      titulo: linha.dataset.titulo,
      autor: linha.dataset.autor,
      genero: linha.dataset.genero,
      exemplares: Number(linha.dataset.exemplares),
      ocupados: Number(linha.dataset.ocupados),
      linha: linha, // guardamos a referência ao elemento para escondê-lo depois
    });
  }

  imprimirRelatorio(livros); // veja o resultado no Console do DevTools

  // ---- 2. Função que aplica o filtro -------------------------------------
  function filtrar() {
    const termo = normalizarTexto(campoBusca.value);
    let visiveis = 0;

    for (const livro of livros) {
      const texto = normalizarTexto(livro.titulo + " " + livro.autor);
      const combina = texto.includes(termo);
      livro.linha.hidden = !combina; // hidden = true esconde o elemento
      if (combina) {
        visiveis++;
      }
    }

    // textContent (e não innerHTML): o termo digitado é tratado como texto puro.
    if (visiveis === 0) {
      contador.textContent = "Nenhum livro encontrado para \"" + campoBusca.value.trim() + "\".";
    } else {
      contador.textContent = visiveis + " livro(s) encontrado(s).";
    }
  }

  // ---- 3. Registrar o evento ----------------------------------------------
  // "input" dispara a cada caractere digitado ou apagado (Apostila 05, seção 7).
  campoBusca.addEventListener("input", filtrar);
}
