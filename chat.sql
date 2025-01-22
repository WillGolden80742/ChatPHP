DELIMITER $$

CREATE PROCEDURE `contatos` (IN `userNickName` VARCHAR(20))
NO SQL
SELECT LOWER(clientes.nickName) as nickNameContato, LOWER(clientes.nomeCliente) AS Contato, messages.Messages, MAX(messages.date) as DateFormated
FROM clientes
INNER JOIN messages ON LOWER(messages.MsgFrom) = LOWER(clientes.nickName) OR LOWER(messages.MsgTo) = LOWER(clientes.nickName)
WHERE (LOWER(messages.MsgFrom) = LOWER(userNickName) AND LOWER(clientes.nickName) != LOWER(userNickName))
   OR (LOWER(messages.MsgTo) = LOWER(userNickName) AND LOWER(clientes.nickName) != LOWER(userNickName))
GROUP BY nickNameContato
ORDER BY DateFormated DESC$$

CREATE PROCEDURE `deleteMessage` (IN `idMsg` INT(20), IN `nickName` VARCHAR(20))
NO SQL
DELETE FROM messages
WHERE messages.Idmessage = idMsg AND LOWER(messages.MsgFrom) = LOWER(nickName)$$

CREATE PROCEDURE `lastMessage` (IN `nickName` VARCHAR(20), IN `contactNickName` VARCHAR(20))
NO SQL
BEGIN
    SELECT m.*, DATE_FORMAT(m.date, '%H:%i') AS HourMsg, an.nome AS nome_anexo, an.arquivo AS arquivo_anexo
    FROM messages m
    LEFT JOIN anexo an ON m.Idmessage = an.mensagem
    WHERE (LOWER(m.MsgFrom) = LOWER(contactNickName) AND LOWER(m.MsgTo) = LOWER(nickName))
       OR (LOWER(m.MsgFrom) = LOWER(nickName) AND LOWER(m.MsgTo) = LOWER(contactNickName))
    ORDER BY m.date DESC
    LIMIT 1;
END$$

CREATE PROCEDURE `messageByID` (IN `nickName` VARCHAR(20), IN `contactNickName` VARCHAR(20), IN `messageId` INT)
NO SQL
BEGIN
    SELECT m.*, DATE_FORMAT(m.date, '%H:%i') AS HourMsg, an.nome AS nome_anexo, an.arquivo AS arquivo_anexo
    FROM messages m
    LEFT JOIN anexo an ON m.Idmessage = an.mensagem
    WHERE ((LOWER(m.MsgFrom) = LOWER(contactNickName) AND LOWER(m.MsgTo) = LOWER(nickName))
        OR (LOWER(m.MsgFrom) = LOWER(nickName) AND LOWER(m.MsgTo) = LOWER(contactNickName)))
        AND m.Idmessage = messageId
    ORDER BY m.date DESC
    LIMIT 1;
END$$

CREATE PROCEDURE `messagePaginated` (IN `nickName` VARCHAR(20), IN `contactNickName` VARCHAR(20), IN `partNumber` INT)
NO SQL
BEGIN
    DECLARE startIdx INT;
    DECLARE pageSize INT;
    
    SET pageSize = 10; -- Número de mensagens por página
    
    -- Calcula o índice de início da seleção com base na parte fornecida
    SET startIdx = (partNumber - 1) * pageSize;
    
    SELECT m.*, DATE_FORMAT(m.date, '%H:%i') AS HourMsg,
           an.nome AS nome_anexo, an.arquivo AS arquivo_anexo
    FROM messages m
    LEFT JOIN anexo an ON m.Idmessage = an.mensagem
    WHERE (LOWER(m.MsgFrom) = LOWER(contactNickName) AND LOWER(m.MsgTo) = LOWER(nickName))
       OR (LOWER(m.MsgFrom) = LOWER(nickName) AND LOWER(m.MsgTo) = LOWER(contactNickName))
    ORDER BY m.date DESC -- Ordena por data mais recente primeiro
    LIMIT startIdx, pageSize;
END$$

