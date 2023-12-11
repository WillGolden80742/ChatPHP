<?php
include 'Controller/AuthenticateController.php';
include 'Controller/Sessions.php';
include 'Controller/Message.php';
class UsersModel
{
    private $conFactory;
    private $conFactoryPDO;
    private $auth;
    function __construct()
    {
        $this->conFactory = new ConnectionFactory();
        $this->conFactoryPDO = new ConnectionFactoryPDO();
        $this->auth = new AuthenticateController();
        $this->auth->isLogged();
    }


    function uploadFile($file, $msg, $nickName, $contactNickName)
    {
        if (strcmp($contactNickName, $nickName) !== 0) {
            if (!empty($msg)) {
                $this->createMessage($msg, new StringT($contactNickName), new StringT($nickName));
            } else {
                $this->createMessage(" ", new StringT($contactNickName), new StringT($nickName));
            }

            $row = mysqli_fetch_assoc($this->lasIdMessage($nickName, $contactNickName));

            if ($this->getNumberOfAttachments($row['LastID']) == 0) {
                $nomeHash = md5_file($file['tmp_name']) . '.' . $file['size'] . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);

                // Verificar se o arquivo já existe na tabela 'arquivos'
                $connection = $this->conFactoryPDO;
                $query = $connection->query("SELECT nomeHash FROM arquivos WHERE nomeHash = :nomeHash");
                $query->bindParam(':nomeHash', $nomeHash);
                $connection->execute($query);

                if (!($query->rowCount() > 0)) {
                    // Inserir o arquivo na tabela 'arquivos'
                    $query = $connection->query("INSERT INTO arquivos (nomeHash, arquivo) VALUES (:nomeHash, :arquivo)");
                    $query->bindParam(':nomeHash', $nomeHash);
                    $arquivo = file_get_contents($file['tmp_name']);
                    $query->bindParam(':arquivo', $arquivo);
                    $connection->execute($query);
                }

                // Verificar se existe um anexo referenciando a um arquivo Idmessage que pertença ao MsgTo ($contactNickName)
                $query = $connection->query("SELECT anexoId FROM anexo WHERE arquivo = :arquivo AND mensagem IN (SELECT Idmessage FROM messages WHERE MsgTo = :msgTo)");
                $query->bindParam(':arquivo', $nomeHash);
                $query->bindParam(':msgTo', $contactNickName);
                $connection->execute($query);

                if ($query->rowCount() > 0) {
                    // Atualizar o anexo existente com o ID da última mensagem enviada
                    $query = $connection->query("UPDATE anexo SET mensagem = :mensagem WHERE arquivo = :arquivo AND mensagem IN (SELECT Idmessage FROM messages WHERE MsgTo = :msgTo)");
                    $query->bindParam(':mensagem', $row['LastID']);
                    $query->bindParam(':arquivo', $nomeHash);
                    $query->bindParam(':msgTo', $contactNickName);
                    $connection->execute($query);
                } else {
                    // Inserir um novo anexo referenciando o arquivo à última mensagem enviada
                    $query = $connection->query("INSERT INTO anexo (nome, arquivo, mensagem) VALUES (:nome, :arquivo, :mensagem)");
                    $query->bindParam(':nome', $file['name']);
                    $query->bindParam(':arquivo', $nomeHash);
                    $query->bindParam(':mensagem', $row['LastID']);
                    $connection->execute($query);
                }
                // Apagar mensagens que não possuem anexos referenciados
                $query = $connection->query("DELETE FROM messages WHERE Idmessage NOT IN (SELECT mensagem FROM anexo) AND Messages = ' '");
                $connection->execute($query);
            }
        }
        return mysqli_fetch_assoc($this->lasIdMessage($nickName, $contactNickName))['LastID'];
    }




    function downloadFile($nomeHash)
    {
        $connection = $this->conFactoryPDO;
        $query = $connection->query("SELECT arquivo FROM arquivos WHERE nomeHash = :nomeHash");
        $query->bindParam(':nomeHash', $nomeHash, PDO::PARAM_STR);
        $resultado = $connection->execute($query)->fetchAll();


        foreach ($resultado as $r) {
            return $r['arquivo'];
        }
    }



    function uploadProfilePic(StringT $nick, $pic, $format)
    {
        // Recomendado uso de prepare statement 
        $connection = $this->conFactoryPDO;
        $query = $connection->query("DELETE FROM profilepicture WHERE clienteId = :nick");
        $query->bindParam(':nick', $nick, PDO::PARAM_STR);
        $connection->execute($query);
        $query = $connection->query("INSERT INTO profilepicture (clienteId,picture,format) VALUES (:nick,'" . $pic . "',:format)");
        $query->bindParam(':nick', $nick, PDO::PARAM_STR);
        $query->bindParam(':format', $format, PDO::PARAM_STR);
        $connection->execute($query);
    }

    function uploadProfile(StringT $nick, $pass, StringT $newNick, $name)
    {
        // Recomendado uso de prepare statement 
        $connection = $this->conFactoryPDO;
        $query = $connection->query("UPDATE clientes SET nickName = :newNick, nomeCliente = :name, senha = :pass WHERE nickName = :nick ");
        $query->bindParam(':newNick', $newNick);
        $query->bindParam(':name', $name);
        $query->bindParam(':pass', $pass);
        $query->bindParam(':nick', $nick);
        return $connection->execute($query);
    }

    function uploadPassword(StringT $nick, $newPass)
    {
        // Recomendado uso de prepare statement 
        $connection = $this->conFactoryPDO;
        $query = $connection->query("UPDATE clientes SET senha = :newPass WHERE nickName = :nick ");
        $query->bindParam(':newPass', $newPass);
        $query->bindParam(':nick', $nick);
        return $connection->execute($query);
    }


    function name(StringT $nick)
    {
        // Recomendado uso de prepare statement 
        $connection = $this->conFactoryPDO;
        $query = $connection->query("SELECT nomeCliente FROM clientes WHERE nickName = :user ");
        $query->bindParam(':user', $nick, PDO::PARAM_STR);
        return $connection->execute($query)->fetch(PDO::FETCH_ASSOC);
    }

    function downloadProfilePic(StringT $contactNickName)
    {
        // Recomendado uso de prepare statement 
        $connection = $this->conFactoryPDO;
        $query = $connection->query("SELECT * FROM profilepicture WHERE clienteId = :user");
        $query->bindParam(':user', $contactNickName, PDO::PARAM_STR);
        return $connection->execute($query)->fetchAll();
    }

    function searchContact(StringT $nick)
    {
        // Recomendado uso de prepare statement 
        return $this->conFactory->query("call searchContato('" . $nick . "')");
    }

    function contacts(StringT $nick)
    {
        // Recomendado uso de prepare statement 
        return $this->conFactory->query("call contatos('" . $nick . "')");
    }
    // MESSAGES 

    function messages(StringT $nickName, StringT $contactNickName,$pag=1)
    {
        $connection = $this->conFactoryPDO;
        // Excluir entrada em new_messages onde sender é $contactNickName e receiver é $nickName
        $deleteQuery = $connection->query("DELETE FROM new_messages WHERE sender = :contactNickName AND receiver = :nickName");
        $deleteQuery->bindParam(':contactNickName', $contactNickName);
        $deleteQuery->bindParam(':nickName', $nickName);
        $connection->execute($deleteQuery);
        // Recomendado uso de prepare statement 
        return $this->conFactory->query("call messagePaginated('" . $nickName . "','" . $contactNickName . "',$pag)");
    }

    function lastMessage(StringT $nickName, StringT $contactNickName)
    {
        // Recomendado uso de prepare statement 
        return $this->conFactory->query("call  lastMessage('" . $nickName . "','" . $contactNickName . "')");
    }

    function messageByID(StringT $nickName, StringT $contactNickName, StringT $id)
    {
        // Recomendado uso de prepare statement 
        return $this->conFactory->query("call  messageByID('" . $nickName . "','" . $contactNickName . "', '".$id."')");
    }

    function lasIdMessage($nick, $contactNickName)
    {
        return $this->conFactory->query("SELECT MAX(messages.Idmessage) as LastID From messages WHERE MsgFrom = '$nick' AND MsgTo = '$contactNickName'");
    }

    function createMessage($msg, StringT $contactNickName, StringT $nick)
    {
        if (strcmp($contactNickName, $nick) !== 0) {
            $connection = $this->conFactoryPDO;
    
            // Insert message into the 'messages' table
            $messageQuery = $connection->query("INSERT INTO messages (Messages, MsgFrom, MsgTo) VALUES (:msg, :nick, :contactNickName)");
            $messageQuery->bindParam(':msg', $msg);
            $messageQuery->bindParam(':nick', $nick);
            $messageQuery->bindParam(':contactNickName', $contactNickName);
            $connection->execute($messageQuery);
    
            // Get the last inserted message Id
            $getLastMessageIdQuery = $connection->query("SELECT Idmessage FROM messages WHERE (MsgFrom = :nick AND MsgTo = :contactNickName) OR (MsgFrom = :contactNickName AND MsgTo = :nick) ORDER BY Date DESC LIMIT 1");
            $getLastMessageIdQuery->bindParam(':nick', $nick);
            $getLastMessageIdQuery->bindParam(':contactNickName', $contactNickName);
            $connection->execute($getLastMessageIdQuery);
            $messageId = $getLastMessageIdQuery->fetchColumn();
    
            // Check if a row already exists in 'new_messages' table
            $checkQuery = $connection->query("SELECT * FROM new_messages WHERE sender = :nick AND receiver = :contactNickName");
            $checkQuery->bindParam(':nick', $nick);
            $checkQuery->bindParam(':contactNickName', $contactNickName);
            $connection->execute($checkQuery);
    
            if ($checkQuery->rowCount() > 0) {
                // If a row exists, update the message_count
                $updateQuery = $connection->query("UPDATE new_messages SET message_count = message_count + 1, messageId = :messageId WHERE sender = :nick AND receiver = :contactNickName");
                $updateQuery->bindParam(':messageId', $messageId);
                $updateQuery->bindParam(':nick', $nick);
                $updateQuery->bindParam(':contactNickName', $contactNickName);
                $connection->execute($updateQuery);
            } else {
                // If no row exists, create a new row
                $insertQuery = $connection->query("INSERT INTO new_messages (sender, receiver, messageId) VALUES (:nick, :contactNickName, :messageId)");
                $insertQuery->bindParam(':nick', $nick);
                $insertQuery->bindParam(':contactNickName', $contactNickName);
                $insertQuery->bindParam(':messageId', $messageId);
                $connection->execute($insertQuery);
            }
        }
    }

    function getNewMessagesForContact(StringT $contactNickName) {

        $connection = $this->conFactoryPDO;

        // Select new messages from table
        $getMessagesQuery = $connection->query("SELECT message_count FROM new_messages WHERE sender = :contactNickName");
        $getMessagesQuery->bindParam(':contactNickName', $contactNickName);
        $connection->execute($getMessagesQuery);
        
        // Get number of messages
        $numMessages = $getMessagesQuery->fetchColumn();
      
        return $numMessages;
    }
    

    function deleteMessage($id, $contactNickName, $nick)
    {
        // Verificar se existe anexo vinculado a essa mensagem
        $result = $this->conFactory->query("SELECT anexoId, arquivo FROM anexo WHERE mensagem = " . $id);
        if ($result->num_rows > 0) {
            // Verificar quantos anexos estão vinculados a esse arquivo
            $row = $result->fetch_assoc();
            $arquivo = $row['arquivo'];

            $result = $this->conFactory->query("SELECT COUNT(*) as totalAnexos FROM anexo WHERE arquivo = '" . $arquivo . "'");
            $row = $result->fetch_assoc();
            $totalAnexos = $row['totalAnexos'];

            if ($totalAnexos == 1) {
                // Apenas um anexo vinculado a esse arquivo, apagar o arquivo também
                $this->conFactory->query("DELETE FROM arquivos WHERE nomeHash = '" . $arquivo . "'");
            }
        }

        // Apagar a mensagem
        $this->conFactory->query("DELETE FROM messages WHERE Idmessage = " . $id . " AND MsgFrom = '" . $nick . "' AND MsgTo = '" . $contactNickName . "'");
    }


    function getNumberOfAttachments($lasIdMessage)
    {
        $connection = $this->conFactoryPDO;

        // Consultar o número de anexos vinculados à mensagem
        $query = $connection->query("SELECT COUNT(*) AS num_anexos FROM anexo WHERE mensagem = :mensagem");
        $query->bindParam(':mensagem', $lasIdMessage);
        $connection->execute($query);

        $result = $query->fetch(PDO::FETCH_ASSOC);
        $numAnexos = $result['num_anexos'];

        return $numAnexos;
    }
}
