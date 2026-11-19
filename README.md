# Biblioteca do Bairro — projeto de exemplo

Projeto modelo da disciplina **Desenvolvimento Web em HTML5, CSS, JavaScript e PHP**.

É o site de uma biblioteca comunitária onde:

- **visitantes** consultam o acervo, filtram por gênero, buscam por título/autor e **reservam livros** para retirada;
- **bibliotecários** entram em uma área restrita para cadastrar, editar e excluir livros e acompanhar as reservas.

O objetivo é servir de **material de estudo**: cada arquivo tem comentários explicando o *porquê* das escolhas e indicando a seção da apostila correspondente.

---

## 1. Como rodar

### Requisitos

- **PHP 8.1 ou mais recente** com as extensões `pdo_sqlite` e `mbstring` (já vêm habilitadas na maioria das instalações).
  Confira com:
  ```bash
  php -v
  php -m
  ```
  Na lista do `php -m` devem aparecer `pdo_sqlite` e `mbstring`.
- Nenhum banco de dados para instalar: o projeto usa **SQLite**, que guarda tudo em um arquivo.

### Usando o servidor embutido do PHP (recomendado)

Na pasta do projeto:

```bash
php -S localhost:8000 -t public
```

Abra **http://localhost:8000** no navegador.

Na primeira página acessada, o arquivo `dados/biblioteca.sqlite` é criado automaticamente com livros e reservas de exemplo.

> Não abra os arquivos `.php` com duplo clique: o navegador mostraria o código em vez de executá-lo.
> O PHP precisa de um servidor (Apostila PHP, 1.2).

### Usando XAMPP / WAMP / Laragon

Copie a pasta do projeto para `htdocs` (ou `www`) e acesse `http://localhost/NOME-DA-PASTA/public/`.
Os links se ajustam sozinhos à subpasta (veja a função `url()` em `src/funcoes.php`).
As pastas `src/`, `sql/`, `dados/` e `bin/` têm um arquivo `.htaccess` que impede o Apache de entregá-las ao navegador
(sem ele, qualquer pessoa poderia baixar o arquivo do banco). O servidor embutido do PHP não lê `.htaccess`,
mas com `-t public` essas pastas já ficam de fora.
No Windows com XAMPP, se `pdo_sqlite` não aparecer, remova o `;` da linha `extension=pdo_sqlite` no `php.ini`.

### Área do bibliotecário

Acesse **Área do bibliotecário** no menu e entre com:

| Usuário | Senha |
|---|---|
| `bibliotecario` | `biblio123` |

(Credenciais de estudo, criadas por `sql/seed.sql`. Nunca use senhas assim em um sistema real.)

### Voltar o banco ao estado inicial

```bash
php bin/resetar-banco.php
```

No Windows, pare o servidor (Ctrl+C) antes de rodar o comando.

---

## 2. Estrutura de pastas

```
├── public/                    ← única pasta acessível pelo navegador
│   ├── index.php              página inicial
│   ├── acervo.php             lista de livros com filtro (GET) e busca (JS)
│   ├── livro.php              detalhe de um livro (404 se não existir)
│   ├── reservar.php           formulário de reserva
│   ├── processar-reserva.php  recebe o POST: valida e grava com transação
│   ├── reserva-confirmada.php destino do redirecionamento (PRG)
│   ├── tema.php               grava a preferência de tema em cookie
│   ├── admin/                 área do bibliotecário (exige login)
│   │   ├── login.php  logout.php
│   │   ├── index.php          lista de livros
│   │   ├── livro-form.php     cadastrar / editar
│   │   ├── salvar-livro.php   INSERT ou UPDATE
│   │   ├── excluir-livro.php  DELETE (barrado se houver reservas)
│   │   ├── reservas.php       reservas com JOIN
│   │   └── atualizar-reserva.php  retirar / devolver / cancelar
│   ├── css/style.css
│   ├── js/                    relatorio.js, acervo.js, reserva.js, admin.js, tema.js
│   └── img/
├── src/                       ← código PHP que NÃO pode ser acessado por URL
│   ├── iniciar.php            incluído no topo de toda página
│   ├── db.php                 conexão PDO + criação automática do banco
│   ├── funcoes.php            escape, validação, redirecionamento, flash, CSRF
│   ├── auth.php               login com sessão
│   └── partes/                topo, rodapé e formulários reutilizados
├── sql/
│   ├── schema.sql             tabelas (SQLite)
│   ├── seed.sql               dados de exemplo
│   └── schema.mysql.sql       as mesmas tabelas em MySQL, para comparação
├── dados/                     arquivo do banco (gerado automaticamente)
├── bin/resetar-banco.php
└── etapas/                    o site em cada estágio da disciplina (veja etapas/README.md)
```

