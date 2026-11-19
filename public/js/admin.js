/* =====================================================================
   admin.js — confirmação antes de ações destrutivas (Apostila 05)

   Todo <form> com o atributo data-confirmar pede confirmação antes de
   ser enviado. O texto da pergunta vem do próprio atributo.

   Sem JavaScript, o formulário é enviado direto — e o servidor continua
   protegendo os dados (ex.: o banco recusa excluir livro com reservas).
   ===================================================================== */

const formulariosComConfirmacao = document.querySelectorAll("form[data-confirmar]");

for (const form of formulariosComConfirmacao) {
  form.addEventListener("submit", (evento) => {
    // confirm() mostra uma caixa com OK/Cancelar e devolve true ou false.
    const confirmou = window.confirm(form.dataset.confirmar);
    if (!confirmou) {
      evento.preventDefault();
    }
  });
}
