-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 11/12/2023 às 19:10
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `ChatPHP`
--
CREATE DATABASE IF NOT EXISTS `ChatPHP` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ChatPHP`;

DELIMITER $$
--
-- Procedimentos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `contatos` (IN `userNickName` VARCHAR(20))  NO SQL select clientes.nickName as nickNameContato, clientes.nomeCliente AS Contato, messages.Messages, max(messages.date) as DateFormated FROM clientes INNER JOIN messages on messages .MsgFrom = clientes.nickName or messages.MsgTo = clientes.nickName WHERE (messages.MsgFrom = userNickName AND clientes.nickName != userNickName) OR  (messages.MsgTo = userNickName AND clientes.nickName != userNickName) GROUP BY nickNameContato ORDER BY DateFormated DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteMessage` (IN `idMsg` INT(20), IN `nickName` VARCHAR(20))   DELETE FROM messages WHERE messages.Idmessage = idMsg and messages.MsgFrom = nickName$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `lastMessage` (IN `nickName` VARCHAR(20), IN `contactNickName` VARCHAR(20))  NO SQL BEGIN
    SELECT m.*, DATE_FORMAT(m.date, '%H:%i') AS HourMsg, an.nome AS nome_anexo, an.arquivo AS arquivo_anexo
    FROM messages m
    LEFT JOIN anexo an ON m.Idmessage = an.mensagem
    WHERE (m.MsgFrom = contactNickName AND m.MsgTo = nickName) OR (m.MsgFrom = nickName AND m.MsgTo = contactNickName)
    ORDER BY m.date DESC
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `messageByID` (IN `nickName` VARCHAR(20), IN `contactNickName` VARCHAR(20), IN `messageId` INT)  NO SQL BEGIN
    SELECT m.*, DATE_FORMAT(m.date, '%H:%i') AS HourMsg, an.nome AS nome_anexo, an.arquivo AS arquivo_anexo
    FROM messages m
    LEFT JOIN anexo an ON m.Idmessage = an.mensagem
    WHERE ((m.MsgFrom = contactNickName AND m.MsgTo = nickName) OR (m.MsgFrom = nickName AND m.MsgTo = contactNickName))
        AND m.Idmessage = messageId
    ORDER BY m.date DESC
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `messagePaginated` (IN `nickName` VARCHAR(20), IN `contactNickName` VARCHAR(20), IN `partNumber` INT)  NO SQL BEGIN
    DECLARE startIdx INT;
    DECLARE pageSize INT;
    
    SET pageSize = 10; -- Número de mensagens por página
    
    -- Calcula o índice de início da seleção com base na parte fornecida
    SET startIdx = (partNumber - 1) * pageSize;
    
    SELECT m.*, DATE_FORMAT(m.date, '%H:%i') AS HourMsg,
           an.nome AS nome_anexo, an.arquivo AS arquivo_anexo
    FROM messages m
    LEFT JOIN anexo an ON m.Idmessage = an.mensagem
    WHERE (m.MsgFrom = contactNickName AND m.MsgTo = nickName)
       OR (m.MsgFrom = nickName AND m.MsgTo = contactNickName)
    ORDER BY m.date DESC -- Ordena por data mais recente primeiro
    LIMIT startIdx, pageSize;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `messages` (IN `nickName` VARCHAR(20), IN `contactNickName` VARCHAR(20))  NO SQL SELECT m.*, DATE_FORMAT(m.date, '%H:%i') AS HourMsg, an.nome AS nome_anexo, an.arquivo AS arquivo_anexo
FROM messages m
LEFT JOIN anexo an ON m.Idmessage = an.mensagem
WHERE (m.MsgFrom = contactNickName AND m.MsgTo = nickName) OR (m.MsgFrom = nickName AND m.MsgTo = contactNickName)
ORDER BY DATE_FORMAT(m.date, '%Y/%m/%d %H:%i:%s') ASC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `searchContato` (IN `contactNickName` VARCHAR(20))   SELECT clientes.nomeCliente as Contato, clientes.nickName as nickNameContato FROM clientes WHERE clientes.nickName LIKE CONCAT("%",contactNickName,"%") OR clientes.nomeCliente LIKE CONCAT("%",contactNickName,"%")$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `anexo`
--

