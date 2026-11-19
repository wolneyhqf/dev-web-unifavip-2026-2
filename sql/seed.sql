-- =====================================================================
-- Biblioteca do Bairro — dados de exemplo
-- Executado logo após o schema.sql na criação do banco.
-- =====================================================================

-- Usuário didático da área administrativa
--   login: bibliotecario
--   senha: biblio123
-- O valor abaixo foi gerado com: php -r 'echo password_hash("biblio123", PASSWORD_DEFAULT);'
INSERT INTO usuarios (login, senha_hash) VALUES
('bibliotecario', '$2y$12$zRkOfRdkcpt/KXs/oApZo.aj9hLenutzhUT84HFfpr37kcRDXzCny');

INSERT INTO livros (id, titulo, autor, genero, ano_publicacao, sinopse, exemplares) VALUES
(1,  'Dom Casmurro', 'Machado de Assis', 'romance', 1899,
     'Bentinho, já idoso, revisita a juventude e o casamento com Capitu, tentando provar a si mesmo uma desconfiança que nunca se resolve.', 3),
(2,  'Memórias Póstumas de Brás Cubas', 'Machado de Assis', 'romance', 1881,
     'Um narrador que já morreu conta a própria vida com ironia e sem pressa, zombando de si e da sociedade da época.', 2),
(3,  'O Alienista', 'Machado de Assis', 'ficcao', 1882,
     'O médico Simão Bacamarte funda um hospício em Itaguaí e passa a internar cada vez mais moradores em nome da ciência.', 2),
(4,  'Triste Fim de Policarpo Quaresma', 'Lima Barreto', 'romance', 1915,
     'Um funcionário público patriota tenta reformar o país com ideias sinceras e ingênuas, e colhe uma sucessão de decepções.', 2),
(5,  'O Cortiço', 'Aluísio Azevedo', 'romance', 1890,
     'A vida coletiva de um cortiço no Rio de Janeiro mostra como o ambiente molda e transforma seus moradores.', 2),
(6,  'A Máquina do Tempo', 'H. G. Wells', 'ficcao', 1895,
     'Um inventor viaja ao futuro distante e encontra uma humanidade dividida em dois povos muito diferentes.', 1),
(7,  'Alice no País das Maravilhas', 'Lewis Carroll', 'fantasia', 1865,
     'Ao seguir um coelho apressado, Alice cai em um mundo onde a lógica funciona de um jeito todo próprio.', 2),
(8,  'Reinações de Narizinho', 'Monteiro Lobato', 'infantil', 1931,
     'No Sítio do Picapau Amarelo, Narizinho e Emília vivem aventuras que misturam o cotidiano com o fantástico.', 3),
(9,  'O Pequeno Príncipe', 'Antoine de Saint-Exupéry', 'infantil', 1943,
     'Um aviador perdido no deserto conhece um pequeno viajante vindo de outro planeta, com perguntas que ninguém sabe responder.', 2),
(10, 'Quarto de Despejo', 'Carolina Maria de Jesus', 'biografia', 1960,
     'O diário de uma catadora de papel na favela do Canindé, em São Paulo, escrito com franqueza e força literária.', 2),
(11, 'Código Limpo', 'Robert C. Martin', 'tecnologia', 2008,
     'Princípios e práticas para escrever código legível, fácil de manter e de testar.', 1);

-- Reservas de exemplo.
-- "O Pequeno Príncipe" (id 9) fica ESGOTADO: 2 exemplares e 2 reservas ativas.
-- "Dom Casmurro" (id 1) tem reservas, então sua exclusão será barrada (409).
INSERT INTO reservas (livro_id, nome, email, data_retirada, periodo, generos_interesse, observacoes, situacao) VALUES
(1, 'Ana Souza',      'ana.souza@exemplo.com',   date('now', 'localtime', '+2 days'), 'manha', 'romance,biografia', '', 'ativa'),
(1, 'Bruno Lima',     'bruno.lima@exemplo.com',  date('now', 'localtime', '-5 days'), 'tarde', '', 'Vou buscar com minha filha.', 'retirada'),
(9, 'Carla Mendes',   'carla.m@exemplo.com',     date('now', 'localtime', '+1 days'), 'noite', 'infantil,fantasia', '', 'ativa'),
(9, 'Diego Ferreira', 'diego.f@exemplo.com',     date('now', 'localtime', '+3 days'), 'manha', 'infantil', '', 'ativa'),
(6, 'Elisa Rocha',    'elisa.rocha@exemplo.com', date('now', 'localtime', '-20 days'), 'tarde', 'ficcao,tecnologia', '', 'devolvida'),
(3, 'Fábio Nunes',    'fabio.nunes@exemplo.com', date('now', 'localtime', '+4 days'), 'tarde', '', '', 'cancelada');
