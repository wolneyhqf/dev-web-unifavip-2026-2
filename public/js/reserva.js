/* =====================================================================
   reserva.js — Apostila 05 (formulários, validação, mensagens)

   O que este script faz:
    1. Mostra quantos caracteres faltam nas observações (evento "input").
    2. Valida o formulário ANTES de enviar e mostra a mensagem junto de cada
       campo, levando o foco ao primeiro campo com problema.

   IMPORTANTE: esta validação existe para a CONVENIÊNCIA de quem preenche.
   Ela pode ser burlada (DevTools, JavaScript desligado...). Por isso o
   servidor (processar-reserva.php) valida TUDO novamente.
   ===================================================================== */

const formulario = document.querySelector("#form-reserva");

if (formulario !== null) {
  // ---- 1. Contador de caracteres ------------------------------------------
  const observacoes = document.querySelector("#observacoes");
  const contador = document.querySelector("#contador-observacoes");
  const LIMITE = Number(observacoes.getAttribute("maxlength"));

  function atualizarContador() {
    const restantes = LIMITE - observacoes.value.length;
    contador.textContent = restantes + " de " + LIMITE + " caracteres restantes.";
  }

  observacoes.addEventListener("input", atualizarContador);
  atualizarContador(); // calcula também ao carregar (o campo pode vir preenchido)

  // ---- 2. Validação ----------------------------------------------------------

  // Desligamos a validação nativa do navegador SÓ quando o JS está ativo,
  // para mostrar nossas mensagens. Sem JS, required/type="email" continuam valendo.
  formulario.noValidate = true;

  /** Mostra (ou limpa, se mensagem for "") o erro de um campo. */
  function mostrarErro(nomeCampo, mensagem) {
    const paragrafo = document.querySelector("#erro-" + nomeCampo);
    const campo = document.querySelector("#" + nomeCampo);
    paragrafo.textContent = mensagem;
    if (campo !== null) {
      if (mensagem === "") {
        campo.removeAttribute("aria-invalid");
      } else {
        campo.setAttribute("aria-invalid", "true");
      }
    }
  }

  /** Converte "AAAA-MM-DD" em um objeto Date à meia-noite (horário local). */
  function criarData(texto) {
    const partes = texto.split("-");
    return new Date(Number(partes[0]), Number(partes[1]) - 1, Number(partes[2]));
  }

  /** Confere todos os campos e devolve um objeto { campo: mensagem }. */
  function validar() {
    const erros = {};

    const nome = formulario.nome.value.trim();
    if (nome.length < 2) {
      erros.nome = "Informe pelo menos 2 letras no nome.";
    }

    const email = formulario.email.value.trim();
    // Verificação simples: algo@algo.algo (o servidor faz a validação completa)
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      erros.email = "Informe um e-mail válido, como nome@exemplo.com.";
    }

    if (formulario.livro_id.value === "") {
      erros.livro_id = "Selecione um livro.";
    }

    const campoData = formulario.data_retirada;
    if (campoData.value === "") {
      erros.data_retirada = "Escolha a data de retirada.";
    } else {
      const data = criarData(campoData.value);
      const minimo = criarData(campoData.min);
      const maximo = criarData(campoData.max);
      if (data < minimo || data > maximo) {
        erros.data_retirada = "Escolha uma data entre " + minimo.toLocaleDateString("pt-BR") +
          " e " + maximo.toLocaleDateString("pt-BR") + ".";
      }
    }

    // Para radios, form.periodo é uma coleção; .value é o valor do marcado ("" se nenhum).
    if (formulario.periodo.value === "") {
      erros.periodo = "Escolha um período de retirada.";
    }

    return erros;
  }

  const campos = ["nome", "email", "livro_id", "data_retirada", "periodo"];

  formulario.addEventListener("submit", (evento) => {
    const erros = validar();

    for (const campo of campos) {
      // erros[campo] ?? "" → usa "" quando não há erro para o campo
      mostrarErro(campo, erros[campo] ?? "");
    }

    const camposComErro = Object.keys(erros);
    if (camposComErro.length > 0) {
      evento.preventDefault(); // impede o envio enquanto houver erro

      // Leva o teclado até o primeiro campo com problema (Apostila 05, seção 9).
      const primeiro = camposComErro[0];
      const elemento = primeiro === "periodo"
        ? formulario.querySelector('input[name="periodo"]')
        : document.querySelector("#" + primeiro);
      elemento.focus();
    }
    // Sem erros: não chamamos preventDefault, e o formulário é enviado ao PHP.
  });

  // Quando a pessoa corrige um campo, a mensagem dele some.
  // Um único listener no <form> recebe os eventos de todos os campos
  // (o evento "sobe" do campo até o formulário); evento.target é o campo alterado.
  formulario.addEventListener("input", (evento) => {
    const nome = evento.target.name;
    if (campos.includes(nome)) {
      mostrarErro(nome, "");
    }
  });
}
