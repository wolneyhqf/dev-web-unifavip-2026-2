-- =====================================================================
-- Biblioteca do Bairro — esquema EQUIVALENTE em MySQL (apenas referência)
--
-- O projeto usa SQLite (sql/schema.sql) para rodar sem instalar um
-- servidor de banco. Este arquivo mostra como as mesmas tabelas ficariam
-- no MySQL, para comparação com a Apostila PHP, Módulo 4.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS biblioteca CHARACTER SET utf8mb4;
USE biblioteca;

CREATE TABLE IF NOT EXISTS livros (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo         VARCHAR(150) NOT NULL,
    autor          VARCHAR(120) NOT NULL,
    genero         ENUM('ficcao', 'romance', 'fantasia', 'tecnologia', 'infantil', 'biografia') NOT NULL,
    ano_publicacao SMALLINT UNSIGNED NOT NULL,
    sinopse        TEXT NOT NULL,
    exemplares     INT UNSIGNED NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reservas (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    livro_id          INT UNSIGNED NOT NULL,
    nome              VARCHAR(120) NOT NULL,
    email             VARCHAR(190) NOT NULL,
    data_retirada     DATE NOT NULL,
    periodo           ENUM('manha', 'tarde', 'noite') NOT NULL,
    generos_interesse VARCHAR(200) NOT NULL DEFAULT '',
    observacoes       VARCHAR(300) NOT NULL DEFAULT '',
    situacao          ENUM('ativa', 'retirada', 'devolvida', 'cancelada') NOT NULL DEFAULT 'ativa',
    criado_em         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reserva_livro FOREIGN KEY (livro_id)
        REFERENCES livros (id) ON DELETE RESTRICT,
    CONSTRAINT uq_reserva_livro_email UNIQUE (livro_id, email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS usuarios (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    login      VARCHAR(60) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- Diferença importante no controlador de reserva:
--   MySQL : START TRANSACTION; SELECT exemplares FROM livros WHERE id = ? FOR UPDATE; ...
--   SQLite: BEGIN IMMEDIATE;   SELECT exemplares FROM livros WHERE id = ?;            ...
-- No MySQL a trava é por LINHA; no SQLite, BEGIN IMMEDIATE trava a ESCRITA no
-- banco inteiro. Nos dois casos a ideia é a mesma: bloqueio pessimista.
