/* =====================================================================
   tema.js — alternar tema claro/escuro (Apostila 05, seção 8)

   Sem JavaScript: o botão envia um formulário para tema.php, que grava
   um cookie e recarrega a página.

   Com JavaScript: interceptamos o envio, trocamos o atributo data-tema
   do <html> na hora (o CSS faz o resto) e gravamos o MESMO cookie pelo
   navegador, para que o PHP já gere a próxima página no tema escolhido.

   Repare: o JavaScript só decide o ESTADO (claro/escuro); as cores ficam
   todas no CSS. Script expressa a decisão, CSS expressa o visual.
   ===================================================================== */

const formTema = document.querySelector(".form-tema");

if (formTema !== null) {
  const botaoTema = formTema.querySelector("button");
  const campoTema = formTema.querySelector('input[name="tema"]');
  const raiz = document.documentElement; // o elemento <html>

  formTema.addEventListener("submit", (evento) => {
    evento.preventDefault(); // não precisamos ir ao servidor

    const novoTema = raiz.dataset.tema === "escuro" ? "claro" : "escuro";
    raiz.dataset.tema = novoTema;

    // Cookie gravado pelo navegador: nome=valor; atributos (válido por 1 ano)
    document.cookie = "tema=" + novoTema + "; max-age=31536000; path=/; samesite=lax";

    // Mantém o botão e o formulário coerentes com o novo estado
    botaoTema.setAttribute("aria-pressed", novoTema === "escuro" ? "true" : "false");
    campoTema.value = novoTema === "escuro" ? "claro" : "escuro";
  });
}
