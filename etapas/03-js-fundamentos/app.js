/* =====================================================================
   Etapa 03 — JavaScript fundamentos (Apostila 04)
   Tudo aqui aparece no CONSOLE (F12 → Console). Ainda não mexemos na página.
   ===================================================================== */

console.log("Olá, Biblioteca do Bairro!");

// ---------------------------------------------------------------------
// 1. Variáveis e tipos
// const: não pode receber outro valor. let: pode.
// ---------------------------------------------------------------------
const nomeBiblioteca = "Biblioteca do Bairro"; // string
const exemplares = 3;                           // number
let reservados = 1;                             // number (vai mudar)
const aberta = true;                            // boolean
let proximaDevolucao = null;                    // ausência intencional de valor

console.log(typeof nomeBiblioteca, typeof exemplares, typeof aberta, typeof proximaDevolucao);

reservados = reservados + 1;
console.log("Reservados agora:", reservados);

// Conversões: o + junta textos se algum lado for string
console.log("3" + 1);          // "31"  → concatenação
console.log(Number("3") + 1);  // 4     → conversão explícita
console.log(Number("abc"));    // NaN   → conversão inválida
console.log(3 === "3");        // false → tipos diferentes

// ---------------------------------------------------------------------
// 2. Condições
// ---------------------------------------------------------------------
const disponiveis = exemplares - reservados;

if (disponiveis > 0) {
  console.log("Ainda há " + disponiveis + " exemplar(es) para reservar.");
} else {
  console.log("Livro esgotado.");
}

// && (e), || (ou), ! (não)
const podeReservar = aberta && disponiveis > 0;
console.log("Pode reservar?", podeReservar);

// switch: várias opções discretas
const periodo = "tarde";
switch (periodo) {
  case "manha": console.log("Retirada entre 9h e 12h"); break;
  case "tarde": console.log("Retirada entre 13h e 17h"); break;
  case "noite": console.log("Retirada entre 18h e 21h"); break;
  default: console.log("Período desconhecido");
}

// ---------------------------------------------------------------------
// 3. Repetições — sempre confira o que faz o laço PARAR
// ---------------------------------------------------------------------
for (let dia = 1; dia <= 3; dia++) {
  console.log("Lembrete de devolução no dia " + dia);
}

let tentativas = 0;
while (tentativas < 2) {
  tentativas++; // sem esta linha o laço nunca termina!
  console.log("Tentativa " + tentativas);
}

// ---------------------------------------------------------------------
// 4. Funções — recebem parâmetros e devolvem resultado com return
// ---------------------------------------------------------------------
function calcularDisponiveis(totalExemplares, totalReservados) {
  return Math.max(0, totalExemplares - totalReservados); // nunca negativo
}

function podeReservarLivro(totalExemplares, totalReservados) {
  return totalReservados < totalExemplares;
}

console.log(calcularDisponiveis(3, 1)); // 2
console.log(podeReservarLivro(2, 2));   // false

// ---------------------------------------------------------------------
// 5. Arrays e objetos
// ---------------------------------------------------------------------
const livros = [
  { titulo: "Dom Casmurro", genero: "romance", exemplares: 3, reservados: 2 },
  { titulo: "O Alienista", genero: "ficcao", exemplares: 2, reservados: 0 },
  { titulo: "O Pequeno Príncipe", genero: "infantil", exemplares: 2, reservados: 2 },
  { titulo: "Código Limpo", genero: "tecnologia", exemplares: 1, reservados: 0 },
];

console.log("Primeiro livro:", livros[0].titulo); // índice começa em 0
console.log("Quantidade de livros:", livros.length);
console.log(livros[0].autor); // propriedade que não existe → undefined

// ---------------------------------------------------------------------
// 6. Prática guiada — relatório de disponibilidade
// Saída esperada:
//   Dom Casmurro: 1 disponível(is)
//   O Alienista: 2 disponível(is)
//   O Pequeno Príncipe: Esgotado
//   Código Limpo: 1 disponível(is)
// ---------------------------------------------------------------------
console.log("=== Relatório ===");
for (const livro of livros) {
  const quantos = calcularDisponiveis(livro.exemplares, livro.reservados);
  const situacao = quantos > 0 ? quantos + " disponível(is)" : "Esgotado"; // operador ternário
  console.log(livro.titulo + ": " + situacao);
}

// ---------------------------------------------------------------------
// 7. Desafio resolvido — resumo do acervo em um objeto
// ---------------------------------------------------------------------
function resumoAcervo(lista) {
  let totalDisponiveis = 0;
  let esgotados = 0;
  for (const livro of lista) {
    const quantos = calcularDisponiveis(livro.exemplares, livro.reservados);
    totalDisponiveis += quantos;
    if (quantos === 0) {
      esgotados++;
    }
  }
  return { titulos: lista.length, disponiveis: totalDisponiveis, esgotados: esgotados };
}

console.log(resumoAcervo(livros)); // { titulos: 4, disponiveis: 4, esgotados: 1 }
console.log(resumoAcervo([]));     // { titulos: 0, disponiveis: 0, esgotados: 0 }

// ---------------------------------------------------------------------
// 8. Strings: busca sem diferenciar maiúsculas/minúsculas
// ---------------------------------------------------------------------
const termo = "  CASMURRO ".trim().toLowerCase();
for (const livro of livros) {
  if (livro.titulo.toLowerCase().includes(termo)) {
    console.log("Encontrado:", livro.titulo);
  }
}

/* ---------------------------------------------------------------------
   PARA PRATICAR
   1. Acrescente um livro ao array e veja o relatório mudar.
   2. Faça o relatório mostrar só livros do gênero "romance".
   3. Crie a função livrosEsgotados(lista) que devolve um array só com os títulos esgotados.
   4. Descubra o erro: console.log("Disponíveis: " + "3" - 1);  (por que dá NaN?)
   --------------------------------------------------------------------- */
