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

    function messages(StringT $nickName, StringT $contactNickName)
    {
        // Recomendado uso de prepare statement 
        return $this->conFactory->query("call messages('" . $nickName . "','" . $contactNickName . "')");
    }


    function hasNewMsgByCurrentContact(StringT $contactNickName, StringT $nick)
    {
        // Recomendado uso de prepare statement 
        return $this->conFactory->query("SELECT COUNT(messages.Idmessage) as countMsg FROM messages WHERE messages.MsgFrom = '" . $contactNickName . "' AND messages.MsgTo = '" . $nick . "' AND messages.received = '0'");
    }

    function isDeleteMessage(StringT $contactNickName, StringT $nick)
    {
        // Recomendado uso de prepare statement 
        return $this->conFactory->query("SELECT COUNT(messages.Idmessage) as countMsg FROM messages WHERE messages.MsgFrom = '" . $contactNickName . "' AND messages.MsgTo = '" . $nick . "' AND messages.received = '2'");
    }

    function newMsg(StringT $contactNickName, StringT $nick, $value)
    {
        // Recomendado uso de prepare statement 
        return $this->conFactory->query("call newMsg('" . $nick . "','" . $contactNickName . "','" . $value . "')");
    }

    function hasNewMsgByContact(StringT $nick)
    {
        // Recomendado uso de prepare statement 
        return $this->conFactory->query("call newMsgs('" . $nick . "')");
    }

    function delMsg(StringT $nick)
    {
        // Recomendado uso de prepare statement 
        $this->conFactory->query("DELETE FROM newMsg WHERE msgTo = '" . $nick . "'");
    }

    function receivedMsg(StringT $contactNickName, StringT $nick)
    {
        // Recomendado uso de prepare statement 
        $this->conFactory->query("UPDATE messages SET received = 1 WHERE messages.MsgFrom = '" . $contactNickName . "' and messages.MsgTo = '" . $nick . "'");
    }

    function lasIdMessage($nick, $contactNickName)
    {
        return $this->conFactory->query("SELECT MAX(messages.Idmessage) as LastID From messages WHERE MsgFrom = '$nick' AND MsgTo = '$contactNickName'");
    }

    function createMessage($msg, StringT $contactNickName, StringT $nick)
    {
        if (strcmp($contactNickName, $nick) !== 0) {
            $connection = $this->conFactoryPDO;
            $query = $connection->query("INSERT INTO messages (Messages, MsgFrom, MsgTo) VALUES (:msg, :nick,:contactNickName)");
            $query->bindParam(':msg', $msg);
            $query->bindParam(':nick', $nick);
            $query->bindParam(':contactNickName', $contactNickName);
            $connection->execute($query);
            $this->conFactory->query("INSERT INTO newMsg (msgFrom, msgTo) VALUES ('" . $nick . "','" . $contactNickName . "')");
        }
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
        $this->conFactory->query("DELETE FROM messages WHERE Idmessage = " . $id);

        // Inserir nova mensagem
        $this->conFactory->query("INSERT INTO newMsg (msgFrom, msgTo) VALUES ('" . $nick . "','" . $contactNickName . "')");

        // Atualizar o status da mensagem para '2'
        $this->conFactory->query("UPDATE messages SET received = '2' WHERE MsgFrom = '" . $nick . "' AND MsgTo = '" . $contactNickName . "'");
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
