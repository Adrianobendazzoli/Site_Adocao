/*
AUTO-COMMENTED FILE
Original path: site-adocao1/projeto/banco_de_dados/sistema_adocao.sql
Summary (auto-generated):
performs INSERT/UPDATE/DELETE (writes data); related to favorites functionality

Notes:
- This header was generated automatically to give a quick overview of the file.
- Inline, line-by-line commenting was NOT applied automatically to avoid changing behavior.
- If you want detailed line-by-line comments for specific files, ask and I'll produce them.
*/
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21/11/2025 às 21:00
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
-- Banco de dados: `sistema_adocao`
--
CREATE DATABASE IF NOT EXISTS `sistema_adocao` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `sistema_adocao`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `favoritos`
--

CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL,
  `usuario_cpf` varchar(14) NOT NULL,
  `pet_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `favoritos`
--

INSERT INTO `favoritos` (`id`, `usuario_cpf`, `pet_id`) VALUES
(9, '12345678977', 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pets`
--

CREATE TABLE `pets` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `cor` varchar(50) DEFAULT NULL,
  `tamanho` decimal(10,2) DEFAULT NULL,
  `peso` decimal(10,2) DEFAULT NULL,
  `castrado` enum('sim','nao') DEFAULT 'nao',
  `detalhes_castracao` text DEFAULT NULL,
  `id_tipo` int(11) DEFAULT NULL,
  `id_raca` int(11) DEFAULT NULL,
  `foto` varchar(255) NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_cpf` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pets`
--

INSERT INTO `pets` (`id`, `nome`, `descricao`, `cor`, `tamanho`, `peso`, `castrado`, `detalhes_castracao`, `id_tipo`, `id_raca`, `foto`, `data_cadastro`, `usuario_cpf`) VALUES
(5, 'Bob', 'docil', 'Dourado', 132.00, 56.00, 'sim', 'raiva', 1, 7, '', '2025-11-20 19:01:51', '12345678900'),
(7, 'Rex', 'docil', 'Dourado', 132.00, 56.00, 'sim', 'raiva', 1, 7, '', '2025-11-20 19:04:00', '12345678900'),
(13, 'Rabito', 'Muito Dócil', 'preto', 110.00, 56.00, 'sim', 'Castrado', 1, 10, 'uploads/1763754777_labrador1.jpg', '2025-11-21 19:52:57', '12345678911'),
(14, 'Bolinha', 'Adora carinho', 'marrom', 12.00, 0.50, 'sim', 'Feliz', 5, 24, 'uploads/1763754878_img_20190514_205937_934.jpg', '2025-11-21 19:54:38', '12345678922'),
(15, 'Fofo', 'Muito fofo, por isso o nome', 'branco', 17.00, 1.00, 'sim', '', 3, 17, 'uploads/1763754996_toy-ou-anao_341_0_600.jpg', '2025-11-21 19:56:36', '12345678933'),
(16, 'Voleibol', 'Muito adoravel', 'preto', 273.00, 100.00, 'nao', '', 8, 37, 'uploads/1763755133_images.jpeg', '2025-11-21 19:58:53', '12345678944');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pet_photos`
--

CREATE TABLE `pet_photos` (
  `id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `photo_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pet_photos`
--

