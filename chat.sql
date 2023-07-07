-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 26/06/2023 às 05:33
-- Versão do servidor: 10.4.27-MariaDB
-- Versão do PHP: 8.1.12

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

DELIMITER $$
--
-- Procedimentos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `contatos` (IN `userNickName` VARCHAR(20))  NO SQL select clientes.nickName as nickNameContato, clientes.nomeCliente AS Contato, messages.Messages, max(messages.date) as DateFormated FROM clientes INNER JOIN messages on messages .MsgFrom = clientes.nickName or messages.MsgTo = clientes.nickName WHERE (messages.MsgFrom = userNickName AND clientes.nickName != userNickName) OR  (messages.MsgTo = userNickName AND clientes.nickName != userNickName) GROUP BY nickNameContato ORDER BY DateFormated DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `contNotReceived` (IN `contactNickName` VARCHAR(20))  NO SQL SELECT COUNT(Idmessage) AS contMSg FROM messages WHERE messages.MsgFrom = contactNickName AND received = 0 OR messages.MsgFrom = contactNickName AND received = 2$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `countAnexos` (IN `nickName` VARCHAR(20), IN `contactNickName` VARCHAR(20))  NO SQL SELECT count(messages.Idmessage) AS countAnexos From messages INNER JOIN anexo on anexo.mensagem = messages.Idmessage WHERE messages.MsgFrom = contactNickName AND messages.MsgTo = nickName OR messages.MsgFrom = nickName AND messages.MsgTo = contactNickName$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteMessage` (IN `idMsg` INT(20), IN `nickName` VARCHAR(20))   DELETE FROM messages WHERE messages.Idmessage = idMsg and messages.MsgFrom = nickName$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `firstMessageWithAttachment` (IN `nickName` VARCHAR(20))  NO SQL SELECT messages.Idmessage,messages.Messages, messages.MsgFrom,messages.MsgTo, messages.Date as dataOrd, DATE_FORMAT(messages.date, '%H:%i') as HourMsg FROM messages WHERE messages.MsgTo = nickName AND messages.received = 0 ORDER BY dataOrd DESC LIMIT 0,1$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `messages` (IN `nickName` VARCHAR(20), IN `contactNickName` VARCHAR(20))  NO SQL SELECT m.*, DATE_FORMAT(m.date, '%H:%i') AS HourMsg, an.nome AS nome_anexo, an.arquivo AS arquivo_anexo
FROM messages m
LEFT JOIN anexo an ON m.Idmessage = an.mensagem
WHERE (m.MsgFrom = contactNickName AND m.MsgTo = nickName) OR (m.MsgFrom = nickName AND m.MsgTo = contactNickName)
ORDER BY DATE_FORMAT(m.date, '%Y/%m/%d %H:%i:%s') ASC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `messagesWithAttachment` (IN `nickName` VARCHAR(20), IN `contactNickName` VARCHAR(20))  NO SQL SELECT messages.Idmessage, "" as messages ,messages.MsgFrom,"",messages.Date as dataOrd, DATE_FORMAT(messages.date, '%H:%i') as HourMsg, anexo.nome AS NomeArquivo, arquivos.nomeHash AS hashArquivo From messages INNER JOIN anexo on anexo.mensagem = messages.Idmessage INNER JOIN arquivos ON arquivos.nomeHash = anexo.arquivo WHERE messages.MsgFrom = contactNickName AND messages.MsgTo = nickName OR messages.MsgFrom = nickName AND messages.MsgTo = contactNickName
UNION
SELECT messages.Idmessage, messages.Messages, messages.MsgFrom,messages.MsgTo, messages.Date as dataOrd, DATE_FORMAT(messages.date, '%H:%i') as HourMsg, "" AS NomeArquivo, "" AS hashArquivo From messages WHERE messages.MsgFrom = contactNickName AND messages.MsgTo = nickName AND messages.Messages != "" OR messages.MsgFrom = nickName AND messages.MsgTo = contactNickName AND messages.Messages != "" ORDER BY dataOrd DESC LIMIT 0,15$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `newMsg` (IN `userNickName` VARCHAR(20), IN `contactuserNickName` VARCHAR(20), IN `received` INT(1))   SELECT COUNT(messages.Idmessage) as countMsg FROM messages WHERE messages.MsgFrom = contactuserNickName AND messages.MsgTo = userNickName AND messages.received = received$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `newMsgs` (IN `userNickName` VARCHAR(20))   SELECT COUNT(newMsg.msgTo) as countMsg FROM newMsg WHERE newMsg.msgTo = userNickName$$

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

-- --------------------------------------------------------

--
-- Estrutura para tabela `messages`
--

CREATE TABLE `messages` (
  `Idmessage` int(20) NOT NULL,
  `Messages` varchar(600) NOT NULL,
  `MsgFrom` varchar(20) NOT NULL,
  `MsgTo` varchar(20) NOT NULL,
  `Date` varchar(20) NOT NULL DEFAULT current_timestamp(),
  `received` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `newMsg`
--

CREATE TABLE `newMsg` (
  `msgFrom` varchar(20) NOT NULL,
  `msgTo` varchar(20) NOT NULL
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
-- Índices de tabela `newMsg`
--
ALTER TABLE `newMsg`
  ADD KEY `msgFrom_Fk` (`msgFrom`),
  ADD KEY `msgTo_Fk` (`msgTo`);

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
-- Restrições para tabelas `newMsg`
--
ALTER TABLE `newMsg`
  ADD CONSTRAINT `msgFrom_Fk` FOREIGN KEY (`msgFrom`) REFERENCES `clientes` (`nickName`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `msgTo_Fk` FOREIGN KEY (`msgTo`) REFERENCES `clientes` (`nickName`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `profilepicture`
--
ALTER TABLE `profilepicture`
  ADD CONSTRAINT `clienteId` FOREIGN KEY (`clienteId`) REFERENCES `clientes` (`nickName`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
