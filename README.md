![Páginas do projeto](assets/docs/pagina.png)

<div align="center">

<img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" />
<img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
<img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white" />
<img src="https://img.shields.io/badge/JavaScript-ES6-D97706?style=for-the-badge&logo=javascript&logoColor=white" />

<h1>Inkora Books</h1>

</div>

**Link:** https://inkorabooks.page.gd/

### Sobre o projeto

O **Inkora Books** é uma aplicação web desenvolvida com o objetivo de criar uma comunidade literária, permitindo que leitores descubram livros, organizem suas leituras e compartilhem suas experiências.

A plataforma oferece um catálogo de livros, estantes pessoais, perfis de leitores e autores, avaliações e interação entre os usuários.

O projeto foi desenvolvido como **trabalho do 4º bimestre do curso técnico de Informática para Internet da ETEC**, colocando em prática os conhecimentos adquiridos durante o curso, especialmente no desenvolvimento web e na integração com banco de dados.

**Tecnologias principais:** PHP, MySQL, HTML, CSS e JavaScript.

---

### Funcionalidades

#### Catálogo de livros
- Visualização do catálogo de livros.
- Pesquisa e consulta de informações literárias.
- Cadastro, edição e exclusão de livros.
- Cadastro de livros por título, autor ou ISBN.
- Integração com a API da Open Library para consulta de informações e capas.

#### Estante pessoal
- Organização de livros em três categorias:
  - Quero ler.
  - Lendo.
  - Lido.
- Acompanhamento das leituras.
- Visualização das atividades de leitura da comunidade.

#### Comunidade
- Cadastro e autenticação de leitores.
- Perfis personalizados.
- Perfis de autores.
- Possibilidade de seguir autores.
- Avaliações de livros com notas de 1 a 5 estrelas e comentários.
- Galeria de imagens nos perfis de autores.

#### Administração
- Painel administrativo.
- Gerenciamento de clientes, autores e livros.
- Cadastro e gerenciamento de usuários da equipe.
- Visualização de informações e atividades da comunidade.
- Controle de permissões de acesso.

![Páginas do projeto](assets/docs/pagina2.png)

#### Personalização
- Alternância entre tema claro e escuro.
- Upload de fotos de perfil e capas de livros.
- Interface adaptável a diferentes dispositivos.
- Carrossel animado de capas de livros.

---

### Como rodar o projeto

#### Pré-requisitos

- PHP 8.2 ou superior.
- MySQL ou MariaDB.
- XAMPP ou outro servidor local.
- Navegador atualizado.

#### 1️⃣ Clone o repositório

```bash
git clone https://github.com/MarianSantos14/inkora-books.git
```

#### 2️⃣ Mova o projeto

Coloque a pasta `inkora-books` dentro do diretório:

```text
C:\xampp\htdocs\
```

#### 3️⃣ Importe o banco de dados

1. Inicie o **Apache** e o **MySQL** pelo painel do XAMPP.
2. Acesse o phpMyAdmin: http://localhost/phpmyadmin.
3. Clique na opção **Importar**.
4. Selecione o arquivo **[`database/schema.sql`](database/schema.sql)** 
5. Clique em **Importar** ou **Executar** para concluir.

#### 4. Configure o ambiente

Crie um arquivo chamado `.env` na raiz do projeto com as configurações do banco:

```env
DB_HOST=localhost
DB_NAME=inkora_books
DB_CHARSET=utf8mb4
DB_USER=root
DB_PASS=
```

Ajuste os valores conforme a configuração do seu MySQL. No XAMPP padrão, o usuário costuma ser `root` e a senha fica vazia.

#### 5. Crie o primeiro administrador

1. Inicie o Apache e o MySQL pelo XAMPP.
2. Acesse o phpMyAdmin.
3. Importe o arquivo `database/schema.sql`.
4. Para carregar dados de demonstração, importe `database/demo_data.sql`.

#### 5️⃣ Crie o primeiro administrador

```text
http://localhost/inkora-books/setup.php
```

Preencha o formulário para cadastrar seu nome, e-mail e senha de administrador.

Obs: Se você importou `database/demo_data.sql`, essa etapa não é necessária: use a conta de demonstração informada acima.

#### 6️⃣ Acesse o projeto

```text
http://localhost/inkora-books/
```

---

### Aprendizados

Durante o desenvolvimento do Inkora Books, foram trabalhados conceitos importantes de desenvolvimento web:

- Desenvolvimento backend utilizando PHP.
- Integração com banco de dados MySQL.
- Operações CRUD (Create, Read, Update e Delete).
- Autenticação e gerenciamento de sessões.
- Controle de permissões de usuários.
- Consultas preparadas utilizando PDO.
- Upload e gerenciamento de imagens.
- Consumo de API externa.
- Organização e reutilização de componentes.
- Segurança com validação de dados e proteção CSRF.
- Desenvolvimento de interfaces e interações com JavaScript.

---

### Dados fictícios e demonstração

Para fins de demonstração e desenvolvimento, a plataforma utiliza dados fictícios.

- **Clientes:** todos os perfis de leitores são fictícios. As fotos utilizadas são de atores de séries que gosto, apenas para representar visualmente os usuários da plataforma.
- **Autores:** são escritores reais, porém os perfis, informações complementares e interações apresentados no projeto são fictícios e foram criados para fins demonstrativos.

Os dados não representam contas ou interações reais dessas pessoas.
