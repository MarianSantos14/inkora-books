-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23/11/2026 às 23:02
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `inkora_books`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `autor_seguidor`
--

CREATE TABLE `autor_seguidor` (
  `cliente_id` int(10) UNSIGNED NOT NULL,
  `autor_id` int(10) UNSIGNED NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `autor_seguidor`
--

INSERT INTO `autor_seguidor` (`cliente_id`, `autor_id`, `criado_em`) VALUES
(1, 10, '2026-11-21 13:30:01'),
(2, 3, '2026-11-21 13:32:33'),
(3, 10, '2026-11-21 13:32:33'),
(4, 10, '2026-11-21 13:32:33'),
(5, 1, '2026-11-21 13:32:33'),
(6, 1, '2026-11-21 13:32:33');

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacao_livro`
--

CREATE TABLE `avaliacao_livro` (
  `id` int(10) UNSIGNED NOT NULL,
  `cliente_id` int(10) UNSIGNED NOT NULL,
  `livro_id` int(10) UNSIGNED NOT NULL,
  `nota` tinyint(3) UNSIGNED NOT NULL,
  `comentario` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Despejando dados para a tabela `avaliacao_livro`
--

INSERT INTO `avaliacao_livro` (`id`, `cliente_id`, `livro_id`, `nota`, `comentario`, `criado_em`, `atualizado_em`) VALUES
(1, 5, 4, 5, 'Uma leitura marcante para a minha estante.', '2026-11-21 13:16:59', '2026-11-21 13:16:59'),
(2, 4, 19, 4, 'Uma leitura que me prendeu do começo ao fim.', '2026-11-21 13:16:59', '2026-11-21 13:16:59'),
(3, 3, 19, 5, 'Uma leitura que me prendeu do começo ao fim.', '2026-11-21 13:16:59', '2026-11-21 13:16:59'),
(4, 6, 17, 4, 'Uma leitura marcante para a minha estante.', '2026-11-21 13:16:59', '2026-11-21 13:16:59'),
(5, 3, 4, 4, 'Uma leitura marcante para a minha estante.', '2026-11-21 13:16:59', '2026-11-21 13:16:59'),
(6, 2, 3, 4, 'Uma leitura marcante para a minha estante.', '2026-11-21 13:16:59', '2026-11-21 13:16:59'),
(7, 1, 5, 3, 'Uma leitura marcante para a minha estante.', '2026-11-21 13:16:59', '2026-11-21 13:16:59'),
(8, 1, 22, 3, 'Gostei muito da história e dos personagens.', '2026-11-21 13:16:59', '2026-11-21 13:16:59'),
(9, 2, 1, 4, 'Uma leitura que me prendeu do começo ao fim.', '2026-11-21 13:16:59', '2026-11-21 13:16:59'),
(10, 6, 8, 3, 'Uma leitura que me prendeu do começo ao fim.', '2026-11-21 13:16:59', '2026-11-21 13:16:59'),
(11, 3, 3, 3, 'Uma leitura que me prendeu do começo ao fim.', '2026-11-21 13:16:59', '2026-11-21 13:16:59'),
(12, 3, 22, 3, 'Uma leitura que me prendeu do começo ao fim.', '2026-11-21 13:16:59', '2026-11-21 13:16:59'),
(16, 1, 8, 5, 'Uma leitura que me prendeu do começo ao fim.', '2026-11-21 13:57:49', '2026-11-21 13:57:49');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(30) DEFAULT NULL,
  `foto` varchar(500) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id`, `nome`, `email`, `senha`, `telefone`, `foto`, `criado_em`, `atualizado_em`) VALUES
