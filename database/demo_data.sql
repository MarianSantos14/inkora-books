USE inkora_books;
SET NAMES utf8mb4;
-- Dados demonstrativos. A senha das contas criadas aqui e: Leitura123!
START TRANSACTION;

INSERT INTO usuario (nome, email, senha, tipo) VALUES
('Administrador Demo', 'admin.demo@example.test', '$2y$10$ZCyIlboQnkGuMzYodJdKaeL67mFMavPAPrWdW.dk0dy7rwkI300My', 'admin'),
('Holly Black', 'holly.black.demo@example.test', '$2y$10$ZCyIlboQnkGuMzYodJdKaeL67mFMavPAPrWdW.dk0dy7rwkI300My', 'autor'),
('Lynn Painter', 'lynn.painter.demo@example.test', '$2y$10$ZCyIlboQnkGuMzYodJdKaeL67mFMavPAPrWdW.dk0dy7rwkI300My', 'autor'),
('Colleen Hoover', 'colleen.hoover.demo@example.test', '$2y$10$ZCyIlboQnkGuMzYodJdKaeL67mFMavPAPrWdW.dk0dy7rwkI300My', 'autor')
ON DUPLICATE KEY UPDATE nome=VALUES(nome), senha=VALUES(senha), tipo=VALUES(tipo);

INSERT INTO livro (titulo, autor, ano_publicacao, usuario_id)
SELECT s.titulo, s.autor, s.ano, u.id FROM (
  SELECT 'O Príncipe Cruel' AS titulo, 'Holly Black' AS autor, 2018 AS ano, 'holly.black.demo@example.test' AS email
  UNION ALL SELECT 'O Rei Perverso', 'Holly Black', 2019, 'holly.black.demo@example.test'
  UNION ALL SELECT 'A Rainha do Nada', 'Holly Black', 2019, 'holly.black.demo@example.test'
  UNION ALL SELECT 'Better Than the Movies', 'Lynn Painter', 2021, 'lynn.painter.demo@example.test'
  UNION ALL SELECT 'The Do-Over', 'Lynn Painter', 2022, 'lynn.painter.demo@example.test'
  UNION ALL SELECT 'Betting on You', 'Lynn Painter', 2023, 'lynn.painter.demo@example.test'
  UNION ALL SELECT 'It Ends with Us', 'Colleen Hoover', 2016, 'colleen.hoover.demo@example.test'
  UNION ALL SELECT 'Verity', 'Colleen Hoover', 2018, 'colleen.hoover.demo@example.test'
  UNION ALL SELECT 'Reminders of Him', 'Colleen Hoover', 2022, 'colleen.hoover.demo@example.test'
) s JOIN usuario u ON u.email = s.email
WHERE NOT EXISTS (SELECT 1 FROM livro l WHERE l.titulo = s.titulo AND l.autor = s.autor);

INSERT INTO cliente (nome, email, senha) VALUES
('Ana Clara Souza', 'leitor01@example.test', '$2y$10$ZCyIlboQnkGuMzYodJdKaeL67mFMavPAPrWdW.dk0dy7rwkI300My'),
('Bruno Martins', 'leitor02@example.test', '$2y$10$ZCyIlboQnkGuMzYodJdKaeL67mFMavPAPrWdW.dk0dy7rwkI300My'),
('Camila Rocha', 'leitor03@example.test', '$2y$10$ZCyIlboQnkGuMzYodJdKaeL67mFMavPAPrWdW.dk0dy7rwkI300My'),
('Diego Ferreira', 'leitor04@example.test', '$2y$10$ZCyIlboQnkGuMzYodJdKaeL67mFMavPAPrWdW.dk0dy7rwkI300My'),
('Fernanda Lima', 'leitor05@example.test', '$2y$10$ZCyIlboQnkGuMzYodJdKaeL67mFMavPAPrWdW.dk0dy7rwkI300My')
ON DUPLICATE KEY UPDATE nome=VALUES(nome), senha=VALUES(senha);

INSERT INTO estante (cliente_id, livro_id, status)
SELECT c.id, l.id, s.status FROM (
  SELECT 'leitor01@example.test' AS email, 'O Príncipe Cruel' AS titulo, 'Holly Black' AS autor, 'lido' AS status
  UNION ALL SELECT 'leitor01@example.test', 'Better Than the Movies', 'Lynn Painter', 'lendo'
  UNION ALL SELECT 'leitor01@example.test', 'It Ends with Us', 'Colleen Hoover', 'quero ler'
  UNION ALL SELECT 'leitor02@example.test', 'O Rei Perverso', 'Holly Black', 'lido'
  UNION ALL SELECT 'leitor02@example.test', 'The Do-Over', 'Lynn Painter', 'lendo'
  UNION ALL SELECT 'leitor02@example.test', 'Verity', 'Colleen Hoover', 'quero ler'
  UNION ALL SELECT 'leitor03@example.test', 'A Rainha do Nada', 'Holly Black', 'lido'
  UNION ALL SELECT 'leitor03@example.test', 'Betting on You', 'Lynn Painter', 'lendo'
  UNION ALL SELECT 'leitor03@example.test', 'Reminders of Him', 'Colleen Hoover', 'quero ler'
  UNION ALL SELECT 'leitor04@example.test', 'Better Than the Movies', 'Lynn Painter', 'lido'
  UNION ALL SELECT 'leitor04@example.test', 'It Ends with Us', 'Colleen Hoover', 'lendo'
  UNION ALL SELECT 'leitor04@example.test', 'O Príncipe Cruel', 'Holly Black', 'quero ler'
  UNION ALL SELECT 'leitor05@example.test', 'Verity', 'Colleen Hoover', 'lido'
  UNION ALL SELECT 'leitor05@example.test', 'Betting on You', 'Lynn Painter', 'lendo'
  UNION ALL SELECT 'leitor05@example.test', 'O Rei Perverso', 'Holly Black', 'quero ler'
) s JOIN cliente c ON c.email = s.email
JOIN livro l ON l.titulo = s.titulo AND l.autor = s.autor
ON DUPLICATE KEY UPDATE status=VALUES(status);

INSERT INTO avaliacao_livro (cliente_id, livro_id, nota, comentario)
SELECT c.id, e.livro_id, 3 + MOD(c.id + e.livro_id, 3),
       CASE MOD(c.id + e.livro_id, 3)
         WHEN 0 THEN 'Uma leitura que me prendeu do começo ao fim.'
         WHEN 1 THEN 'Gostei muito da história e dos personagens.'
         ELSE 'Uma leitura marcante para a minha estante.'
       END
FROM estante e
JOIN cliente c ON c.id=e.cliente_id
WHERE e.status='lido'
  AND c.email IN ('leitor01@example.test','leitor02@example.test','leitor03@example.test','leitor04@example.test','leitor05@example.test')
ON DUPLICATE KEY UPDATE nota=VALUES(nota),comentario=VALUES(comentario);

INSERT IGNORE INTO autor_seguidor (cliente_id, autor_id)
SELECT c.id,u.id FROM cliente c JOIN usuario u ON (
  (c.email='leitor01@example.test' AND u.email='holly.black.demo@example.test') OR
  (c.email='leitor02@example.test' AND u.email='lynn.painter.demo@example.test') OR
  (c.email='leitor03@example.test' AND u.email='colleen.hoover.demo@example.test') OR
  (c.email='leitor04@example.test' AND u.email='holly.black.demo@example.test') OR
  (c.email='leitor05@example.test' AND u.email='lynn.painter.demo@example.test')
) WHERE u.tipo='autor';

COMMIT;