CREATE PROCEDURE `messages` (IN `nickName` VARCHAR(20), IN `contactNickName` VARCHAR(20))
NO SQL
SELECT m.*, DATE_FORMAT(m.date, '%H:%i') AS HourMsg, an.nome AS nome_anexo, an.arquivo AS arquivo_anexo
FROM messages m
LEFT JOIN anexo an ON m.Idmessage = an.mensagem
WHERE (LOWER(m.MsgFrom) = LOWER(contactNickName) AND LOWER(m.MsgTo) = LOWER(nickName))
   OR (LOWER(m.MsgFrom) = LOWER(nickName) AND LOWER(m.MsgTo) = LOWER(contactNickName))
ORDER BY DATE_FORMAT(m.date, '%Y/%m/%d %H:%i:%s') ASC$$

CREATE PROCEDURE `searchContato` (IN `contactNickName` VARCHAR(20))
NO SQL
SELECT LOWER(clientes.nomeCliente) as Contato, LOWER(clientes.nickName) as nickNameContato
FROM clientes
WHERE LOWER(clientes.nickName) LIKE CONCAT("%", LOWER(contactNickName), "%")
   OR LOWER(clientes.nomeCliente) LIKE CONCAT("%", LOWER(contactNickName), "%")$$

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
);

-- --------------------------------------------------------

--
-- Estrutura para tabela `arquivos`
--

CREATE TABLE `arquivos` (
  `nomeHash` varchar(300) NOT NULL,
  `arquivo` longblob NOT NULL
);

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `nomeCliente` varchar(20) NOT NULL,
  `nickName` varchar(20) NOT NULL,
  `senha` varchar(128) NOT NULL
);

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`nomeCliente`, `nickName`, `senha`) VALUES
('Alyssom', 'ally77', '0e3a1b6281353561aa19e3423d9737168e4247aae6194a3c366d09ef75be3e26a814a61420974cfc99534a79e5114af4d6aee63028a2203e2d06d4e3fe08dadd'),
('Гуммо', 'gvmmo', '73ff16a44c59b42ffaaedebd4958b46c31b0081690fe4f7c2eddbb4afc17929d50daab5a17157d38d8d2e3e8f29808f8a8fa6ec97b99337c4b852c1bd56a3368'),
('Eloa', 'lola', '4b92d552c953acd11c6370b4f107518c00df07e6ca99269a1dae70bdb7d1747966e1fd25c28e2d265a46851b02d3ef36ceb131d6c70b7e51ddb26aed92a9415c'),
('Уилл Голден', 'willGolden', 'd6267868582bfe4eda5e83b57dd9faf72188000ab78b2b94f5bdf0f21774284a54d1bcb5fe84dc8979e086c6749bb5e8228f7ceead35adbe1333596f709d589e');

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
);

-- --------------------------------------------------------

--
-- Estrutura para tabela `new_messages`
--

CREATE TABLE `new_messages` (
  `sender` varchar(20) NOT NULL,
  `receiver` varchar(20) NOT NULL,
  `message_count` int(11) NOT NULL DEFAULT 1
);

-- --------------------------------------------------------

--
-- Estrutura para tabela `profilepicture`
--

CREATE TABLE `profilepicture` (
  `clienteId` varchar(20) NOT NULL,
  `picture` longblob NOT NULL,
  `format` varchar(20) NOT NULL,
  `updated` varchar(20) NOT NULL DEFAULT current_timestamp()
);

-- --------------------------------------------------------

--
-- Estrutura para tabela `token`
--

CREATE TABLE `token` (
  `clienteID` varchar(256) NOT NULL,
  `tokenHash` varchar(256) NOT NULL
);

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
  ADD KEY `receiver` (`receiver`);

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
  ADD CONSTRAINT `new_messages_ibfk_2` FOREIGN KEY (`receiver`) REFERENCES `clientes` (`nickName`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `profilepicture`
--
ALTER TABLE `profilepicture`
  ADD CONSTRAINT `clienteId` FOREIGN KEY (`clienteId`) REFERENCES `clientes` (`nickName`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

