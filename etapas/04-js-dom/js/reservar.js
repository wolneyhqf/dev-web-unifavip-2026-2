/* =====================================================================
   reservar.js — Apostila 05, seções 4, 7 e 9
   - Contador de caracteres (evento "input")
   - Validação no envio (evento "submit") com mensagens junto aos campos

   Nesta etapa NÃO existe servidor: usamos preventDefault() sempre, e a
   mensagem final só confirma que os dados foram CONFERIDOS (nada é salvo).
   ===================================================================== */

const formulario = document.querySelector("#form-reserva");
const resultado = document.querySelector("#resultado");
const observacoes = document.querySelector("#observacoes");
const contador = document.querySelector("#contador");

// ---- Contador ---------------------------------------------------------
observacoes.addEventListener("input", () => {
  contador.textContent = observacoes.value.length + " de 300 caracteres";
});

// ---- Funções auxiliares -------------------------------------------------
function mostrarErro(id, mensagem) {
  document.querySelector("#erro-" + id).textContent = mensagem;
  const campo = document.querySelector("#" + id);
  if (campo !== null) {
    if (mensagem === "") {
      campo.removeAttribute("aria-invalid");
    } else {
      campo.setAttribute("aria-invalid", "true");
    }
  }
}

// Desliga as mensagens nativas do navegador para usarmos as nossas.
// (Sem JavaScript, required e type="email" continuam funcionando.)
formulario.noValidate = true;

// ---- Validação no envio ---------------------------------------------------
formulario.addEventListener("submit", (evento) => {
  evento.preventDefault(); // nesta etapa nunca enviamos (não há servidor)
  resultado.textContent = "";

  // .value sempre é TEXTO; trim() remove espaços das bordas
  const nome = document.querySelector("#nome").value.trim();
  const email = document.querySelector("#email").value.trim();
  const livro = document.querySelector("#livro_id");
  const data = document.querySelector("#data_retirada").value;
  const periodoMarcado = document.querySelector('input[name="periodo"]:checked');

  let primeiroErro = null;

  if (nome.length < 2) {
    mostrarErro("nome", "Informe pelo menos 2 letras.");
    primeiroErro = primeiroErro ?? document.querySelector("#nome");
  } else {
    mostrarErro("nome", "");
  }

  if (!email.includes("@") || !email.includes(".")) {
    mostrarErro("email", "Informe um e-mail válido.");
    primeiroErro = primeiroErro ?? document.querySelector("#email");
  } else {
    mostrarErro("email", "");
  }

  if (livro.value === "") {
    mostrarErro("livro_id", "Selecione um livro.");
    primeiroErro = primeiroErro ?? livro;
  } else {
    mostrarErro("livro_id", "");
  }

  if (data === "") {
    mostrarErro("data_retirada", "Escolha a data de retirada.");
    primeiroErro = primeiroErro ?? document.querySelector("#data_retirada");
  } else {
    mostrarErro("data_retirada", "");
  }

  if (periodoMarcado === null) {
    mostrarErro("periodo", "Escolha um período.");
    primeiroErro = primeiroErro ?? document.querySelector('input[name="periodo"]');
  } else {
    mostrarErro("periodo", "");
  }

  if (primeiroErro !== null) {
    primeiroErro.focus(); // leva o teclado ao primeiro campo com problema
    return;
  }

  // O texto da opção escolhida (e não o value)
  const tituloLivro = livro.options[livro.selectedIndex].textContent;
  resultado.textContent = "Dados conferidos para " + nome + ": " + tituloLivro +
    " em " + data.split("-").reverse().join("/") + ". (Nada foi salvo: ainda não há servidor.)";
});