(1, 'Elisa Oliveira', 'mariana.carvalho1409@outlook.com', '$2y$10$AFwsBasQCiSdQe1C2sEzjONfU5CtrCbEACLZwFon0Pueuqte6m0jG', '15991008688', 'assets/uploads/clientes/0e5fdc8d7bf055d20391636e3f739753.jpg', '2026-11-20 16:21:17', '2026-11-20 03:00:00'),
(2, 'Regina Machado', 'leitor01@leitor.com', '$2y$10$ZCyIlboQnkGuMzYodJdKaeL67mFMavPAPrWdW.dk0dy7rwkI300My', '', 'assets/uploads/clientes/7039922b81bec4ed6f89d32f8edc1fa5.jpg', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(3, 'Pedro João', 'leitor02@leitor.com', '$2y$10$ZCyIlboQnkGuMzYodJdKaeL67mFMavPAPrWdW.dk0dy7rwkI300My', '', 'assets/uploads/clientes/a811eb73833082007e36a511e920c39c.jpg', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(4, 'Bella Machado', 'leitor03@leitor.com', '$2y$10$ZCyIlboQnkGuMzYodJdKaeL67mFMavPAPrWdW.dk0dy7rwkI300My', '', 'assets/uploads/clientes/6325a5ef4d1be54bed3fcbe944da360e.jpg', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(5, 'Jonathas Logan', 'leitor04@leitor.com', '$2y$10$ZCyIlboQnkGuMzYodJdKaeL67mFMavPAPrWdW.dk0dy7rwkI300My', '', 'assets/uploads/clientes/d9bf1de8c289ae17edea7b6e87fa5b0e.jpg', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(6, 'Lara Silva', 'leitor05@leitor.com', '$2y$10$ZCyIlboQnkGuMzYodJdKaeL67mFMavPAPrWdW.dk0dy7rwkI300My', '', 'assets/uploads/clientes/245302fd5960d29d8f4dbad4d70bf7d9.jpg', '2026-11-20 17:26:55', '2026-11-20 03:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `compra`
--

CREATE TABLE `compra` (
  `id` int(10) UNSIGNED NOT NULL,
  `cliente_id` int(10) UNSIGNED NOT NULL,
  `livro_id` int(10) UNSIGNED NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `comprado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estante`
--

CREATE TABLE `estante` (
  `id` int(10) UNSIGNED NOT NULL,
  `cliente_id` int(10) UNSIGNED NOT NULL,
  `livro_id` int(10) UNSIGNED NOT NULL,
  `status` enum('quero ler','lendo','lido') NOT NULL DEFAULT 'quero ler',
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `estante`
--

INSERT INTO `estante` (`id`, `cliente_id`, `livro_id`, `status`, `atualizado_em`, `criado_em`) VALUES
(1, 2, 1, 'lido', '2026-11-20 22:09:00', '2026-11-20 03:00:00'),
(2, 2, 4, 'lendo', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(3, 2, 7, 'quero ler', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(4, 3, 2, 'lido', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(5, 3, 5, 'lido', '2026-11-20 22:07:55', '2026-11-20 03:00:00'),
(6, 3, 8, 'quero ler', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(7, 4, 3, 'lido', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(8, 4, 6, 'lendo', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(9, 4, 9, 'quero ler', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(10, 5, 4, 'lido', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(11, 5, 7, 'lendo', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(12, 5, 1, 'quero ler', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(13, 6, 8, 'lido', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(14, 6, 6, 'lendo', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(15, 6, 2, 'quero ler', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(17, 1, 18, 'lido', '2026-11-20 21:08:23', '2026-11-20 03:00:00'),
(19, 1, 23, 'lendo', '2026-11-20 21:40:31', '2026-11-20 03:00:00'),
(20, 1, 22, 'lido', '2026-11-20 21:40:27', '2026-11-20 03:00:00'),
(21, 1, 21, 'lido', '2026-11-20 21:40:21', '2026-11-20 03:00:00'),
(22, 1, 20, 'quero ler', '2026-11-20 21:41:27', '2026-11-20 03:00:00'),
(27, 1, 9, 'quero ler', '2026-11-20 21:40:41', '2026-11-20 03:00:00'),
(28, 1, 1, 'lendo', '2026-11-20 23:23:37', '2026-11-20 03:00:00'),
(30, 1, 17, 'quero ler', '2026-11-20 21:40:58', '2026-11-20 03:00:00'),
(31, 1, 19, 'quero ler', '2026-11-20 21:41:05', '2026-11-20 03:00:00'),
(32, 1, 2, 'quero ler', '2026-11-20 21:41:19', '2026-11-20 03:00:00'),
(34, 3, 1, 'lido', '2026-11-20 22:08:06', '2026-11-20 03:00:00'),
(35, 3, 3, 'lido', '2026-11-20 22:07:21', '2026-11-20 03:00:00'),
(37, 3, 4, 'lido', '2026-11-20 22:07:58', '2026-11-20 03:00:00'),
(39, 3, 9, 'lendo', '2026-11-20 22:07:49', '2026-11-20 03:00:00'),
(45, 3, 6, 'lendo', '2026-11-20 22:08:24', '2026-11-20 03:00:00'),
(49, 1, 3, 'lendo', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(50, 1, 4, 'quero ler', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(51, 1, 5, 'lido', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(52, 1, 6, 'lendo', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(53, 1, 7, 'quero ler', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(54, 1, 8, 'lido', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(55, 2, 2, 'quero ler', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(56, 2, 3, 'lido', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(57, 2, 5, 'lendo', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(58, 2, 6, 'quero ler', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(59, 2, 8, 'lido', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(60, 2, 9, 'lendo', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(61, 2, 17, 'quero ler', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(62, 2, 18, 'lido', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(63, 2, 19, 'lendo', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(64, 2, 20, 'quero ler', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(65, 3, 7, 'lido', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(66, 3, 17, 'lendo', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(67, 3, 18, 'quero ler', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(68, 3, 19, 'lido', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(69, 3, 20, 'lendo', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(70, 3, 21, 'quero ler', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(71, 3, 22, 'lido', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(72, 3, 23, 'lendo', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(73, 4, 1, 'lendo', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(74, 4, 2, 'quero ler', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(75, 4, 4, 'lido', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(76, 4, 5, 'lendo', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(77, 4, 7, 'quero ler', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(78, 4, 8, 'lido', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(79, 4, 17, 'lendo', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(80, 4, 18, 'quero ler', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(81, 4, 19, 'lido', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(82, 4, 20, 'lendo', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(83, 5, 2, 'quero ler', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(84, 5, 3, 'lido', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(85, 5, 5, 'lendo', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(86, 5, 6, 'quero ler', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(87, 5, 8, 'lido', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(88, 5, 9, 'lendo', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(89, 5, 17, 'quero ler', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(90, 5, 18, 'lido', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(91, 5, 19, 'lendo', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(92, 5, 20, 'quero ler', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(93, 6, 1, 'lido', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(94, 6, 3, 'lendo', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(95, 6, 4, 'quero ler', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(96, 6, 5, 'lido', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(97, 6, 7, 'lendo', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(98, 6, 9, 'quero ler', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(99, 6, 17, 'lido', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(100, 6, 18, 'lendo', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(101, 6, 19, 'quero ler', '2026-11-20 22:12:16', '2026-11-20 03:00:00'),
(102, 6, 20, 'lido', '2026-11-20 22:12:16', '2026-11-20 03:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `galeria_autor`
--

CREATE TABLE `galeria_autor` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `imagem` varchar(500) NOT NULL,
  `legenda` varchar(180) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `galeria_autor`
--

INSERT INTO `galeria_autor` (`id`, `usuario_id`, `imagem`, `legenda`, `criado_em`, `atualizado_em`) VALUES
(1, 2, 'assets/uploads/galeria/0b3184ecea17878691133db99949c444.jpg', 'Bienal do Livro 2023', '2026-11-20 18:57:05', '2026-11-20 03:00:00'),
(2, 2, 'assets/uploads/galeria/892af41fa6005b291f7c14526a7adcb8.jpg', 'Bienal do Rio 2025', '2026-11-20 18:59:07', '2026-11-20 03:00:00'),
(3, 2, 'assets/uploads/galeria/fe5846d85eeb82d900fa751bc4e3029b.png', 'Bienal do Rio 2025', '2026-11-20 19:03:39', '2026-11-20 03:00:00'),
(4, 2, 'assets/uploads/galeria/5a9e002dc938396e1252628367e23a4e.jpg', 'Bienal do Livro 2023', '2026-11-20 19:04:58', '2026-11-20 03:00:00'),
(5, 3, 'assets/uploads/galeria/13fe0576d2e9a8f47bffda5e70c1d08c.jpg', 'Lançamento de Verity nos cinemas', '2026-11-20 19:12:08', '2026-11-20 03:00:00'),
(6, 3, 'assets/uploads/galeria/c28733bea507fc69cb7a46ba9b0c119a.webp', 'Lançamento de É assim que acaba nos cinemas', '2026-11-20 19:13:49', '2026-11-20 03:00:00'),
(7, 3, 'assets/uploads/galeria/112bd03392d1ea0989c007bad4a4df2b.jpg', 'bienal do livro 2015', '2026-11-20 19:14:41', '2026-11-20 03:00:00'),
(8, 1, 'assets/uploads/galeria/a15a2f6179f13f3761524a3debe6ba3d.jpg', 'As Crônicas de Spiderwick', '2026-11-20 19:16:24', '2026-11-20 03:00:00'),
(10, 8, 'assets/uploads/galeria/8e7cf250b39091ab4e3c8ce95617c5da.webp', 'Manual de assassinato para boas garotas disponível na Netflix', '2026-11-20 19:27:45', '2026-11-20 03:00:00'),
(11, 8, 'assets/uploads/galeria/c205e8bd74f65ad6bc5adc52d4f3b3cb.jpg', '2 Temporada de Manual de assassinato para boas garotas disponível na Netflix', '2026-11-20 19:28:13', '2026-11-20 03:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `isbn_api_state`
--

CREATE TABLE `isbn_api_state` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `last_request_at` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `isbn_api_state`
--

INSERT INTO `isbn_api_state` (`id`, `last_request_at`) VALUES
(1, '2026-11-21 14:25:40.221239');

-- --------------------------------------------------------

--
-- Estrutura para tabela `isbn_cache`
--

CREATE TABLE `isbn_cache` (
  `isbn` varchar(20) NOT NULL,
  `payload` longtext NOT NULL,
  `cached_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `isbn_cache`
--

INSERT INTO `isbn_cache` (`isbn`, `payload`, `cached_at`) VALUES
('9780593336823', '{\"isbn\":\"9780593336823\",\"title\":\"The Love Hypothesis\",\"author\":\"Ali Hazelwood\",\"publisher\":\"Penguin Random House LLC\",\"year\":2021,\"page_count\":398,\"synopsis\":\"\",\"cover_url\":\"https://covers.openlibrary.org/b/id/10601402-L.jpg?default=false\"}', '2026-11-21 14:08:10'),
('9786555321449', '{\"isbn\":\"9786555321449\",\"title\":\"The Hunger Games\",\"author\":\"Suzanne Collins\",\"publisher\":\"scholastic audiobooks\",\"year\":2008,\"page_count\":397,\"synopsis\":\"When I wake up, the other side of the bed is cold.\",\"cover_url\":\"https://covers.openlibrary.org/b/id/12646537-L.jpg?default=false\"}', '2026-11-20 17:05:11'),
('9786555321456', '{\"isbn\":\"9786555321456\",\"title\":\"Catching Fire\",\"author\":\"Suzanne Collins\",\"publisher\":\"suzzane collins\",\"year\":2009,\"page_count\":400,\"synopsis\":\"I clasp the flask between my hands even though the warmth from the tea has long since leached into the frozen air.\",\"cover_url\":\"https://covers.openlibrary.org/b/id/12646539-L.jpg?default=false\"}', '2026-11-20 17:05:31'),
('9786555321463', '{\"isbn\":\"9786555321463\",\"title\":\"Mockingjay\",\"author\":\"Suzanne Collins\",\"publisher\":\"Audible Studios on Brilliance Audio\",\"year\":2010,\"page_count\":424,\"synopsis\":\"\",\"cover_url\":\"https://covers.openlibrary.org/b/id/12646459-L.jpg?default=false\"}', '2026-11-20 17:05:43'),
('9786555607284', '{\"isbn\":\"9786555607284\",\"title\":\"Melhor do que nos filmes\",\"author\":\"Lynn Painter\",\"publisher\":\"Intrínseca\",\"year\":2023,\"page_count\":null,\"synopsis\":\"\",\"cover_url\":\"https://covers.openlibrary.org/b/id/15140832-L.jpg?default=false\"}', '2026-11-20 17:06:17'),
('9786555658781', '{\"isbn\":\"9786555658781\",\"title\":\"O FILHO PERFEITO\",\"author\":\"Freida McFadden\",\"publisher\":\"ARQUEIRO\",\"year\":2025,\"page_count\":null,\"synopsis\":\"\",\"cover_url\":\"https://covers.openlibrary.org/b/id/15272286-L.jpg?default=false\"}', '2026-11-20 16:21:29'),
('9786559811397', '{\"isbn\":\"9786559811397\",\"title\":\"It Starts with Us\",\"author\":\"Colleen Hoover\",\"publisher\":\"Colleen Hoover\",\"year\":2022,\"page_count\":344,\"synopsis\":\"\",\"cover_url\":\"https://covers.openlibrary.org/b/id/12749873-L.jpg?default=false\"}', '2026-11-20 16:43:49');

-- --------------------------------------------------------

--
-- Estrutura para tabela `livro`
--

CREATE TABLE `livro` (
  `id` int(10) UNSIGNED NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `titulo` varchar(180) NOT NULL,
  `autor` varchar(140) NOT NULL,
  `editora` varchar(180) DEFAULT NULL,
  `ano_publicacao` smallint(5) UNSIGNED DEFAULT NULL,
  `numero_paginas` smallint(5) UNSIGNED DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL DEFAULT 0.00,
  `capa` varchar(500) DEFAULT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `livro`
--

INSERT INTO `livro` (`id`, `isbn`, `titulo`, `autor`, `editora`, `ano_publicacao`, `numero_paginas`, `descricao`, `preco`, `capa`, `usuario_id`, `criado_em`, `atualizado_em`) VALUES
(1, NULL, 'O Príncipe Cruel', 'Holly Black', 'Galera Record', 2018, 322, 'O Príncipe Cruel é o primeiro volume da série best-seller O Povo do Ar. Jude tinha apenas sete anos quando seus pais foram brutalmente assassinados e ela e as irmãs levadas para viver no traiçoeiro Reino das Fadas.\n\nDez anos depois, tudo o que Jude quer é se encaixar, mesmo sendo uma garota mortal. Mas todos os feéricos parecem desprezar os humanos...\n\nEspecialmente o príncipe Cardan, o mais jovem e mais perverso dos filhos do Grande Rei de Elfhame.\n\nPara conquistar o tão desejado lugar na Corte, Jude precisa desafiar o príncipe - e enfrentar as consequências do ato. A garota passa a se envolver cada vez mais nos jogos e intrigas do palácio e acaba descobrindo a própria vocação para trapaças e derramamento de sangue.\n\nMas quando uma traição ameaça afogar o Reino das Fadas em violência, Jude precisará arriscar tudo em uma perigosa aliança para salvar suas irmãs e a própria Elfhame. Cercada por mentiras e pessoas que desejam destruí-la, Jude terá que encontrar o verdadeiro significado da palavra poder antes que seja tarde demais.\n\nCom personagens únicos, reviravoltas inesperadas e uma traição de tirar o fôlego, o livro convida o leitor a mergulhar no universo da obra.', 56.72, 'assets/uploads/livros/1c06bee1284914aaf79dad65789438b2.jpg', 1, '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(2, '9788501925916', 'O Rei Perverso', 'Holly Black', 'Galera Record', 2019, 308, 'O Rei Perverso, de Holly Black, é a eletrizante sequência de O Príncipe Cruel, que continua a saga de Jude Duarte no intrigante e traiçoeiro Reino das Fadas. Após enganar Cardan para que ele jurasse obedecê-la por um ano e um dia, Jude ocupa o cargo de senescal do rei, mas logo percebe que controlar o Grande Rei de Elfhame é uma tarefa mais complexa do que imaginava.\n\nCardan, embora submisso ao juramento, faz de tudo para humilhá-la e prejudicá-la, enquanto seu fascínio por Jude permanece inabalável. Com 308 páginas, o livro aprofunda-se nas intrigas políticas e nas complexas relações de poder, mantendo o ritmo acelerado e a tensão que marcaram o primeiro volume\n\nNeste volume, Jude enfrenta ameaças externas e internas que colocam em risco sua vida e a estabilidade do reino. Ondas ameaçam engolir as terras de Elfhame, e um alerta de traição iminente testa sua habilidade de liderança e sobrevivência.\n\nAo mesmo tempo, ela luta contra seus sentimentos conflitantes por Cardan, em um cenário onde confiança e lealdade são escassas. A autora combina elementos de alta fantasia com drama político, criando uma narrativa envolvente que explora os limites do poder e da ambição.\n\nO Rei Perverso é uma leitura obrigatória para fãs de fantasia que apreciam tramas complexas e personagens multifacetados. A obra mantém o equilíbrio entre ação, romance e intriga política, oferecendo uma continuação digna de seu antecessor.\n\nCom uma escrita envolvente e personagens cativantes, Holly Black solidifica seu lugar como uma das principais autoras de fantasia contemporânea.', 54.34, 'assets/uploads/livros/3fc55127f12f6cdbaf9acb08eb1dfade.webp', 1, '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(3, NULL, 'A Rainha do Nada', 'Holly Black', 'Galera Record', 2019, 294, 'A rainha do nada é o aguardado final da trilogia best-seller O Povo do Ar. Ele será a destruição da coroa e a ruína do trono.\n\nO poder é mais fácil de adquirir do que de manter. Jude aprendeu a lição mais difícil de sua vida quando abdicou do controle do Rei Cardan em troca de um poder imensurável.\n\nAgora, ela carrega o outrora impensável título de Grande Rainha de Elfhame, mas as condições são longe de ser ideais. Exilada por Cardan no mundo mortal, Jude se encontra impotente e frustrada enquanto planeja reivindicar tudo que Cardan tomou dela.\n\nA oportunidade surge com sua irmã gêmea, cuja vida está em perigo. Para salvá-la de uma situação tenebrosa envolvendo Locke, Jude decide voltar ao Reino das Fadas se passando por Taryn.\n\nAntes disso, porém, ela precisa confrontar os próprios sentimentos contraditórios pelo rei que a traiu. No entanto, ao voltar a Elfhame, Jude constata que tudo mudou.\n\nA guerra está prestes a eclodir, e ela caminha próximo a seus inimigos. Será que ela vai ser capaz de resgatar a Coroa e o amor incondicional de Cardan, ao mesmo tempo que destrói os planos de seus inimigos?\n\nOu será que tudo está perdido para sempre? A rainha do nada é o épico desfecho da trilogia O Povo do Ar, da renomada autora Holly Black.\n\nCom intrigas palacianas, reviravoltas inesquecíveis e uma construção de universo ao mesmo tempo complexa e crível, Holly Black se consagra mais uma vez como a rainha do Reino das Fadas e um dos nomes mais icônicos da fantasia para jovens adultos.', 45.00, 'assets/uploads/livros/09cd456a59a07781f3df94fa4f3b85ec.jpg', 1, '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(4, NULL, 'Melhor do que nos filmes', 'Lynn Painter', 'Intrínseca', 2021, 352, 'Sucesso no TikTok, livro de Lynn Painter vai conquistar os fãs de comédias românticas com uma protagonista determinada a encontrar seu “felizes para sempre”\n\nElizabeth Buxbaum sempre soube que seu vizinho não seria um bom namorado. Apesar de todos acharem Wesley Bennett simpático e muito bonito, Liz tinha certeza de que, na verdade, ele era um chato de galochas.\n\nMas Michael Young era diferente. O amor de infância de Liz estava à altura dos protagonistas das comédias românticas que ela tanto gostava, só que havia se mudado para longe quando os dois ainda eram crianças.\n\nDez anos depois, ele estava de volta, mais lindo e charmoso do que nunca. Esbarrar com o garoto na escola foi como um sinal do universo.\n\nO último ano do ensino médio clamava por acontecimentos grandiosos, um baile inesquecível e momentos apaixonantes. Por isso, como uma boa romântica incurável, Liz estava determinada a fazer qualquer coisa para conquistar o verdadeiro amor.\n\nAté mesmo pedir ajuda ao vizinho irritante. O plano era infalível: fazer com que Michael notasse sua existência e a convidasse para o tão sonhado baile de formatura.\n\nMas à medida que Wes e Liz se aproximam, ela vai questionar tudo o que sabe sobre o amor e descobrir que talvez seu “felizes para sempre” seja surpreendente — e melhor do que ela poderia imaginar.', 69.00, 'assets/uploads/livros/b8895082445f66ddbd44a41e57fe673e.webp', 2, '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(5, NULL, 'Não é como nos filmes', 'Lynn Painter', 'Intrínseca', 2024, 416, 'Fenômeno literário no Brasil e no mundo, Melhor do que nos filmes ganha sequência recheada de referências aos filmes de comédia romântica\n\nWes e Liz viveram uma história de amor inesquecível e foram juntos para a Universidade da Califórnia. Mas o universo parecia ter outros planos para eles.\n\nDe uma hora para outra, a vida de Wes virou de ponta-cabeça, e ele precisou voltar para casa e tomar uma decisão inesperada: terminar com Liz. Dois anos depois, Wes convence os treinadores, ganha outra chance no time e volta à universidade.\n\nMais maduro e determinado, ele vai fazer de tudo para reconquistar a garota dos seus sonhos. E conta com um plano perfeito para isso.\n\nLiz, por outro lado, não parece disposta a facilitar as coisas. Afinal, não é tão simples perdoar o garoto que partiu seu coração.\n\nAlém disso, ela precisa conciliar as aulas com um emprego e um estágio superimportante, mas o reencontro com Wes abala suas certezas. E tudo fica mais complicado quando ela começa a acompanhar a equipe de beisebol para um documentário.\n\nSó que... Liz tem um novo namorado.\n\nEntão talvez seja tarde demais para Wes reconquistar a garota que ele ama. Será que os dois estão dispostos a reescrever essa história?\n\nOu será que, na vida real, o “felizes para sempre” não é como nos filmes? Muito aguardada pelos fãs, a sequência do best-seller Melhor do que nos filmes faz inúmeras referências às músicas da Taylor Swift e a clássicos da comédia romântica e alterna os pontos de vista entre Wes e Liz.', 55.60, 'assets/uploads/livros/2bcdd89fd23fcd5532899d61f2b80af5.jpg', 2, '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(6, NULL, 'Apostando no amor', 'Lynn Painter', 'Intrínseca', 2024, 366, 'Na nova comédia romântica de Lynn Painter, autora do best-seller Melhor do que nos filmes, Bailey e Charlie vão aprender que o amor é uma aposta que vale a pena\n\nBailey e Charlie não poderiam ser mais diferentes. Ao se mudar para Nebraska após o divórcio dos pais, Bailey Mitchell conhece Charlie Sampson no aeroporto — um garoto lindo e muito implicante que não para de puxar assunto.\n\nPara piorar, eles sentam juntos no mesmo voo, e ela não tem um segundo de paz. Dois anos depois, eles se reencontram por acaso no cinema, e Charlie continua o completo oposto de Bailey, uma garota de dezesseis anos certinha que gosta de agradar as pessoas e planejar cada detalhe da vida.\n\nUm ano mais tarde, o universo tem uma surpresa: agora eles são colegas de trabalho. Apesar de não se suportarem, dessa vez os dois começam a conversar.\n\nE Bailey percebe que algo mudou: Charlie parece menos cético e... mais bonito. Quando reparam que está rolando um clima entre dois colegas de empresa, eles decidem fazer uma aposta.\n\nCharlie acredita que homens e mulheres não podem ser só amigos. Mas Bailey quer provar que ele está errado.\n\nEntre farpas e risadas, Charlie se dá conta de que Bailey está mexendo com seus sentimentos. E tudo fica mais complicado quando eles combinam de fingir que estão namorando só para irritar o namorado da mãe da garota.\n\nCharlie e Bailey apostaram alto. E nenhum deles está disposto a perder.\n\nIdeal para fãs de comédias românticas, o livrotraz os tropes literários “de inimigos a amantes” (enemies to lovers) e namoro falso, além de fazer referências às músicas de Taylor Swift.', 36.70, 'assets/uploads/livros/8705c9cd12b193803eea7b6f62de69bd.jpg', 2, '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(7, NULL, 'É Assim Que Começa', 'Colleen Hoover', 'Galera Record', 2022, 336, 'Preparem os corações. Lily e Atlas estão de volta na aguardada sequência de É assim que acaba.\n\nÉ assim que começa chega para consagrar novamente Colleen Hoover como a autora mais vendida do Brasil. Colleen é um fenômeno editorial, acumulando não só milhões de vizualizações no TikTok, mas também milhões de exemplares vendidos.\n\nLily Bloom continua administrando uma floricultura. Seu ex-marido abusivo, Ryle Kincaid, ainda é um cirurgião.\n\nMas agora os dois estão oficialmente divorciados e dividem a guarda da filha, Emerson. Quando Lily esbarra em Atlas — com quem não fala há quase dois anos —, parece que finalmente chegou o momento de retomar o relacionamento da adolescência, já que ele também está solteiro e parece retribuir os sentimentos de Lily.\n\nMas apesar de divorciada, Lily não está exatamente livre de Ryle. Culpando Atlas pelo fim de seu casamento, Ryle não está nada disposto a aceitar o novo relacionamento de Lily, ainda mais com Atlas, o último homem que aceitaria ver perto de sua filha e da ex-esposa.\n\nAlternando entre os pontos de vista de Atlas e Lily, É assim que começa retoma logo após o epílogo de É assim que acaba. Revelando mais sobre o passado de Atlas e acompanhando a jornada de Lily para abraçar a sua segunda chance, no amor enquanto lida com um ex-marido ciumento, É assim que começa prova que ninguém entrega uma leitura mais emocionante do que Colleen Hoover.\n\n“Em uma história permeada de tensão com lampejos de esperança, Hoover captura perfeitamente as dores de um coração partido e a felicidade de começar de novo.” - Kirkus Review \"', 54.50, 'assets/uploads/livros/9ada6346630a8a1a49f17d9a357a7754.jpg', 3, '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(8, NULL, 'É assim que acaba', 'Colleen Hoover', 'Galera Record', 2018, 368, 'O romance mais pessoal da carreira de Colleen Hoover, \"É assim que acaba\" aborda temas como violência doméstica e abuso psicológico de maneira sensível e direta. Lily, uma jovem que se mudou de uma pequena cidade do Maine para Boston, formou-se em marketing e abriu sua própria floricultura.\n\nEm um terraço de Boston, ela conhece Ryle, um neurocirurgião confiante, teimoso e talvez um pouco arrogante, com uma grande aversão a relacionamentos, mas que se sente muito atraído por ela. Quando os dois se apaixonam, Lily se vê em um relacionamento turbulento que não é o que ela esperava. \"É assim que acaba\" é uma narrativa poderosa sobre a força necessária para fazer escolhas certas nas situações mais difíceis.\n\nA obra explora a complexidade das relações tóxicas e como amor e abuso muitas vezes coexistem em uma confusão de sentimentos.', 54.50, 'assets/uploads/livros/2c39c84a67a972a2052500e9ba47d2e3.webp', 3, '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(9, NULL, 'Verity', 'Colleen Hoover', 'Galera Record', 2022, 320, 'Um casal apaixonado. Uma intrusa.\n\nTrês mentes doentias. Finalista do prêmio Goodreads como melhor romance de 2019, Verity é o primeiro thriller de Colleen Hoover e deixa os leitores envolvidos do começo ao fim.\n\nHoover é um fenômeno editorial inegável. Acumulando não só milhões de visualizações no TikTok, mas também milhões de exemplares vendidos, ela é a autora mais vendida no Brasil, com suas obras emocionantes que já encantaram milhares de leitores no mundo todo.\n\nVerity Crawford é a autora best-seller por trás de uma série de sucesso. Ela está no auge de sua carreira, aclamada pela crítica e pelo público, no entanto, um súbito e terrível acidente acaba interrompendo suas atividades, deixando-a sem condições de concluir a história...\n\nE é nessa complexa circunstância que surge Lowen Ashleigh, uma escritora à beira da falência convidada a escrever, sob um pseudônimo, os três livros restantes da já consolidada série. Para que consiga entender melhor o processo criativo de Verity com relação aos livros publicados e, ainda, tentar descobrir seus possíveis planos para os próximos, Lowen decide passar alguns dias na casa dos Crawford, imersa no caótico escritório de Verity - e, lá, encontra uma espécie de autobiografia onde a escritora narra os fatos acontecidos desde o dia em que conhece Jeremy, seu marido, até os instantes imediatamente anteriores a seu acidente - incluindo sua perspectiva sobre as tragédias ocorridas às filhas do casal.\n\nQuanto mais o tempo passa, mais Lowen se percebe envolvida em uma confusa rede de mentiras e segredos, e, lentamente, adquire sua própria posição no jogo psicológico que rodeia aquela casa. Emocional e fisicamente atraída por Jeremy, ela precisa decidir: expor uma versão que nem ele conhece sobre a própria esposa ou manter o sigilo dos escritos de Verity?\n\nEm Verity, Colleen Hoover se afasta do estilo que a consagrou, os romances, para se aventurar em um suspense psicológico que deixou todo o mercado editorial sem palavras de tão avassalador. Através de uma narrativa perturbadora e chocante, Verity explora o lado mais sombrio das relações humanas deixando uma surpresinha chocante no final.\n\nClassificação indicativa: 18 anos. \"Aviso: Verity não vai derreter seu coração. Vai fritar sua alma.\" Kindle Crack \"Distintamente sinistro, com um verdadeiro toque de [Colleen] Hoover.\n\nEstive esperando um thriller como esse por anos.\" - Tarryn Fisher, coautora da série Nunca Jamais', 64.70, 'assets/uploads/livros/e96bb4098d00325f72de769cd185a0b4.webp', 3, '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(17, '9789655743814', 'Manual de assassinato para boas garotas', 'Holly Jackson', 'Intrínseca', 2022, 432, 'Todos em Little Kilton conhecem essa história. Andie Bell, a garota mais bonita e popular da escola, foi assassinada pelo namorado, Sal Singh, que se suicidou após o crime.\n\nNa época, não se falava em outra coisa. Cinco anos depois, Pip ainda vê as marcas que a tragédia deixou na cidade, desde as matérias tendenciosas da imprensa local até o ostracismo da família das vítimas.\n\nMas a garota tem a impressão de que há peças faltando nesse quebra-cabeça. Ela conhecia Sal desde a infância, e ele sempre foi gentil.\n\nPor que teria se tornado um assassino? E será que Andie era mesmo tão angelical quanto a imagem construída após a sua morte?\n\nPrestes a se formar no Ensino Médio, Pip decide analisar o crime em seu projeto de conclusão de curso e questionar alguns pontos da versão oficial. Porém, quando a pesquisa revela segredos aterrorizantes, ela percebe que se aproximar da verdade pode custar sua vida.\n\nNessa investigação obsessiva e repleta de reviravoltas, Pip começa a se questionar se ainda é uma boa garota, no fim das contas. Porque, com a ajuda de Ravi, irmão mais novo de Sal, ela está disposta a tudo para fazer seu dever de casa, proteger quem ama e reescrever a história de sua cidade.', 45.50, 'assets/uploads/livros/0596355e97710aad577c40347b051d29.webp', 8, '2026-11-20 19:22:49', '2026-11-20 03:00:00'),
(18, NULL, 'Boa garota, segredo mortal', 'Holly Jackson', 'Intrínseca', 2022, 432, 'Um ano após a investigação obsessiva que reescreveu a história de sua pacata cidade, Pip acredita que seus dias de detetive amadora chegaram ao fim. Prometendo nunca mais se envolver no emaranhado de segredos de Little Kilton, a garota planeja lançar um podcast sobre a resolução do caso e voltar a ter uma vida normal.\n\nAté que algo terrível acontece, e ela precisa quebrar sua promessa. Jamie Reynolds, o irmão de um de seus melhores amigos, está desaparecido.\n\nEle foi visto pela última vez no memorial dedicado a Andie Bell e Sal Singh, seis anos após o crime. Seria mera coincidência?\n\nQuando a polícia se recusa a agir, Pip decide voltar à ativa.\n\nCom a ajuda dos amigos, dos ouvintes do podcast e de Ravi, ela se lança em uma busca frenética para encontrar Jamie antes que seja tarde demais. Cada segundo conta, e só resta uma certeza: ninguém em Little Kilton é quem diz ser.\n\nNessa trama afiada, os efeitos da primeira investigação de Pip se misturam com um novo mistério arrepiante. E ela está disposta a correr riscos ainda maiores em sua busca pela verdade.\n\nNa sequência de Manual de assassinato para boas garotas , Holly Jackson constrói mais um thriller surpreendente, profundo e impossível de largar.', 50.70, 'assets/uploads/livros/f7dab766b6d8b889fc8fdf368140d388.webp', 8, '2026-11-20 19:23:54', '2026-11-20 03:00:00'),
(19, NULL, 'Boa garota nunca mais', 'Holly Jackson', 'Intrisca', 2023, 496, 'Pip está prestes a ir para a faculdade, mas ainda é assombrada pelo desfecho de sua investigação mais recente. Devido à fama de detetive amadora, ela se acostumou a receber ameaças na internet.\n\nMas, nos últimos tempos, uma pergunta não para de surgir: Quem vai investigar quando você desaparecer?\n\nApós uma série de eventos estranhos, Pip percebe que está sendo perseguida de verdade. E pior: seu stalker pode estar conectado a um assassino que aterrorizou a região na mesma época do caso de Andie Bell.\n\nCom a ajuda de Ravi, a garota precisa descobrir quem está por trás disso… antes que se torne a próxima vítima.\n\nEnvolta em uma teia de segredos e perigos, Pip toma atitudes drásticas. Tudo em Little Kilton parece estar interligado e apontar para um único suspeito.\n\nÉ a investigação mais importante de sua vida, e também a última. Aconteça o que acontecer, seus dias de boa garota vão chegar ao fim.\n\nPrepare-se para questionar tudo o que achava que sabia com o desfecho chocante da jornada de Pip. Na aguardada conclusão da série Manual de assassinato para boas garotas, Holly Jackson entrega mais uma trama envolvente, assustadora e inesquecível.', 45.50, 'assets/uploads/livros/983d102cc15ead740556f251c0b06c24.jpg', 8, '2026-11-20 19:25:02', '2026-11-20 03:00:00'),
(20, NULL, 'Assistente do vilão', 'Hannah Nicole Maehrer', 'Alt', 2024, 512, 'Evie Sage, uma jovem com talentos organizacionais impecáveis, vive em busca de estabilidade financeira. Quando consegue um emprego como assistente do vilão mais temido do reino, ela percebe que a descrição do trabalho envolve mais do que apenas lidar com agendas e orçamentos.\n\nRepleto de reviravoltas, intrigas e momentos de tirar o fôlego, o livro mistura romance e ação com um toque de ironia. A narrativa brinca com os clichês dos contos de fadas, destacando o lado humano e as camadas ocultas dos personagens que geralmente ficam à margem das histórias heroicas.', 55.64, 'assets/uploads/livros/5fa2ebbcd073f01caa1ae81d6ff64ad3.webp', 10, '2026-11-20 21:34:08', '2026-11-20 03:00:00'),
(21, NULL, 'Aprendiz do vilão', 'Hannah Nicole Maehrer', 'Alt', 2024, 544, 'O segundo volume da série Assistente do Vilão, a fantasia divertida fenômeno do TikTok\n\nAVISO À EQUIPE: Ultimamente tem havido um aumento perturbador nos comportamentos de felicidade, alegria e otimismo de maneira geral. Por favor, retome seus hábitos sombrios e ameaçadores o mais rápido possível — A gerência Evie Sage nunca esteve tão feliz de ser a assistente do Vilão.\n\nQuem poderia imaginar que trabalhar para um senhor do mal ridiculamente bonito (shhh, é ruim pra imagem dele) seria tão gratificante? Ainda assim, os negócios do mal são muito cansativos, as forças do bem são irritantemente persistentes, e o chefe antes mencionado está... hm... fora do escritório.\n\nMas Rennedawn está afundado em problemas e tudo — até o Reinaldo — aponta para uma catástrofe. Algo peculiar está acontecendo com a magia do reino e fez o castelo do Vilão vulnerável aos inimigos... inclusive seu maior rival, o rei.\n\nEm Aprendiz do Vilão, Evie precisará aprender novas habilidades para confrontar seu maior desafio: proteger o lar do Vilão, todos os seus trabalhos nefastos e, talvez (desde que ninguém descubra), o reino inteiro. Sem pressão, Evie!\n\nTraição. Manusear uma adaga.\n\nConspirar com o inimigo. Tudo isso é tão... tão... maravilhosamente divertido.\n\nMas o que acontece quando a assistente do Vilão está pronta para se tornar sua aprendiz?', 44.00, 'assets/uploads/livros/d6901a1c67306d8f3039c98be90e2081.webp', 10, '2026-11-20 21:35:21', '2026-11-20 03:00:00'),
(22, NULL, 'Aliada do vilão', 'Hannah Nicole Maehrer', 'Alt', 2025, 608, 'O terceiro volume da série Assistente do Vilão, a fantasia divertida fenômeno do TikTok\n\nOFERECE-SE RECOMPENSA! Procura-se a aprendiz do Vilão por traição (leve), dano à propriedade mágica (supostamente) e um incidente envolvendo um pãozinho transformado em arma (confirmado).\n\nFrequentemente vista acompanhada de um sapo mal-humorado (usa coroa, é malcomportado). Atende por “Evie” ou “Fique quieta”.\n\nEvie Sage jamais teve a intenção de se tornar o braço direito do vilão mais temido do reino. Num minuto, ela estava se candidatando a uma vaga de nível básico que prometia “pouca papelada e decapitações ocasionais”, e no seguinte, já estava afundada até o pescoço em caos mágico, planos de assassinato e uma quedinha totalmente inapropriada por seu chefe carrancudo — dono de um maxilar esculpido e energia de catástrofe ambulante.\n\nEntre uma profecia mágica prestes a ruir, assassinos surgindo na sala de descanso e uma quantidade altamente suspeita de sapos usando coroas, Evie precisa dar um jeito de sobreviver a esse emprego — sem implodir o reino. Ou a própria dignidade, que já está por um fio... bem sarcástico.\n\nEstar ao lado do mal nunca fez parte dos seus planos. Mas, pensando bem... também não fazia parte se apaixonar pelo Vilão.\n\nEm Aliada do Vilão, Evie vai descobrir que, às vezes, ser cúmplice do mal é só o começo — porque, quando o reino inteiro ameaça desmoronar (e seu chefe sumir de novo, só para variar), ela precisará decidir até onde vai sua lealdade... e seu coração. Na trilogia Assistente do Vilão, Hannah Nicole Maehrer mistura contos de fadas, humor afiado e caos corporativo em uma fantasia romântica irresistível — provando, de uma vez por todas, que príncipes são ótimos... mas vilões são ainda melhores.', 42.70, 'assets/uploads/livros/d0a4ac629d12239a84ec95b6be6445a8.webp', 10, '2026-11-20 21:36:17', '2026-11-20 03:00:00'),
(23, NULL, 'Adversária do vilão', 'Hannah Nicole Maehrer', 'Alt', 2026, 560, 'O quarto volume da série Assistente do Vilão, a fantasia humorística que conquistou milhares de leitores no Brasil!\n\nATUALIZAÇÃO DO RH: O cargo de “adversária do Vilão” foi preenchido. Evie Sage não se lembra de ter feito sua inscrição.\n\nEvie sempre fez de tudo para ser prestativa, leal e apenas moderadamente envolvida em atos de traição. Mas, com uma profecia perigosa ameaçando a própria magia de Rennedawn — sem falar na corrida para juntar os artefatos necessários antes do rei Benedict —, tudo começou a parecer menos um plano de carreira e mais um problemão.\n\nA profecia insiste em apontar para Evie. A magia do Vilão continua a escolhendo.\n\nE, sinceramente, se vão acusá-la de ser má de qualquer forma, talvez seja melhor aceitar o papel de uma vez. Infelizmente, quanto mais Evie abraça sua suposta era de vilania, mais seu chefe começa a agir como o herói de uma história completamente diferente.\n\nTrystan está cada vez mais próximo dela — mais do que seria sensato, aconselhável ou até profissional — e a olha como se ela já tivesse escolhido o lado do qual quer estar. O dele.\n\nO que é muito inconveniente, porque, se Evie não conseguir salvar a magia do reino, talvez acabe se tornando sua maior ameaça. Adversária do Vilão é o quarto livro da série Assistente do Vilão, que mistura contos de fadas, humor afiado e caos corporativo em uma fantasia romântica irresistível.', 49.50, 'assets/uploads/livros/d950a79d2bb34545e8daa6e4bba6d2fb.webp', 10, '2026-11-20 21:37:11', '2026-11-20 03:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `foto` varchar(500) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `tipo` enum('admin','autor') NOT NULL DEFAULT 'autor',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `email`, `senha`, `foto`, `bio`, `tipo`, `criado_em`, `atualizado_em`) VALUES
(1, 'Holly Black', 'holly.black@autor.com', '$2y$10$ZCyIlboQnkGuMzYodJdKaeL67mFMavPAPrWdW.dk0dy7rwkI300My', 'assets/uploads/usuarios/34183ebc1edd4fa34085d0b93cbf1db8.jpg', 'Holly Black é autora de fantasia e criou universos como Elfhame e As Crônicas de Spiderwick. Suas histórias são conhecidas por criaturas feéricas, intrigas e personagens moralmente complexos.', 'autor', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(2, 'Lynn Painter', 'lynn.painter@autor.com', '$2y$10$Y93NXeaI98uHNhyKuW79HeQmJknvodUnVSpX.kSLrJhsUOCbaT9TO', 'assets/uploads/usuarios/bdb6e449ad120311a4560b2753200da3.jpg', 'Lynn Painter escreve comédias românticas para leitores jovens e adultos. É autora de Melhor do que nos Filmes e cria histórias leves, divertidas e cheias de referências às comédias românticas.', 'autor', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(3, 'Colleen Hoover', 'colleen.hoover@autor.com', '$2y$10$oGYS1Cs6dDcg/7fPu4izKOnB2wAR1AIA6OHhXfyFdVX0ykGPv/aYC', 'assets/uploads/usuarios/588e4624a183326d343f1b5fff9edf2a.webp', 'Colleen Hoover escreve romances contemporâneos e para jovens adultos, além de thrillers psicológicos. Entre seus livros mais conhecidos estão É Assim que Acaba, Verity e Todas as Suas (Im)Perfeições.', 'autor', '2026-11-20 17:26:55', '2026-11-20 03:00:00'),
(7, 'Administrador', 'admin1@administrador.com', '$2y$10$pvz22DvVFkHz/Wp0GAy/u.GeTNOycx9Gy3JU8o8ru0b0kyEpaXOGu', 'assets/uploads/usuarios/57992bb5dd231284a05677895b1efa53.jpg', NULL, 'admin', '2026-11-20 17:41:34', '2026-11-20 03:00:00'),
(8, 'Holly Jackson', 'holly.jackson@autor.com', '$2y$10$oFlN5cVZPnHR8bBQFEXZWuFT/32oaSj0FJ8LeTh9/TgJFS5ad7AgW', 'assets/uploads/usuarios/fb6684493588e9adc0e54981de371b8c.jpg', 'Holly Jackson escreve mistérios e thrillers para jovens leitores. É autora da série Manual de Assassinato para Boas Garotas e estudou linguística literária e escrita criativa na Universidade de Nottingham.', 'autor', '2026-11-20 17:56:02', '2026-11-20 03:00:00'),
(9, 'Administrador', 'admin2@administrador.com', '$2y$10$YEcdA/1zNuUWNAFZ4U1xL.J2HGMIZF3er5bQcxsKjBAjoCIoyePG2', 'assets/uploads/usuarios/36d59e09ebb65dc4441031cdf8ac1a30.jpg', NULL, 'admin', '2026-11-20 17:59:25', '2026-11-20 03:00:00'),
(10, 'Hannah Nicole Maehrer', 'hannah.nicole.maehrer@autor.com', '$2y$10$4ZaI45YXY3IY5xIv7N5YgOrRzBGP/gtBOn1aCaZZGOcr8B7szYD1q', 'assets/uploads/usuarios/2537b21e643754ebfee93f3a2d51f8ee.jpg', 'Hannah Nicole Maehrer é autora de fantasia romântica e criadora de conteúdo literário. Seu universo mistura magia, humor e romance, com destaque para a série Assistant to the Villain.', 'autor', '2026-11-20 21:30:26', '2026-11-20 03:00:00');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `autor_seguidor`
--
ALTER TABLE `autor_seguidor`
  ADD PRIMARY KEY (`cliente_id`,`autor_id`),
  ADD KEY `idx_autor_seguidor_autor` (`autor_id`);

--
-- Índices de tabela `avaliacao_livro`
--
ALTER TABLE `avaliacao_livro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_avaliacao_cliente_livro` (`cliente_id`,`livro_id`),
  ADD KEY `idx_avaliacao_livro` (`livro_id`);

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `compra`
--
ALTER TABLE `compra`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_compra_cliente_livro` (`cliente_id`,`livro_id`),
  ADD KEY `fk_compra_livro` (`livro_id`);

--
-- Índices de tabela `estante`
--
ALTER TABLE `estante`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_estante_cliente_livro` (`cliente_id`,`livro_id`),
  ADD KEY `fk_estante_livro` (`livro_id`);

--
-- Índices de tabela `galeria_autor`
--
ALTER TABLE `galeria_autor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_galeria_autor_usuario` (`usuario_id`);

--
-- Índices de tabela `isbn_api_state`
--
ALTER TABLE `isbn_api_state`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `isbn_cache`
--
ALTER TABLE `isbn_cache`
  ADD PRIMARY KEY (`isbn`),
  ADD KEY `idx_isbn_cache_cached_at` (`cached_at`);

--
-- Índices de tabela `livro`
--
ALTER TABLE `livro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `fk_livro_usuario` (`usuario_id`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `avaliacao_livro`
--
ALTER TABLE `avaliacao_livro`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `compra`
--
ALTER TABLE `compra`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estante`
--
ALTER TABLE `estante`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT de tabela `galeria_autor`
--
ALTER TABLE `galeria_autor`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `livro`
--
ALTER TABLE `livro`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `autor_seguidor`
--
ALTER TABLE `autor_seguidor`
  ADD CONSTRAINT `fk_autor_seguidor_autor` FOREIGN KEY (`autor_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_autor_seguidor_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `avaliacao_livro`
--
ALTER TABLE `avaliacao_livro`
  ADD CONSTRAINT `fk_avaliacao_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_avaliacao_livro` FOREIGN KEY (`livro_id`) REFERENCES `livro` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `compra`
--
ALTER TABLE `compra`
  ADD CONSTRAINT `fk_compra_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_compra_livro` FOREIGN KEY (`livro_id`) REFERENCES `livro` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `estante`
--
ALTER TABLE `estante`
  ADD CONSTRAINT `fk_estante_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_estante_livro` FOREIGN KEY (`livro_id`) REFERENCES `livro` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `galeria_autor`
--
ALTER TABLE `galeria_autor`
  ADD CONSTRAINT `fk_galeria_autor_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `livro`
--
ALTER TABLE `livro`
  ADD CONSTRAINT `fk_livro_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
