/* =====================================================================
   acervo.js — Apostila 05, seção 5 (filtro local)
   Esconde as linhas da tabela que não combinam com o texto digitado.
   Não consulta servidor nem banco: trabalha só com o que já está no HTML.
   ===================================================================== */

const busca = document.querySelector("#busca");
const resultado = document.querySelector("#resultado-busca");
// querySelectorAll devolve TODAS as linhas do corpo da tabela
const linhas = document.querySelectorAll("#tabela-acervo tbody tr");

busca.addEventListener("input", () => {
  const termo = busca.value.trim().toLocaleLowerCase("pt-BR");
  let visiveis = 0;

  for (const linha of linhas) {
    const texto = linha.textContent.toLocaleLowerCase("pt-BR");
    const combina = texto.includes(termo);
    linha.hidden = !combina;   // hidden = true esconde a linha
    if (combina) {
      visiveis++;
    }
  }

  // textContent: escreve TEXTO, nunca interpreta HTML (mais seguro que innerHTML)
  if (visiveis === 0) {
    resultado.textContent = "Nenhum livro encontrado.";
  } else {
    resultado.textContent = visiveis + " livro(s) na lista.";
  }
});
