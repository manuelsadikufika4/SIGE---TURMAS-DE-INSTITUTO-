-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05/05/2026 às 12:35
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
-- Banco de dados: `ipagturma_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nomeUsuario` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `cargo` enum('delegado','professor','alunoComun','coordenador') NOT NULL,
  `nome_completo` varchar(100) NOT NULL DEFAULT '',
  `numeroInterno` varchar(20) NOT NULL DEFAULT '',
  `turma` varchar(20) NOT NULL DEFAULT '',
  `id_turma` int(11) DEFAULT NULL,
  `ultima_atividade` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nomeUsuario`, `senha`, `email`, `cargo`, `nome_completo`, `numeroInterno`, `turma`, `id_turma`, `ultima_atividade`) VALUES
(1, 'manuelsadikufika4', '$2y$10$vVvAoJ4Pqr0qwaoI9zvWo.N1LUc61/NoDnWLt2hm15/.aacXQAcUe', '', 'delegado', 'Manuel Sadi Kufika', '', 'IG12A25', 1, '2026-05-05 11:26:30'),
(2, 'bernardokinavuidipaulo', '$2y$10$yHywyrJaImh.YJvbSQn6DepDE2GC6Ff9iofdmgSo381spaYLqcG1O', '', 'professor', 'Bernardo Kinavuidi Paulo Lukoki', '', '', NULL, '2026-05-05 11:26:30'),
(3, 'luvuatumuku', '$2y$10$lsScq/lUYXHdZqDBvfsFA.bZcBz7GxlVfA1/wiA.JB1OpKYpmjZKG', '', 'coordenador', '', '', '', NULL, '2026-05-05 11:26:30'),
(4, 'lidiapaulinamiguel', '$2y$10$Oq0ezYLbasMpckNPR/6d4O6tMOu2x7hNp8xp6G3Fr.FAGBiAlLW6O', '', 'alunoComun', 'Lídia Paulina Miguel', '2024002', 'IG12A25', 1, '2026-05-05 11:32:07');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomeUsuario` (`nomeUsuario`),
  ADD KEY `fk_turma` (`id_turma`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_turma` FOREIGN KEY (`id_turma`) REFERENCES `turmas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
