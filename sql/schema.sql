-- =====================================================================
-- Biblioteca do Bairro — esquema do banco (SQLite)
-- Apostila PHP, Módulo 4.1 — Modelo relacional e integridade referencial
--
-- Este arquivo é executado automaticamente por src/db.php quando o
-- arquivo dados/biblioteca.sqlite ainda não existe.
-- Para comparar com a sintaxe do MySQL, veja sql/schema.mysql.sql.
-- =====================================================================

-- Livros do acervo. Cada livro possui um número de exemplares físicos.
CREATE TABLE IF NOT EXISTS livros (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    titulo         TEXT    NOT NULL,
    autor          TEXT    NOT NULL,
    genero         TEXT    NOT NULL
                   CHECK (genero IN ('ficcao', 'romance', 'fantasia', 'tecnologia', 'infantil', 'biografia')),
    ano_publicacao INTEGER NOT NULL,
    sinopse        TEXT    NOT NULL DEFAULT '',
    exemplares     INTEGER NOT NULL CHECK (exemplares >= 1)
);

-- Reservas feitas pelos visitantes (relação 1:N — um livro, muitas reservas).
CREATE TABLE IF NOT EXISTS reservas (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    livro_id          INTEGER NOT NULL,
    nome              TEXT    NOT NULL,
    email             TEXT    NOT NULL,
    data_retirada     TEXT    NOT NULL,              -- formato AAAA-MM-DD
    periodo           TEXT    NOT NULL CHECK (periodo IN ('manha', 'tarde', 'noite')),
    -- Simplificação didática: os gêneros de interesse ficam em um texto
    -- separado por vírgulas ("romance,fantasia"). Em um modelo relacional
    -- completo, isso seria uma tabela N:N (reserva_genero).
    generos_interesse TEXT    NOT NULL DEFAULT '',
    observacoes       TEXT    NOT NULL DEFAULT '',
    -- ativa → retirada → devolvida   |   ativa → cancelada
    situacao          TEXT    NOT NULL DEFAULT 'ativa'
                      CHECK (situacao IN ('ativa', 'retirada', 'devolvida', 'cancelada')),
    criado_em         TEXT    NOT NULL DEFAULT (datetime('now', 'localtime')),

    -- Chave estrangeira: não existe reserva de livro inexistente.
    -- ON DELETE RESTRICT: o banco recusa excluir um livro que tenha reservas.
    -- Atenção: no SQLite isso só funciona com "PRAGMA foreign_keys = ON"
    -- (ativado a cada conexão em src/db.php).
    CONSTRAINT fk_reserva_livro FOREIGN KEY (livro_id)
        REFERENCES livros (id) ON DELETE RESTRICT,

    -- A mesma pessoa (e-mail) não pode reservar o mesmo livro duas vezes.
    CONSTRAINT uq_reserva_livro_email UNIQUE (livro_id, email)
);

-- Usuários da área administrativa (bibliotecários).
-- A senha NUNCA é guardada em texto puro: guardamos o resultado de password_hash().
CREATE TABLE IF NOT EXISTS usuarios (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    login      TEXT NOT NULL UNIQUE,
    senha_hash TEXT NOT NULL
);
