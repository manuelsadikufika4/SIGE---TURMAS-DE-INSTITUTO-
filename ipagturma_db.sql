-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05/05/2026 às 14:44
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
-- Estrutura para tabela `disciplinas`
--

CREATE TABLE `disciplinas` (
  `id` int(11) NOT NULL,
  `nome_disciplina` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `disciplinas`
--

INSERT INTO `disciplinas` (`id`, `nome_disciplina`) VALUES
(1, 'Sistema de Informação');

-- --------------------------------------------------------

--
-- Estrutura para tabela `frequencia`
--

CREATE TABLE `frequencia` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `data_registro` datetime DEFAULT current_timestamp(),
  `status` enum('presente','ausente','justificado') DEFAULT 'presente',
  `observacao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `frequencia`
--

INSERT INTO `frequencia` (`id`, `aluno_id`, `data_registro`, `status`, `observacao`) VALUES
(1, 4, '2026-05-05 03:52:04', 'presente', NULL),
(2, 4, '2026-05-05 03:52:27', 'presente', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `horarios`
--

CREATE TABLE `horarios` (
  `id` int(11) NOT NULL,
  `id_turma` int(11) NOT NULL,
  `dia_semana` enum('Segunda','Terça','Quarta','Quinta','Sexta') NOT NULL,
  `aula_numero` int(11) NOT NULL,
  `disciplina` varchar(100) NOT NULL,
  `horario_inicio` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `lista_barulhentos`
--

CREATE TABLE `lista_barulhentos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `data_registro` datetime DEFAULT current_timestamp(),
  `motivo` varchar(255) DEFAULT 'Conversa excessiva'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id` int(11) NOT NULL,
  `id_remetente` int(11) NOT NULL,
  `id_destinatario` int(11) NOT NULL,
  `mensagem` text NOT NULL,
  `data_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `lida` tinyint(1) DEFAULT 0,
  `arquivo_url` varchar(255) DEFAULT NULL,
  `tipo_arquivo` enum('texto','imagem','audio') DEFAULT 'texto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `mensagens`
--

INSERT INTO `mensagens` (`id`, `id_remetente`, `id_destinatario`, `mensagem`, `data_envio`, `lida`, `arquivo_url`, `tipo_arquivo`) VALUES
(1, 1, 4, 'oi', '2026-05-05 10:08:01', 0, NULL, 'texto'),
(2, 4, 1, 'estou bem, maninho', '2026-05-05 10:09:04', 0, NULL, 'texto'),
(3, 4, 1, 'estou bem, maninho', '2026-05-05 10:10:51', 0, NULL, 'texto'),
(4, 4, 1, 'carro', '2026-05-05 10:23:19', 0, 'uploads/imagens/5bce5a619f8e35b435b6e153d027529e.jpeg', 'imagem'),
(5, 2, 1, 'mbizo', '2026-05-05 10:39:37', 0, NULL, 'texto');

-- --------------------------------------------------------

--
-- Estrutura para tabela `professor_turma`
--

CREATE TABLE `professor_turma` (
  `id` int(11) NOT NULL,
  `id_professor` int(11) NOT NULL,
  `id_turma` int(11) NOT NULL,
  `id_disciplina` int(11) NOT NULL,
  `turno` varchar(50) NOT NULL,
  `classe` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `professor_turma`
--

INSERT INTO `professor_turma` (`id`, `id_professor`, `id_turma`, `id_disciplina`, `turno`, `classe`) VALUES
(1, 2, 1, 1, 'Tarde', '12');

-- --------------------------------------------------------

--
-- Estrutura para tabela `relatorios_entregues`
--

CREATE TABLE `relatorios_entregues` (
  `id` int(11) NOT NULL,
  `delegado_id` int(11) NOT NULL,
  `turma` varchar(50) NOT NULL,
  `data_envio` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `relatorios_entregues`
--

INSERT INTO `relatorios_entregues` (`id`, `delegado_id`, `turma`, `data_envio`) VALUES
(1, 1, 'IG12A25', '2026-05-05 05:10:50'),
(2, 1, 'IG12A25', '2026-05-05 05:13:31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `turmas`
--

CREATE TABLE `turmas` (
  `id` int(11) NOT NULL,
  `nome_turma` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `turmas`
--

INSERT INTO `turmas` (`id`, `nome_turma`) VALUES
(1, 'IG12A25'),
(2, 'GRH12A25');

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
(1, 'manuelsadikufika4', '$2y$10$vVvAoJ4Pqr0qwaoI9zvWo.N1LUc61/NoDnWLt2hm15/.aacXQAcUe', '', 'delegado', 'Manuel Sadi Kufika', '', 'IG12A25', 1, '2026-05-05 11:44:42'),
(2, 'bernardokinavuidipaulo', '$2y$10$yHywyrJaImh.YJvbSQn6DepDE2GC6Ff9iofdmgSo381spaYLqcG1O', '', 'professor', 'Bernardo Kinavuidi Paulo Lukoki', '', '', NULL, '2026-05-05 11:39:43'),
(3, 'luvuatumuku', '$2y$10$lsScq/lUYXHdZqDBvfsFA.bZcBz7GxlVfA1/wiA.JB1OpKYpmjZKG', '', 'coordenador', '', '', '', NULL, '2026-05-05 11:26:30'),
(4, 'lidiapaulinamiguel', '$2y$10$Oq0ezYLbasMpckNPR/6d4O6tMOu2x7hNp8xp6G3Fr.FAGBiAlLW6O', '', 'alunoComun', 'Lídia Paulina Miguel', '2024002', 'IG12A25', 1, '2026-05-05 11:32:07');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `disciplinas`
--
ALTER TABLE `disciplinas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome_disciplina` (`nome_disciplina`);

--
-- Índices de tabela `frequencia`
--
ALTER TABLE `frequencia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_aluno_frequencia` (`aluno_id`);

--
-- Índices de tabela `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_turma` (`id_turma`);

--
-- Índices de tabela `lista_barulhentos`
--
ALTER TABLE `lista_barulhentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_remetente` (`id_remetente`),
  ADD KEY `id_destinatario` (`id_destinatario`);

--
-- Índices de tabela `professor_turma`
--
ALTER TABLE `professor_turma`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_professor` (`id_professor`),
  ADD KEY `id_turma` (`id_turma`),
  ADD KEY `id_disciplina` (`id_disciplina`);

--
-- Índices de tabela `relatorios_entregues`
--
ALTER TABLE `relatorios_entregues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_delegado_relatorio` (`delegado_id`);

--
-- Índices de tabela `turmas`
--
ALTER TABLE `turmas`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT de tabela `disciplinas`
--
ALTER TABLE `disciplinas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `frequencia`
--
ALTER TABLE `frequencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `lista_barulhentos`
--
ALTER TABLE `lista_barulhentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `professor_turma`
--
ALTER TABLE `professor_turma`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `relatorios_entregues`
--
ALTER TABLE `relatorios_entregues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `turmas`
--
ALTER TABLE `turmas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `frequencia`
--
ALTER TABLE `frequencia`
  ADD CONSTRAINT `fk_aluno_frequencia` FOREIGN KEY (`aluno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`id_turma`) REFERENCES `turmas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `lista_barulhentos`
--
ALTER TABLE `lista_barulhentos`
  ADD CONSTRAINT `lista_barulhentos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `mensagens`
--
ALTER TABLE `mensagens`
  ADD CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`id_remetente`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `mensagens_ibfk_2` FOREIGN KEY (`id_destinatario`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `professor_turma`
--
ALTER TABLE `professor_turma`
  ADD CONSTRAINT `professor_turma_ibfk_1` FOREIGN KEY (`id_professor`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `professor_turma_ibfk_2` FOREIGN KEY (`id_turma`) REFERENCES `turmas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `professor_turma_ibfk_3` FOREIGN KEY (`id_disciplina`) REFERENCES `disciplinas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `relatorios_entregues`
--
ALTER TABLE `relatorios_entregues`
  ADD CONSTRAINT `fk_delegado_relatorio` FOREIGN KEY (`delegado_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_turma` FOREIGN KEY (`id_turma`) REFERENCES `turmas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
