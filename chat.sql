-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 01/02/2022 às 05:19
-- Versão do servidor: 10.4.22-MariaDB
-- Versão do PHP: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `Chat`
--

DELIMITER $$
--
-- Procedimentos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `contatos` (IN `userNickName` VARCHAR(20))  NO SQL
select clientes.nickName as nickNameContato, clientes.nomeCliente AS Contato, messages.Messages, max(messages.date) as DateFormated FROM clientes INNER JOIN messages on messages .MsgFrom = clientes.nickName or messages.MsgTo = clientes.nickName WHERE (messages.MsgFrom = userNickName AND clientes.nickName != userNickName) OR  (messages.MsgTo = userNickName AND clientes.nickName != userNickName) GROUP BY Contato ORDER BY DateFormated DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `contNotReceived` (IN `contactNickName` VARCHAR(20))  NO SQL
SELECT COUNT(Idmessage) AS contMSg FROM messages WHERE messages.MsgFrom = contactNickName AND received = 0 OR messages.MsgFrom = contactNickName AND received = 2$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `countAnexos` (IN `nickName` VARCHAR(20), IN `contactNickName` VARCHAR(20))  NO SQL
SELECT count(messages.Idmessage) AS countAnexos From messages INNER JOIN anexo on anexo.mensagem = messages.Idmessage WHERE messages.MsgFrom = contactNickName AND messages.MsgTo = nickName OR messages.MsgFrom = nickName AND messages.MsgTo = contactNickName$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteMessage` (IN `idMsg` INT(20), IN `nickName` VARCHAR(20))  DELETE FROM messages WHERE messages.Idmessage = idMsg and messages.MsgFrom = nickName$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `firstMessageWithAttachment` (IN `nickName` VARCHAR(20))  NO SQL
SELECT messages.Idmessage,messages.Messages, messages.MsgFrom,messages.MsgTo, messages.Date as dataOrd, DATE_FORMAT(messages.date, '%H:%i') as HourMsg FROM messages WHERE messages.MsgTo = nickName AND messages.received = 0 ORDER BY dataOrd DESC LIMIT 0,1$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `messages` (IN `nickName` VARCHAR(20), IN `contactNickName` VARCHAR(20))  NO SQL
SELECT *, DATE_FORMAT(messages.date, '%H:%i') as HourMsg From messages WHERE messages.MsgFrom = contactNickName AND messages.MsgTo = nickName OR messages.MsgFrom = nickName AND messages.MsgTo = contactNickName ORDER BY DATE_FORMAT(messages.date, '%Y/%m/%d %H:%i:%s') ASC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `messagesWithAttachment` (IN `nickName` VARCHAR(20), IN `contactNickName` VARCHAR(20))  NO SQL
SELECT messages.Idmessage, "" as messages ,messages.MsgFrom,"",messages.Date as dataOrd, DATE_FORMAT(messages.date, '%H:%i') as HourMsg, anexo.nome AS NomeArquivo, arquivos.nomeHash AS hashArquivo From messages INNER JOIN anexo on anexo.mensagem = messages.Idmessage INNER JOIN arquivos ON arquivos.nomeHash = anexo.arquivo WHERE messages.MsgFrom = contactNickName AND messages.MsgTo = nickName OR messages.MsgFrom = nickName AND messages.MsgTo = contactNickName
UNION
SELECT messages.Idmessage, messages.Messages, messages.MsgFrom,messages.MsgTo, messages.Date as dataOrd, DATE_FORMAT(messages.date, '%H:%i') as HourMsg, "" AS NomeArquivo, "" AS hashArquivo From messages WHERE messages.MsgFrom = contactNickName AND messages.MsgTo = nickName AND messages.Messages != "" OR messages.MsgFrom = nickName AND messages.MsgTo = contactNickName AND messages.Messages != "" ORDER BY dataOrd DESC LIMIT 0,15$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `newMsg` (IN `userNickName` VARCHAR(20), IN `contactuserNickName` VARCHAR(20), IN `received` INT(1))  SELECT COUNT(messages.Idmessage) as countMsg FROM messages WHERE messages.MsgFrom = contactuserNickName AND messages.MsgTo = userNickName AND messages.received = received$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `newMsgs` (IN `userNickName` VARCHAR(20))  SELECT COUNT(newMsg.msgTo) as countMsg FROM newMsg WHERE newMsg.msgTo = userNickName$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `searchContato` (IN `contactNickName` VARCHAR(20))  SELECT clientes.nomeCliente as Contato, clientes.nickName as nickNameContato FROM clientes WHERE clientes.nickName LIKE CONCAT("%",contactNickName,"%") OR clientes.nomeCliente LIKE CONCAT("%",contactNickName,"%")$$

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura para tabela `arquivos`
--

CREATE TABLE `arquivos` (
  `nomeHash` varchar(300) NOT NULL,
  `arquivo` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `nomeCliente` varchar(20) NOT NULL,
  `nickName` varchar(20) NOT NULL,
  `senha` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`nomeCliente`, `nickName`, `senha`) VALUES
('William Dourado', '324', '49acae3f64f68683b7d3b29129452202be9df718b102cea556f5da254bb0bf3860d83a8fb3e2868fc1de85bd6b0de0d69d5ad5d421f0fe6f5748d81a2bba44e0'),
('Alyssom', 'ally77', 'f6541153606baa043898e9286252be3da213b53bf88371f07172449974967ae1cfa02eb32939df66a9cdb465fe2b15cbf5c5cb8324b4b0d6ca2596d641e395e7'),
('Based Pepe', 'based_Pepe', 'bf829a012991fe8380ee52dde9349ade67c3e748eb2851d66842e8275d205cb973794f6626816c6428b184ee118dddaeb68b9cabcd195028c106820ced31de1c'),
('Гуммо', 'gvmmo', '649620a4b72e5c7a9536703ac9b56ba96f872cc698bdf1e21bda4492b1cbb1f71d1da987e9c2cd8c20aac34609cf5a0856776e0722781a73f259a9d4528d49ee'),
('HongKong77', 'hong_kong77', 'cf3e55a4fb06be6f89aab4d93f0ff97eb8d804134e7667519343d492436458a9441b96aa4e7e4ad9c361d3cb5cecd66565fa112a39a0a7e7bbf5303a50c06511'),
('Juliana Monique', 'juhMonique', 'b996c1d402f3d4a42f74219b2e2d0e65c264552dac45bbe94fdffb169d42b3ef85273f6646b01429362ac0b86dff9f9182e86711694575e821e079e7b01a40be'),
('Lobo da Estepe', 'LoboDaEstepe', '0703f0fbf102bd37bb4397e094ab109c79580516973e5e6715b1dd4155033732247204b307cc98dc516d3a4f63a204a78ce10b3a01635fb8dfa4014ebca2e4ba'),
('Logan', 'logan77', 'a2d7f3ddaa4f7737d5b09efd35aacb9fd46d65aa55abf97ba59afcb702d0e6e4b57a5c8c0fd5dcba795572039296e51d5421c7d8757e7440d830641c732a3d81'),
('Eloa', 'lola', '9306e5a07857d057df506cb4be4dab89f714ff038f47494f9c0ae1fa436b09cdaa8660568920c1f57aa0496987d752457e9885d62859f1ac27167d0500c3c4f0'),
('Marlon', 'marlon77', '7547cb5edd847874d6096a7834a6bc8cc361d5a1747255b0be2f9af381f2ecc23af7738aec80a6ac62f0e299c5cf862e6e7d2aa66347af5c803f725649159d6d'),
('Mayumi Sato', 'mayumi_Sato', '253ec8f659ab7560acd2ae12490af2299c12f4b771d6b2c55fea3f6e277e588121fd13654b1966f5f4c52f7a6fdc3e8ea7d817b1838c00485f71240d8a54fb74'),
('pco', 'pco_cooperative', '45546472d49c709ad4cf6a4258da21cf462dc764b48ec1895f6095eaa5d0b03e06a5b927ee5f7d6ece1f1e328bb8a1429227087db503ab0d758afc91ab3550c7'),
('Pepe BluePill', 'pepe_bluepill', 'ca3ac3b326622426f5954fd543a043e5cc6b4c8b2ad6455a2a6ffa7c40594cca6f094b6d8d0a7bf78d898703ab5169e08d52190ea9454ba7e72efd2f9f6a1671'),
('Rafael', 'rafa77', '82f86d39196c5d8c2dcda74c3a7e4cf94c12d0fb2c7f66947e9f538116d1c1d6e7520eb1cd066e2d2088f3c602be8dacbdc838ec7521fc4b10385118f833ad1c'),
('William Dourado', 'willCruz', 'e04deea2cac576f39302a073c511ae7ce3d4fd9d3fa8151e289c67eedfd34c70bd9ca01a0cddf0890496e15dcffb19f5ba184cbe11d52f5bca532f254659bdb1'),
(' Уилл Голден', 'willGolden', 'cfe7e31d64fd6dff8c27d8e51ea4424a985e8434795859cdc21de9a20c3220dfe3d5ee19143d6e58eadfb60e1d100751edf2edf279f928715e486d0942fe9c31'),
('William Dourado', 'willGolden65', '58ac7d9f2d41bc370f31c5b444a1b903e8249a14dcc17d525e14f6a45ce9f703581d3e64fda708b9f8b2bb8b80c073230dbc29fa05a501f36a52592c5dfbc58d'),
('WilliamDourado', 'willGolden7', 'd2d9834d23900330405cd0ce6c6cddaf4eeee85f7171465cc3b2597741a44866630854d1e49d2c0caddb18f246db83b26f828e9420f1bc82cf5970a78865a585');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `messages`
--

INSERT INTO `messages` (`Idmessage`, `Messages`, `MsgFrom`, `MsgTo`, `Date`, `received`) VALUES
(1686, ' Oi bb\r\n', 'lola', 'willGolden', '2022-01-26 17:42:58', 1),
(1700, ' Oi\r\n', 'ally77', 'lola', '2022-01-27 10:26:07', 1),
(1795, ' Oi', 'ally77', 'lola', '2022-01-27 20:10:13', 1),
(1908, ' Oi', 'willGolden', 'willGolden', '2022-01-29 01:53:41', 1),
(1922, ' OI\n', 'willGolden', 'lola', '2022-01-29 22:04:31', 1),
(1923, 'Olá', 'willGolden', 'lola', '2022-01-29 22:04:37', 1),
(1930, ' Ooi', 'lola', 'ally77', '2022-01-30 13:53:56', 1),
(1932, 'Oxe ', 'lola', 'willGolden', '2022-01-30 13:57:35', 1),
(1936, ' Oi\n', 'willGolden', 'ally77', '2022-01-30 20:19:36', 1),
(1937, ' Oi', 'willGolden', 'lola', '2022-01-30 20:19:46', 1),
(2017, ' OI ', 'lola', 'willGolden', '2022-02-01 01:14:28', 1),
(2018, 'g f', 'lola', 'willGolden', '2022-02-01 01:14:49', 1),
(2019, 'fdfg ', 'lola', 'willGolden', '2022-02-01 01:15:10', 1),
(2020, ' Oi ', 'ally77', 'willGolden', '2022-02-01 01:16:13', 1),
(2021, 'ds gf ', 'ally77', 'willGolden', '2022-02-01 01:16:17', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `newMsg`
--

CREATE TABLE `newMsg` (
  `msgFrom` varchar(20) NOT NULL,
  `msgTo` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `newMsg`
--

INSERT INTO `newMsg` (`msgFrom`, `msgTo`) VALUES
('gvmmo', 'hong_kong77'),
('gvmmo', 'hong_kong77');

-- --------------------------------------------------------

--
-- Estrutura para tabela `profilepicture`
--

CREATE TABLE `profilepicture` (
  `clienteId` varchar(20) NOT NULL,
  `picture` longblob NOT NULL,
  `format` varchar(20) NOT NULL,
  `updated` varchar(20) NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `profilepicture`
--

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
  MODIFY `anexoId` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=345;

--
-- AUTO_INCREMENT de tabela `messages`
--
ALTER TABLE `messages`
  MODIFY `Idmessage` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2023;

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
