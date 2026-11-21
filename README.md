# Inkora Books

Aplicação de catálogo e estante de leitura em PHP puro, MySQL/MariaDB, HTML, Tailwind via CDN e JavaScript leve.

## Instalação no XAMPP

1. Copie a pasta `inkora-books` para `C:\xampp\htdocs\`.
2. Inicie Apache e MySQL no painel do XAMPP.
3. Em uma instalação nova, importe `schema.sql` no phpMyAdmin. Em um banco existente, aplique as migrations pendentes em ordem, da v2 até a v4.
4. Confira usuário e senha do MySQL em `config/database.php` (o padrão é `root` sem senha).
5. Acesse `http://localhost/inkora-books/setup.php` para criar a primeira conta administradora.
   Para carregar dados de demonstração, importe `database/demo_data.sql` no phpMyAdmin. A conta administradora demonstrativa usa `admin.demo@example.test`; todas as contas demonstrativas usam a senha `Leitura123!`.
6. Entre pela tela de login. O administrador pode criar autores e outros administradores na página **Equipe**.

## Recursos sociais de autores

- Perfis de autor exibem livros em cards com capas arredondadas, seguidores e avaliaÃ§Ãµes da comunidade.
- Clientes podem seguir autores e avaliar livros com nota de 1 a 5 estrelas apÃ³s marcar a leitura como **lido**.
- Em bancos existentes, aplique `migrations/2026-11-21-author-social.sql` antes de abrir as pÃ¡ginas. Em instalaÃ§Ãµes novas, `schema.sql` jÃ¡ cria as tabelas; `database/demo_data.sql` inclui avaliaÃ§Ãµes e autores seguidos de demonstraÃ§Ã£o.

## Funcionalidades

- Catálogo público de livros e lista pública de clientes; cadastro e alterações exigem login.
- Clientes podem criar conta e atualizar cada livro na estante para **quero ler**, **lendo** ou **lido**.
- Autores e administradores podem cadastrar, editar e remover livros e clientes.
- Apenas administradores podem criar e remover usuários da equipe.
- Administradores têm telas de visualização detalhada e CRUD de clientes e usuários.
- Painel administrativo com leituras concluídas e livros na lista de desejos.
- Usuários logados veem, em cada livro, as contagens de pessoas que marcaram lido, lendo ou quero ler.
- Senhas com `password_hash`, consultas preparadas PDO, validação de e-mail e formulários com token CSRF.
- Alternância de tema claro/escuro salva no navegador.
- Fotos de perfil para clientes e equipe; capas de livros podem ser enviadas ou informadas por URL. Ao substituir ou remover uma imagem local, o arquivo anterior é apagado.
- Cadastro de livros por ISBN: consulta exclusivamente a Open Library. Os resultados encontrados ficam em cache local por 30 dias para reduzir chamadas repetidas; primeiro autor, editora, ano, paginas, sinopse e capa sao preenchidos quando disponiveis.
- Leitura de ISBN por camera para codigos EAN-13; requer navegador com BarcodeDetector e acesso ao site por localhost ou HTTPS.
- Cadastro de livros em duas etapas: pesquise por titulo, ISBN ou autor, escolha um resultado e revise os campos no formulario antes de salvar.
- A busca por ISBN abre um painel de diagnostico com a resposta bruta e o status HTTP da Open Library; tambem permite uma consulta manual sem cache.
- O seletor de capa usa uma prévia de livro com botão de lápis, substituindo o campo de arquivo visível.

As páginas estão separadas em diretórios (`livros/`, `clientes/`, `usuarios/` e `estante/`) e usam um cabeçalho e rodapé compartilhados em `includes/`.