Por que `public/` e `src/` separados? O servidor só entrega ao navegador o que está em `public/`.
Assim, ninguém consegue abrir `src/db.php` pela URL. As páginas incluem esses arquivos pelo
**caminho no disco**: `require __DIR__ . '/../src/iniciar.php';` (Apostila PHP, 3.2).

---

## 3. Onde cada assunto aparece

### Apostila 01 — Web, HTTP e cliente/servidor

| Assunto | Onde ver |
|---|---|
| Requisição e resposta, métodos GET e POST | Filtro do acervo (GET) × formulário de reserva (POST) |
| Códigos de status | `paginaDeErro()` em `src/funcoes.php`; 302, 401, 403, 404, 405, 409 e 422 são usados no projeto (veja a seção 5) |
| HTTP sem estado, cookies e sessões | `src/iniciar.php` (`session_start`), `public/tema.php` (cookie) |
| Front-end × back-end | `public/js/reserva.js` valida no navegador; `public/processar-reserva.php` valida no servidor |

### Apostila 02 — HTML

| Assunto | Onde ver |
|---|---|
| Estrutura mínima, `<head>`, metadados | `src/partes/topo.php` |
| Semântica: `header`, `nav`, `main`, `section`, `article`, `footer`, `address` | `src/partes/topo.php`, `public/index.php`, `src/partes/rodape.php` |
| Listas `ul`, `ol`, `dl` | `public/index.php`, `public/livro.php` |
| Imagem com `figure`/`figcaption` e `alt` | `public/index.php` |
| Tabela com `caption`, `thead`, `th scope` | `public/acervo.php`, `public/admin/*.php` |
| Formulário: `label`, `fieldset`/`legend`, `select`, `radio`, `checkbox[]`, `date`, `textarea` | `src/partes/form-reserva.php` |
| Atributos `data-*` | Linhas da tabela em `public/acervo.php`; `data-confirmar` em `public/admin/index.php` |
| Acessibilidade: `aria-current`, `aria-live`, `aria-describedby`, `aria-invalid`, link "pular para o conteúdo" | `src/partes/topo.php`, `src/partes/form-reserva.php` |

### Apostila 03 — CSS (`public/css/style.css`)

| Assunto | Onde ver no arquivo |
|---|---|
| Variáveis (`:root`) e tema escuro | Seção 1 |
| `box-sizing`, margin × padding | Seção 2 e `.cartao` |
| Seletores de classe, descendente, atributo, pseudoclasse e pseudoelemento | `.menu a[aria-current]`, `.trilha li + li::before`, `tr:nth-child(even)`, `.erro-campo:empty` |
| Cascata e especificidade | Comentário em `input[aria-invalid="true"]` |
| Flexbox | Cabeçalho, menu, botões (seção 4) |
| Grid (inclusive `auto-fill` + `minmax`) | `.grade-cartoes`, `.numeros`, `.filtros` |
| Posicionamento `relative`, `absolute`, `sticky` | `.cartao-livro .selo`, `.pular-conteudo`, `.cabecalho` |
| Responsividade *mobile first* | Seção 11 (media queries com `min-width`) |
| Foco visível | `:focus-visible` (seção 3) |

### Apostila 04 — JavaScript fundamentos

| Assunto | Onde ver |
|---|---|
| Variáveis, tipos, operadores, `if`, `switch`, `for`, `for...of`, funções, arrays e objetos | `public/js/relatorio.js` (sem DOM) e `etapas/03-js-fundamentos/app.js` |
| Conversão de tipos (`Number()` sobre `dataset`) | `public/js/acervo.js` |

