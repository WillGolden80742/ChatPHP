-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 05/01/2022 às 16:42
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `firstMessageWithAttachment` (IN `nickName` VARCHAR(20))  NO SQL
SELECT messages.Idmessage,messages.Messages, messages.MsgFrom,messages.MsgTo, messages.Date as dataOrd, DATE_FORMAT(messages.date, '%H:%i') as HourMsg FROM messages WHERE messages.MsgTo = nickName AND messages.received = 0 ORDER BY dataOrd DESC LIMIT 0,1$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `messages` (IN `nickName` VARCHAR(20), IN `contactNickName` VARCHAR(20))  NO SQL
SELECT *, DATE_FORMAT(messages.date, '%H:%i') as HourMsg From messages WHERE messages.MsgFrom = contactNickName AND messages.MsgTo = nickName OR messages.MsgFrom = nickName AND messages.MsgTo = contactNickName ORDER BY DATE_FORMAT(messages.date, '%d/%m/%Y %H:%i:%s') ASC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `messagesWithAttachment` (IN `nickName` VARCHAR(20), IN `contactNickName` VARCHAR(20))  NO SQL
SELECT messages.Idmessage, "" as messages ,messages.MsgFrom,"",messages.Date as dataOrd, DATE_FORMAT(messages.date, '%H:%i') as HourMsg, anexo.nome AS NomeArquivo, arquivos.nomeHash AS hashArquivo From messages INNER JOIN anexo on anexo.mensagem = messages.Idmessage INNER JOIN arquivos ON arquivos.nomeHash = anexo.arquivo WHERE messages.MsgFrom = contactNickName AND messages.MsgTo = nickName OR messages.MsgFrom = nickName AND messages.MsgTo = contactNickName
UNION
SELECT messages.Idmessage, messages.Messages, messages.MsgFrom,messages.MsgTo, messages.Date as dataOrd, DATE_FORMAT(messages.date, '%H:%i') as HourMsg, "" AS NomeArquivo, "" AS hashArquivo From messages WHERE messages.MsgFrom = contactNickName AND messages.MsgTo = nickName AND messages.Messages != "" OR messages.MsgFrom = nickName AND messages.MsgTo = contactNickName AND messages.Messages != "" ORDER BY dataOrd DESC LIMIT 0,15$$

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
  `senha` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`nomeCliente`, `nickName`, `senha`) VALUES
('Alyssom', 'ally77', 'a51696a86ebadde170b7b06e83f1ec82'),
('Based Pepe', 'based_Pepe', '7bb8414eeea88d61ddaa90ab732f1c56'),
('Гуммо', 'gvmmo', '46de12bf37a60ee1868826fa8b2f6dd4'),
('HongKong77', 'hong_kong77', '9de6f455f1316788a94e36c6db2dff4e'),
('Juliana Monique', 'juhMonique', 'dbdf6b52482eeaca1d540116bf42f52a'),
('Lobo da Estepe', 'LoboDaEstepe', '8e5f96d94b6446e3e9b497cac95d4540'),
('Logan', 'logan77', '401a66b2ed2ee754683da79537eba83d'),
('lolo', 'lolo', '8dddc0620fe076293393ad81e3ce86fd'),
('Marlon', 'marlon77', 'cefdad4bd12e8c57cdf9cf1d176b429a'),
('Mayumi Sato', 'mayumi_Sato', 'fa9e685425a32079d81f024355066df8'),
('pco', 'pco_cooperative', '24ebaad6df398839d63f29cb7eb3b971'),
('Pepe BluePill', 'pepe_bluepill', 'd9002957b34ebefa020cca178bd46739'),
('Rafael', 'rafa77', 'cbcc887b198ad392cd9f60027f27e37a'),
('Уильям Голден', 'willGolden', '69013773d31191dcc17bf195f73ef2e6'),
('wololo', 'wololo', '89b44b8b1515dd349cce0b300029c054');

-- --------------------------------------------------------

--
-- Estrutura para tabela `messages`
--