CREATE TABLE `anexo` (
  `anexoId` int(20) NOT NULL,
  `nome` varchar(260) DEFAULT '',
  `arquivo` varchar(300) NOT NULL,
  `mensagem` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `arquivos`
--

CREATE TABLE `arquivos` (
  `nomeHash` varchar(300) NOT NULL,
  `arquivo` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `nomeCliente` varchar(20) NOT NULL,
  `nickName` varchar(20) NOT NULL,
  `senha` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`nomeCliente`, `nickName`, `senha`) VALUES
('Alyssom', 'ally77', '0e3a1b6281353561aa19e3423d9737168e4247aae6194a3c366d09ef75be3e26a814a61420974cfc99534a79e5114af4d6aee63028a2203e2d06d4e3fe08dadd'),
('Гуммо', 'gvmmo', '73ff16a44c59b42ffaaedebd4958b46c31b0081690fe4f7c2eddbb4afc17929d50daab5a17157d38d8d2e3e8f29808f8a8fa6ec97b99337c4b852c1bd56a3368'),
('Eloa', 'lola', '4b92d552c953acd11c6370b4f107518c00df07e6ca99269a1dae70bdb7d1747966e1fd25c28e2d265a46851b02d3ef36ceb131d6c70b7e51ddb26aed92a9415c'),
('Уилл Голден', 'willGolden', 'a8d853d9087fe946983f10db963a28488a1ee6474a0684ad29111d5c948b431b5fa0d1f5a33335ebf0615fc3c540ecaec687783f62ff58c48ed6cb2badb8f4e4');

-- --------------------------------------------------------

--
-- Estrutura para tabela `messages`
--

CREATE TABLE `messages` (
  `Idmessage` int(20) NOT NULL,
  `Messages` varchar(4196) NOT NULL,
  `MsgFrom` varchar(20) NOT NULL,
  `MsgTo` varchar(20) NOT NULL,
  `Date` varchar(20) NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `new_messages`
--

CREATE TABLE `new_messages` (
  `sender` varchar(20) NOT NULL,
  `receiver` varchar(20) NOT NULL,
  `message_count` int(11) NOT NULL DEFAULT 1,
  `messageId` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `profilepicture`
--

CREATE TABLE `profilepicture` (
  `clienteId` varchar(20) NOT NULL,
  `picture` longblob NOT NULL,
  `format` varchar(20) NOT NULL,
  `updated` varchar(20) NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `token`
--

CREATE TABLE `token` (
  `clienteID` varchar(256) NOT NULL,
  `tokenHash` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `anexo`
--
ALTER TABLE `anexo`
  ADD PRIMARY KEY (`anexoId`),
  ADD KEY `arquivoAnexado` (`arquivo`),
  ADD KEY `mensagemAnexada` (`mensagem`);

--
-- Índices de tabela `arquivos`
--
ALTER TABLE `arquivos`
  ADD PRIMARY KEY (`nomeHash`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`nickName`),
  ADD KEY `nickName` (`nickName`);

--
-- Índices de tabela `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`Idmessage`) USING BTREE,
  ADD KEY `msgToCliente` (`MsgTo`),
  ADD KEY `msgFromCliente` (`MsgFrom`),
  ADD KEY `Idmessage` (`Idmessage`),
  ADD KEY `Idmessage_2` (`Idmessage`);

--
-- Índices de tabela `new_messages`
--
ALTER TABLE `new_messages`
  ADD PRIMARY KEY (`sender`,`receiver`),
  ADD KEY `receiver` (`receiver`),
  ADD KEY `new_messages_ibfk_3` (`messageId`);

--
-- Índices de tabela `profilepicture`
--
ALTER TABLE `profilepicture`
  ADD KEY `clienteId` (`clienteId`),
  ADD KEY `clienteId_2` (`clienteId`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `anexo`
--
ALTER TABLE `anexo`
  MODIFY `anexoId` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `messages`
--
ALTER TABLE `messages`
  MODIFY `Idmessage` int(20) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `anexo`
--
ALTER TABLE `anexo`
  ADD CONSTRAINT `arquivoAnexado` FOREIGN KEY (`arquivo`) REFERENCES `arquivos` (`nomeHash`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mensagemAnexada` FOREIGN KEY (`mensagem`) REFERENCES `messages` (`Idmessage`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `msgFromCliente` FOREIGN KEY (`MsgFrom`) REFERENCES `clientes` (`nickName`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `msgToCliente` FOREIGN KEY (`MsgTo`) REFERENCES `clientes` (`nickName`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `new_messages`
--
ALTER TABLE `new_messages`
  ADD CONSTRAINT `new_messages_ibfk_1` FOREIGN KEY (`sender`) REFERENCES `clientes` (`nickName`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `new_messages_ibfk_2` FOREIGN KEY (`receiver`) REFERENCES `clientes` (`nickName`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `new_messages_ibfk_3` FOREIGN KEY (`messageId`) REFERENCES `messages` (`Idmessage`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `profilepicture`
--
ALTER TABLE `profilepicture`
  ADD CONSTRAINT `clienteId` FOREIGN KEY (`clienteId`) REFERENCES `clientes` (`nickName`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