Abra o **Console** na página do acervo para ver o relatório gerado por `relatorio.js`.

### Apostila 05 — DOM, eventos e formulários

| Assunto | Onde ver |
|---|---|
| `querySelector`, `querySelectorAll`, `textContent`, `hidden` | `public/js/acervo.js` |
| Eventos `input`, `submit`, `click`; `preventDefault` | `public/js/reserva.js`, `public/js/tema.js`, `public/js/admin.js` |
| Mensagens junto aos campos, `focus()` no primeiro erro | `public/js/reserva.js` |
| Estado no JS, aparência no CSS (`dataset` / `classList`) | `public/js/tema.js` |
| *Progressive enhancement*: tudo funciona sem JavaScript | Desative o JS no navegador e use o site |

### Apostila PHP

| Módulo | Assunto | Onde ver |
|---|---|---|
| 1 | PHP gera HTML, `<?= ?>`, laços, arrays, escape com `htmlspecialchars` | Função `e()` em `src/funcoes.php`; todas as páginas |
| 2 | `$_GET`/`$_POST`, bloqueio de método (405), normalização → validação → escape | `public/processar-reserva.php`, `exigirPost()` |
| 2 | Radio, select e checkbox múltiplo contra lista permitida | `public/processar-reserva.php` |
| 3 | `require`, `require_once`, `__DIR__`, topo/rodapé | `src/iniciar.php`, `src/partes/` |
| 3 | Sessão × cookie | `src/auth.php` (sessão) × `public/tema.php` (cookie) |
| 3 | Post-Redirect-Get e mensagem flash | `redirecionar()`, `definirFlash()`, `public/reserva-confirmada.php` |
| 4 | Conexão PDO centralizada | `src/db.php` |
| 4 | Prepared statements | Todas as consultas com parâmetros; busca com `LIKE` em `public/acervo.php` |
| 4 | CRUD | `public/admin/` |
| 4 | Chave estrangeira com `ON DELETE RESTRICT` | `sql/schema.sql` e `public/admin/excluir-livro.php` |
| 4 | `JOIN` e `LEFT JOIN` | `SQL_LIVROS_COM_DISPONIBILIDADE` em `src/funcoes.php`; `public/admin/reservas.php` |
| 4 | Transação e bloqueio contra concorrência | `public/processar-reserva.php` |
| 5 | Login, `password_hash`, proteção CSRF | `src/auth.php`, `tokenCsrf()` / `verificarCsrf()` |

---

## 4. SQLite em vez de MySQL

As apostilas usam MySQL. Este projeto usa SQLite para que você rode tudo sem instalar um servidor de banco.
Os conceitos são os mesmos; mudam alguns detalhes:

| Apostila (MySQL) | Projeto (SQLite) | Por quê |
|---|---|---|
| `new PDO('mysql:host=...;dbname=...;charset=utf8mb4', usuario, senha)` | `new PDO('sqlite:caminho/arquivo.sqlite')` | O SQLite é um arquivo: não tem usuário, senha nem host |
| `INT UNSIGNED AUTO_INCREMENT` | `INTEGER PRIMARY KEY AUTOINCREMENT` | Sintaxe diferente, mesmo conceito |
| `ENUM(...)` | `TEXT CHECK (coluna IN (...))` | O SQLite não tem `ENUM` |
| Chaves estrangeiras sempre ativas (InnoDB) | `PRAGMA foreign_keys = ON` a cada conexão | Sem isso, o SQLite **ignora** `ON DELETE RESTRICT` |
| `beginTransaction()` + `SELECT ... FOR UPDATE` | `BEGIN IMMEDIATE` | O SQLite não trava linhas; ele trava a escrita no banco inteiro. A ideia (bloqueio pessimista) é a mesma |

Compare `sql/schema.sql` com `sql/schema.mysql.sql`. Para migrar para MySQL, basta trocar o schema e a função `banco()` em `src/db.php`.
O código das páginas não muda.

---

## 5. Roteiro de testes manuais

