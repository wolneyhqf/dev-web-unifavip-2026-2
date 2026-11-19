/* =====================================================================
   tema.js — Apostila 05, seção 8 (separar estado e apresentação)
   O clique só liga/desliga a classe "tema-escuro"; as cores estão no CSS.
   ===================================================================== */

const botaoTema = document.querySelector("#alternar-tema");

botaoTema.addEventListener("click", () => {
  // toggle: adiciona a classe se não existir; remove se existir
  document.body.classList.toggle("tema-escuro");

  // contains: consulta o estado atual
  const escuro = document.body.classList.contains("tema-escuro");

  // Acessibilidade: o botão informa se está "pressionado"
  botaoTema.setAttribute("aria-pressed", String(escuro));
});

// Desafio: o tema é esquecido ao trocar de página. Na etapa final,
// a preferência é guardada em um cookie (veja public/js/tema.js).
