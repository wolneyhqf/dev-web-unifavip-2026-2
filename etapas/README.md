# Etapas — o mesmo site crescendo aula a aula

Cada pasta é **independente** e mostra o site da Biblioteca do Bairro em um estágio da disciplina.
Compare uma pasta com a seguinte para enxergar exatamente o que cada tecnologia acrescenta.

| Pasta | Apostila | O que mostra | Como abrir |
|---|---|---|---|
| `01-html/` | 02 — HTML | Estrutura semântica, listas, links, imagem com legenda, tabela, formulário completo. **Sem CSS e sem JS.** | Duplo clique em `index.html` |
| `02-css/` | 03 — CSS | **O mesmo HTML** da etapa 01 + `css/style.css`: variáveis, box model, Flexbox, Grid, media query. | Duplo clique em `index.html` |
| `03-js-fundamentos/` | 04 — JavaScript | Variáveis, tipos, condições, laços, funções, arrays e objetos. Tudo no **Console**. | Abra `index.html` e pressione F12 → Console |
| `04-js-dom/` | 05 — DOM e eventos | Busca no acervo, validação do formulário com mensagens nos campos, contador de caracteres e tema escuro (`classList`). Ainda **sem servidor**. | Duplo clique em `index.html` |
| `05-php-basico/` | PHP Módulos 1–3 | Páginas `.php` com dados em array, `require` de topo/rodapé, filtro por GET, formulário validado no servidor, sessão, mensagem flash e Post-Redirect-Get. **Sem banco.** | `php -S localhost:8000 -t etapas/05-php-basico` |
| Projeto final (`../public`) | PHP Módulos 4–5 | Tudo junto + banco SQLite, CRUD, JOIN, transação, login e CSRF. | Veja o `README.md` da raiz |

## Sugestões de estudo

1. **01 → 02:** abra os dois `index.html` lado a lado. O HTML é idêntico; só o `<link>` mudou.
2. **02 → 04:** procure no HTML da etapa 04 o que foi acrescentado para o JavaScript (`id`, `aria-live`, parágrafos de erro).
3. **04 → 05:** a validação que era feita pelo JavaScript agora é feita pelo PHP. Na etapa 05 os campos estão **sem** `required` de propósito, para você ver a validação do servidor funcionando.
4. **05 → final:** as reservas que ficavam na sessão (só do seu navegador) passam a ficar no banco (visíveis para todos).
