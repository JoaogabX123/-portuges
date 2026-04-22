-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21/04/2026 às 10:30
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
-- Banco de dados: `mais_portugues`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `email` varchar(120) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` enum('professor','admin') NOT NULL DEFAULT 'professor',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `questoes`
--

CREATE TABLE `questoes` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `tipo` enum('objetiva','dissertativa') NOT NULL,
  `status` enum('rascunho','publicada') NOT NULL DEFAULT 'rascunho',
  `genero` enum('narrativo','argumentativo','descritivo','expositivo','instrucional') NOT NULL,
  `subgenero` varchar(100) DEFAULT NULL,
  `especificacao` varchar(100) DEFAULT NULL,
  `enunciado` longtext NOT NULL,
  `explicacao` longtext DEFAULT NULL,
  `resposta_correta` char(1) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `id_usuario_criador` int(11) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `alternativas_objetivas`
--

CREATE TABLE `alternativas_objetivas` (
  `id` int(11) NOT NULL,
  `id_questao` int(11) NOT NULL,
  `alternativa` char(1) NOT NULL,
  `texto` longtext NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `questoes`
--
ALTER TABLE `questoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_questoes_usuario` (`id_usuario_criador`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_genero` (`genero`);

--
-- Índices de tabela `alternativas_objetivas`
--
ALTER TABLE `alternativas_objetivas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_questao_alternativa` (`id_questao`,`alternativa`),
  ADD KEY `fk_alt_questao` (`id_questao`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `questoes`
--
ALTER TABLE `questoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `alternativas_objetivas`
--
ALTER TABLE `alternativas_objetivas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `questoes`
--
ALTER TABLE `questoes`
  ADD CONSTRAINT `fk_questoes_usuario` FOREIGN KEY (`id_usuario_criador`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `alternativas_objetivas`
--
ALTER TABLE `alternativas_objetivas`
  ADD CONSTRAINT `fk_alt_questao` FOREIGN KEY (`id_questao`) REFERENCES `questoes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Dados iniciais
--

--
-- Despejando dados para a tabela `usuarios`
--
INSERT INTO `usuarios` (`id`, `email`, `senha`, `nome`, `tipo`, `status`, `criado_em`) VALUES
(1, 'admin@admin.com', '$2y$10$qYvVPzqb6bKb.kPr5PmwNejHKXHH5o9pZHFl1d3y.8bC8pE6l5B5W', 'Administrador', 'admin', 1, current_timestamp());

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