CREATE TABLE `messages` (
  `Idmessage` int(20) NOT NULL,
  `Messages` varchar(500) NOT NULL,
  `MsgFrom` varchar(20) NOT NULL,
  `MsgTo` varchar(20) NOT NULL,
  `Date` varchar(20) NOT NULL DEFAULT current_timestamp(),
  `received` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `messages`
--

INSERT INTO `messages` (`Idmessage`, `Messages`, `MsgFrom`, `MsgTo`, `Date`, `received`) VALUES
(9, 'E ai bl? Cara', 'ally77', 'willGolden', '2021-03-06 22:48:24', 1),
(10, 'Blz sim', 'willGolden', 'ally77', '2021-03-06 22:48:24', 1),
(11, 'E ai blz cara?', 'marlon77', 'willGolden', '2021-03-07 22:48:24', 1),
(12, 'blz sim mano ', 'willGolden', 'marlon77', '2021-03-07 22:48:24', 1),
(13, 'E ai mano, blz?', 'rafa77', 'willGolden', '2021-03-08 22:48:24', 1),
(15, 'Maravilha então', 'rafa77', 'willGolden', '2021-03-09 22:48:24', 1),
(16, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'rafa77', 'willGolden', '2021-03-10 18:27:19', 1),
(19, 'E ai Rafa. Blz?', 'willGolden', 'rafa77', '2021-03-10 21:09:34', 1),
(20, 'E ai Will. Blz? ', 'rafa77', 'willGolden', '2021-03-10 21:10:05', 1),
(21, 'E ai Will. Blz? ', 'rafa77', 'willGolden', '2021-03-10 21:10:10', 1),
(24, 'E ai Marlon? ', 'willGolden', 'marlon77', '2021-03-10 21:16:40', 1),
(25, 'E ai Ally. Blz? ', 'willGolden', 'ally77', '2021-03-10 21:20:09', 1),
(26, 'E ai Will. Blz?', 'ally77', 'willGolden', '2021-03-10 21:27:36', 1),
(27, 'blz mano', 'willGolden', 'ally77', '2021-03-10 21:40:20', 1),
(28, 'maravilha entao ', 'ally77', 'willGolden', '2021-03-10 21:40:29', 1),
(29, 'E ai ', 'willGolden', 'ally77', '2021-03-10 21:42:18', 1),
(30, 'To bem mano. E vc?', 'marlon77', 'willGolden', '2021-03-11 02:58:31', 1),
(31, 'To tranquilo', 'willGolden', 'marlon77', '2021-03-11 02:58:43', 1),
(32, 'Que ótimo então', 'marlon77', 'willGolden', '2021-03-11 02:59:05', 1),
(33, 'Ainda bem ', 'marlon77', 'willGolden', '2021-03-11 03:00:58', 1),
(34, 'E vc como está?', 'willGolden', 'marlon77', '2021-03-11 03:10:43', 1),
(35, 'To bem ', 'marlon77', 'willGolden', '2021-03-11 03:10:52', 1),
(36, 'Ok então', 'marlon77', 'willGolden', '2021-03-11 03:11:07', 1),
(39, 'blz mano', 'ally77', 'willGolden', '2021-03-11 18:28:45', 1),
(40, 'Como vc tá?', 'ally77', 'willGolden', '2021-03-11 18:28:47', 1),
(41, 'Traquilo', 'willGolden', 'ally77', '2021-03-11 18:29:47', 1),
(43, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop', 'ally77', 'willGolden', '2021-03-12 13:03:03', 1),
(44, '(҂`_´)\n         <,︻╦̵̵̿╤─ ҉     ~  •\n█۞███████]▄▄▄▄▄▄▄▄▄▄▃ ●●●\n▂▄▅█████████▅▄▃▂…\n[███████████████████]\n◥⊙▲⊙▲⊙▲⊙▲⊙▲⊙▲⊙\n', 'willGolden', 'rafa77', '2021-03-12 16:52:45', 1),
(240, 'Daora', 'ally77', 'willGolden', '2021-03-27 18:45:20', 1),
(243, 'E ai Will\r\n', 'rafa77', 'willGolden', '2021-03-30 14:37:11', 1),
(245, 'Olá', 'juhMonique', 'willGolden', '2021-03-30 14:41:48', 1),
(246, 'E ai ', 'LoboDaEstepe', 'willGolden', '2021-03-30 14:41:48', 1),
(371, 'Como está?\r\n', 'willGolden', 'mayumi_Sato', '2021-04-02 02:23:19', 1),
(378, 'Se você não gosta do wolverine, eu sugiro que', 'willGolden', 'logan77', '2021-04-02 14:13:53', 1),
(417, 'Bom dia\r\n', 'hong_kong77', 'willGolden', '2021-04-03 12:15:28', 1),
(431, 'Olá\r\n', 'hong_kong77', 'rafa77', '2021-04-03 13:29:07', 1),
(433, 'Olá, camarada\r\n', 'gvmmo', 'willGolden', '2021-04-03 20:38:43', 1),
(628, 'Como vai?\r\n', 'rafa77', 'hong_kong77', '2021-04-06 00:15:41', 1),
(943, 'Tudo bem?\r\n', 'willGolden', 'gvmmo', '2021-04-09 22:15:42', 1),
(947, '', 'willGolden', 'gvmmo', '2021-04-09 22:23:57', 1),
(949, '', 'gvmmo', 'willGolden', '2021-04-09 22:31:35', 1),
(970, '', 'hong_kong77', 'willGolden', '2021-04-09 23:01:11', 1),
(980, '', 'hong_kong77', 'willGolden', '2021-04-09 23:25:19', 1),
(981, 'ola\r\n', 'willGolden', 'hong_kong77', '2021-04-09 23:25:37', 1),
(982, '', 'gvmmo', 'willGolden', '2021-04-09 23:27:20', 1),
(983, 'Como vai?\r\n', 'gvmmo', 'hong_kong77', '2021-04-09 23:49:50', 1),
(994, 'Tudo certo por aqui\r\n', 'willGolden', 'gvmmo', '2021-04-10 11:45:53', 1),
(995, '', 'gvmmo', 'willGolden', '2021-04-10 11:47:12', 1),
(997, '', 'willGolden', 'gvmmo', '2021-04-10 12:38:59', 1),
(998, '', 'willGolden', 'gvmmo', '2021-04-10 12:39:52', 1),
(999, '', 'willGolden', 'gvmmo', '2021-04-10 14:30:25', 1),
(1001, 'Ola\r\n', 'willGolden', 'gvmmo', '2021-04-10 15:28:09', 1),
(1024, '', 'willGolden', 'gvmmo', '2021-04-10 20:25:49', 1),
(1027, '', 'willGolden', 'gvmmo', '2021-04-10 20:32:00', 1),
(1031, 'Como vai?\r\n', 'pco_cooperative', 'willGolden', '2021-04-11 13:19:39', 1),
(1034, 'E ai?\r\n', 'willGolden', 'rafa77', '2021-04-11 15:38:14', 1),
(1035, 'Tudo bem?\r\n', 'willGolden', 'rafa77', '2021-04-11 15:38:57', 1),
(1036, 'Como vai?\r\n', 'willGolden', 'rafa77', '2021-04-11 15:44:29', 1),
(1037, '', 'willGolden', 'rafa77', '2021-04-11 19:35:52', 1),
(1038, '', 'willGolden', 'rafa77', '2021-04-11 20:09:59', 1),
(1039, '', 'willGolden', 'rafa77', '2021-04-11 20:15:39', 1),
(1043, 'Olá\r\n', 'wololo', 'rafa77', '2021-04-16 23:02:37', 1),
(1044, '', 'willGolden', 'mayumi_Sato', '2021-04-17 17:16:10', 1),
(1188, 'Olá\r\n', 'willGolden', 'mayumi_Sato', '2021-04-18 19:49:44', 1),
(1190, 'Olá\r\n', 'willGolden', 'mayumi_Sato', '2021-04-18 19:51:39', 1),
(1218, 'Como vai\r\n', 'gvmmo', 'willGolden', '2021-04-18 20:42:34', 1),
(1219, 'Olá\r\n', 'gvmmo', 'willGolden', '2021-04-18 20:42:46', 1),
(1221, 'Houve erro?\r\n', 'gvmmo', 'willGolden', '2021-04-18 20:43:21', 1),
(1222, 'Engraçado\r\n', 'gvmmo', 'hong_kong77', '2021-04-18 20:43:36', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `profilepicture`
--

CREATE TABLE `profilepicture` (
  `clienteId` varchar(20) NOT NULL,
  `picture` longblob NOT NULL,
  `format` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `anexo`
  ADD PRIMARY KEY (`anexoId`),
  ADD KEY `arquivoAnexado` (`arquivo`),
  ADD KEY `mensagemAnexada` (`mensagem`);

ALTER TABLE `arquivos`
  ADD PRIMARY KEY (`nomeHash`);

ALTER TABLE `clientes`
  ADD PRIMARY KEY (`nickName`);

ALTER TABLE `messages`
  ADD PRIMARY KEY (`Idmessage`) USING BTREE,
  ADD KEY `msgToCliente` (`MsgTo`),
  ADD KEY `msgFromCliente` (`MsgFrom`),
  ADD KEY `Idmessage` (`Idmessage`);

ALTER TABLE `profilepicture`
  ADD KEY `clienteId` (`clienteId`);

ALTER TABLE `anexo`
  MODIFY `anexoId` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=343;

ALTER TABLE `messages`
  MODIFY `Idmessage` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1223;

ALTER TABLE `anexo`
  ADD CONSTRAINT `arquivoAnexado` FOREIGN KEY (`arquivo`) REFERENCES `arquivos` (`nomeHash`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mensagemAnexada` FOREIGN KEY (`mensagem`) REFERENCES `messages` (`Idmessage`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `messages`
  ADD CONSTRAINT `msgFromCliente` FOREIGN KEY (`MsgFrom`) REFERENCES `clientes` (`nickName`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `msgToCliente` FOREIGN KEY (`MsgTo`) REFERENCES `clientes` (`nickName`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `profilepicture`
  ADD CONSTRAINT `clienteId` FOREIGN KEY (`clienteId`) REFERENCES `clientes` (`nickName`) ON DELETE CASCADE ON UPDATE CASCADE;


COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