INSERT INTO `pet_photos` (`id`, `pet_id`, `photo_path`) VALUES
(7, 5, 'img/1763665311_golden3.jpeg'),
(8, 5, 'img/1763665311_golden2.jpeg'),
(9, 5, 'img/1763665311_golden.jpg'),
(13, 7, 'img/1763665440_golden3.jpeg'),
(14, 7, 'img/1763665440_golden2.jpeg'),
(15, 7, 'img/1763665440_golden.jpg'),
(21, 13, 'uploads/1763754777_labrador1.jpg'),
(22, 13, 'uploads/1763754777_labrador.jpg'),
(23, 14, 'uploads/1763754878_img_20190514_205937_934.jpg'),
(24, 14, 'uploads/1763754878_download.jpeg'),
(25, 15, 'uploads/1763754996_toy-ou-anao_341_0_600.jpg'),
(26, 15, 'uploads/1763754996_coelho-anao.jpg'),
(27, 16, 'uploads/1763755133_images.jpeg'),
(28, 16, 'uploads/1763755133_download (1).jpeg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `raca`
--

CREATE TABLE `raca` (
  `id_raca` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `id_tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `raca`
--

INSERT INTO `raca` (`id_raca`, `descricao`, `id_tipo`) VALUES
(7, 'Golden Retriever', 1),
(9, 'Poodle', 1),
(10, 'Labrador', 1),
(11, 'Shih Tzu', 1),
(12, 'Persa', 2),
(13, 'Siamês', 2),
(14, 'Maine Coon', 2),
(15, 'Sphynx', 2),
(16, 'Ragdoll', 2),
(17, 'Coelho Anão', 3),
(18, 'Coelho Holandês', 3),
(19, 'Coelho Fuzzy Lop', 3),
(20, 'Calopsita', 4),
(21, 'Periquito', 4),
(22, 'Canário', 4),
(23, 'Papagaio', 4),
(24, 'Sírio', 5),
(25, 'Anão Chinês', 5),
(26, 'Roborovski', 5),
(27, 'Iguana', 6),
(28, 'Jiboia', 6),
(29, 'Tartaruga', 6),
(30, 'Betta', 7),
(31, 'Tetra Neon', 7),
(32, 'Guppy', 7),
(33, 'Oscar', 7),
(34, 'Puro Sangue Inglês', 8),
(35, 'Quarto de Milha', 8),
(36, 'Árabe', 8),
(37, 'Lusitano', 8);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo`
--

CREATE TABLE `tipo` (
  `id_tipo` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tipo`
--

INSERT INTO `tipo` (`id_tipo`, `descricao`) VALUES
(1, 'Cachorro'),
(2, 'Gato'),
(3, 'Coelho'),
(4, 'Pássaro'),
(5, 'Hamster'),
(6, 'Réptil'),
(7, 'Peixe'),
(8, 'Cavalo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `user_favorites`
--

CREATE TABLE `user_favorites` (
  `id` int(11) NOT NULL,
  `usuario_cpf` varchar(20) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `cpf` varchar(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `data_nasc` date DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `telefone` varchar(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('usuario','admin') NOT NULL DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`cpf`, `nome`, `data_nasc`, `cep`, `telefone`, `email`, `senha`, `tipo`) VALUES
('12345678900', 'Guilherme', '2008-08-10', '09423500', '11969005288', 'guilherme@gmail.com', '$2y$10$sPEo8MY26QjkWC9LT5qole5ik.0d0TzzJzE4nePbdfeGokHD6P21K', 'admin'),
('12345678911', 'Flavio', '2000-05-10', '09402060', '11999999999', 'Flavio@gmail.com', '$2y$10$anwqTPsjJIrZ2eRe.jqb0OlVgqtU37SMWATT0Cb/Dokp3Wksu/gWi', 'usuario'),
('12345678922', 'Pedro', '2001-02-23', '01310930', '11999999998', 'pedro@gmail.com', '$2y$10$wctK.Pcdu29YzYRiWo9cjOvHgiYEdmkcLMj2CNZ.y7cSCKWByffq6', 'usuario'),
('12345678933', 'Kaio', '2009-03-01', '22793380', '11999999997', 'kaio@gmail.com', '$2y$10$kiJunl8EvQTMGdRI2DWPduWpxxt0YN14rs9jUDlbdWLgrRN5.j0Pa', 'usuario'),
('12345678944', 'Vitor', '2009-01-27', '32010770', '11969005286', 'vitor@gmail.com', '$2y$10$.MccM9kgeV63Ay3TXhUTD.ZRt7298IRkg5Cs9PVOA1c.BJwRaiEAu', 'usuario');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_cpf_pet` (`usuario_cpf`,`pet_id`),
  ADD KEY `pet_id` (`pet_id`);

--
-- Índices de tabela `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_cpf` (`usuario_cpf`),
  ADD KEY `id_tipo` (`id_tipo`),
  ADD KEY `id_raca` (`id_raca`);

--
-- Índices de tabela `pet_photos`
--
ALTER TABLE `pet_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pet_id` (`pet_id`);

--
-- Índices de tabela `raca`
--
ALTER TABLE `raca`
  ADD PRIMARY KEY (`id_raca`),
  ADD KEY `id_tipo` (`id_tipo`);

--
-- Índices de tabela `tipo`
--
ALTER TABLE `tipo`
  ADD PRIMARY KEY (`id_tipo`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`cpf`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `pet_photos`
--
ALTER TABLE `pet_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de tabela `raca`
--
ALTER TABLE `raca`
  MODIFY `id_raca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de tabela `tipo`
--
ALTER TABLE `tipo`
  MODIFY `id_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`usuario_cpf`) REFERENCES `usuario` (`cpf`) ON DELETE CASCADE,
  ADD CONSTRAINT `pets_ibfk_2` FOREIGN KEY (`id_tipo`) REFERENCES `tipo` (`id_tipo`) ON DELETE SET NULL,
  ADD CONSTRAINT `pets_ibfk_3` FOREIGN KEY (`id_raca`) REFERENCES `raca` (`id_raca`) ON DELETE SET NULL;

--
-- Restrições para tabelas `pet_photos`
--
ALTER TABLE `pet_photos`
  ADD CONSTRAINT `pet_photos_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `raca`
--
ALTER TABLE `raca`
  ADD CONSTRAINT `raca_fk_tipo` FOREIGN KEY (`id_tipo`) REFERENCES `tipo` (`id_tipo`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