Faça cada teste com o **DevTools aberto na aba Network (Rede)** para ver o código de status.
Use `php bin/resetar-banco.php` para recomeçar do zero.

| # | Teste | Como fazer | Resultado esperado |
|---|---|---|---|
| 1 | Reserva válida | Reserve um livro disponível | **302** → página de confirmação |
| 2 | F5 após reservar | Na confirmação, aperte F5 | Nada é reenviado (PRG) |
| 3 | Validação no servidor | Desative o JavaScript, apague o `required` no DevTools e envie com nome vazio | **422**, mensagens nos campos e nada gravado |
| 4 | Reserva repetida | Reserve o mesmo livro duas vezes com o mesmo e-mail | **409**, "Já existe uma reserva..." |
| 5 | Livro esgotado | No DevTools, remova o `disabled` da opção "O Pequeno Príncipe" e envie | **409**, "Todos os exemplares... já foram reservados" |
| 6 | Data fora do prazo | Remova o `min` do campo de data e escolha uma data passada | **422** |
| 7 | Método errado | Abra `http://localhost:8000/processar-reserva.php` direto na barra de endereços | **405** |
| 8 | Livro inexistente | Abra `livro.php?id=999` | **404** |
| 9 | XSS | Reserve com o nome `<script>alert(1)</script>` e veja na área do bibliotecário | O texto aparece literalmente; nenhum alerta |
| 10 | SQL Injection | Busque no acervo por `' OR '1'='1` | Nenhum livro encontrado; nenhum erro |
| 11 | Área restrita | Abra `admin/index.php` sem login | **302** → tela de login |
| 12 | Login errado | Entre com senha errada | **401**, mensagem genérica |
| 13 | Cadastrar livro | Na área do bibliotecário, cadastre um livro | **302**, livro aparece no acervo |
| 14 | Editar livro | Altere o número de exemplares | Mesmo `id`, valor atualizado |
| 15 | Reduzir exemplares demais | Edite "Dom Casmurro" para 1 exemplar | **422**: há exemplares reservados |
| 16 | Exclusão restrita | Exclua "Dom Casmurro" (tem reservas) | **409**: a chave estrangeira impede |
| 17 | Exclusão permitida | Exclua o livro criado no teste 13 | **302**, livro removido |
| 18 | Ciclo da reserva | Em Reservas: registrar retirada → registrar devolução | Situação muda; o exemplar volta a ficar disponível |
| 19 | CSRF | No DevTools, altere o valor do campo oculto `csrf` e envie qualquer formulário | **403** |
| 20 | Sem JavaScript | Desative o JS e navegue, filtre, reserve e troque o tema | Tudo continua funcionando |
| 21 | Responsividade | Modo dispositivo do DevTools (ex.: 360 px) | Menu e grades se reorganizam; a tabela rola na horizontal |
| 22 | Teclado | Navegue só com Tab / Shift+Tab / Enter | Foco sempre visível; "Pular para o conteúdo" aparece no primeiro Tab |

---

## 6. Limites deste projeto e próximos passos

Este é um projeto de estudo. Antes de colocar algo assim na internet, faltaria:

- desligar `display_errors` e registrar os erros em log;
- usar HTTPS e o atributo `secure` nos cookies;
- limitar tentativas de login (*rate limiting*);
- cuidar da LGPD: informar por que nome e e-mail são coletados e por quanto tempo ficam guardados;
- confirmar reservas por e-mail.

### Desafios para praticar

1. **Lista de espera:** quando o livro estiver esgotado, permitir entrar em uma fila.
2. **Paginação** do acervo com `LIMIT` e `OFFSET` (`acervo.php?pagina=2`).
3. **Exportar reservas** em CSV na área do bibliotecário.
4. **Gêneros de interesse em tabela própria (N:N)**, no lugar do texto separado por vírgulas.
5. **Nova reserva após devolução:** hoje a restrição `UNIQUE(livro_id, email)` impede que a mesma pessoa reserve o mesmo livro de novo, mesmo depois de devolvê-lo. Como resolver?
6. **Capa do livro por upload** de imagem.
