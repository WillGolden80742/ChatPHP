<?php
    include 'Controller/AutenticateController.php';
    include 'Controller/Sessions.php';
    include 'Controller/Message.php';
    class UsersModel {
        private $conFactory;
        private $auth;
        function __construct() {
            $this->conFactory = new ConnectionFactory();
            $this->conFactoryPDO = new ConnectionFactoryPDO();            
            $this->auth = new AutenticateController();
            $this->auth->isLogged();
        } 
        

        function uploadFile($file,$msg,$nickName, $contactNickName) {

            if (!empty($msg)) {
                $this->createMessage($msg,new StringT($contactNickName),new StringT($nickName));
            } else {
                $this->createMessage(" ",new StringT($contactNickName),new StringT($nickName));
            }

            $row = mysqli_fetch_assoc($this->lasIdMessage($nickName, $contactNickName));
            
            if ($this->getNumberOfAttachments($row['LastID']) == 0) {
            
                $nomeHash = md5_file($file['tmp_name']) . '.' . $file['size'] . '.'. pathinfo($file['name'], PATHINFO_EXTENSION);
            
                // Verificar se o arquivo já existe na tabela 'arquivos'
                $connection = $this->conFactoryPDO;
                $query = $connection->query("SELECT nomeHash FROM arquivos WHERE nomeHash = :nomeHash");
                $query->bindParam(':nomeHash', $nomeHash);
                $connection->execute($query);

                $connection = $this->conFactoryPDO;
            
                if (!($query->rowCount() > 0))  {
                    // Inserir o arquivo na tabela 'arquivos'
                                

                    
                    $query = $connection->query("INSERT INTO arquivos (nomeHash, arquivo) VALUES (:nomeHash, :arquivo)");
                    $query->bindParam(':nomeHash', $nomeHash);
                    
                    $arquivo = file_get_contents($file['tmp_name']);
                    $query->bindParam(':arquivo', $arquivo);
                    
                    $connection->execute($query);
                }  


                // Inserir o registro na tabela 'anexo'
                $query = $connection->query("INSERT INTO anexo (nome, arquivo, mensagem) VALUES (:nome, :arquivo, :mensagem)");
                $query->bindParam(':nome', $file['name']);
                $query->bindParam(':arquivo', $nomeHash);
                $query->bindParam(':mensagem', $row['LastID']);
                $connection->execute($query);
            }
            
        }  


        function downloadFile($nomeHash) {
            $connection = $this->conFactoryPDO;
            $query = $connection->query("SELECT arquivo FROM arquivos WHERE nomeHash = :nomeHash");
            $query->bindParam(':nomeHash', $nomeHash,PDO::PARAM_STR);
            $resultado = $connection->execute($query)->fetchAll();

      
            foreach ($resultado as $r) {
                echo base64_encode($r['arquivo']);
            }
        }
        


        function uploadProfilePic (StringT $nick,$pic,$format) {
            // Recomendado uso de prepare statement 
            $connection = $this->conFactoryPDO;
            $query = $connection->query("DELETE FROM profilepicture WHERE clienteId = :nick");
            $query->bindParam(':nick',$nick, PDO::PARAM_STR);
            $connection->execute($query);
            $query = $connection->query("INSERT INTO profilepicture (clienteId,picture,format) VALUES (:nick,'".$pic."',:format)");
            $query->bindParam(':nick',$nick, PDO::PARAM_STR);
            $query->bindParam(':format',$format, PDO::PARAM_STR);
            $connection->execute($query);
        }

        function uploadProfile (StringT $nick,$pass,StringT $newNick,$name) {   
            // Recomendado uso de prepare statement 
            $connection = $this->conFactoryPDO;
            $query = $connection->query("UPDATE clientes SET nickName = :newNick, nomeCliente = :name, senha = :pass WHERE nickName = :nick ");
            $query->bindParam(':newNick',$newNick);
            $query->bindParam(':name',$name);
            $query->bindParam(':pass',$pass);
            $query->bindParam(':nick',$nick);
            return $connection->execute($query);    
        }

        function uploadPassword (StringT $nick,$newPass) { 
            // Recomendado uso de prepare statement 
            $connection = $this->conFactoryPDO;
            $query = $connection->query("UPDATE clientes SET senha = :newPass WHERE nickName = :nick ");
            $query->bindParam(':newPass',$newPass);
            $query->bindParam(':nick',$nick);
            return $connection->execute($query);    
        }        
        

        function name(StringT $nick) {
            // Recomendado uso de prepare statement 
            $connection = $this->conFactoryPDO;
            $query = $connection->query("SELECT nomeCliente FROM clientes WHERE nickName = :user ");
            $query->bindParam(':user',$nick, PDO::PARAM_STR);
            return $connection->execute($query)->fetch(PDO::FETCH_ASSOC);    
        }

        function downloadProfilePic (StringT $contactNickName) {
            // Recomendado uso de prepare statement 
            $connection = $this->conFactoryPDO;
            $query = $connection->query("SELECT * FROM profilepicture WHERE clienteId = :user");
            $query->bindParam(':user',$contactNickName, PDO::PARAM_STR);
            return $connection->execute($query)->fetchAll(); 
        }  

        function searchContact (StringT $nick) {
            // Recomendado uso de prepare statement 
            return $this->conFactory->query("call searchContato('".$nick."')");
        }
        
        function contacts (StringT $nick) {
            // Recomendado uso de prepare statement 
            return $this->conFactory->query("call contatos('".$nick."')");
        }
        // MESSAGES 
        
        function messages (StringT $nickName,StringT $contactNickName) {
            // Recomendado uso de prepare statement 
            return $this->conFactory->query("call messages('".$nickName."','".$contactNickName."')");
        }


        function newCurrentMsgs (StringT $contactNickName,StringT $nick){
            // Recomendado uso de prepare statement 
            return $this->conFactory->query("SELECT COUNT(messages.Idmessage) as countMsg FROM messages WHERE messages.MsgFrom = '".$contactNickName."' AND messages.MsgTo = '".$nick."' AND messages.received = '0'");
        }

        function isDeleteMessage (StringT $contactNickName,StringT $nick) {
            // Recomendado uso de prepare statement 
            return $this->conFactory->query("SELECT COUNT(messages.Idmessage) as countMsg FROM messages WHERE messages.MsgFrom = '".$contactNickName."' AND messages.MsgTo = '".$nick."' AND messages.received = '2'");
        }

        function newMsg (StringT $contactNickName,StringT $nick,$value) {
            // Recomendado uso de prepare statement 
            return $this->conFactory->query("call newMsg('".$nick."','".$contactNickName."','".$value."')");
        }

        function newContacts (StringT $nick) {
            // Recomendado uso de prepare statement 
            return $this->conFactory->query("call newMsgs('".$nick."')");
        }  
        
        function delMsg (StringT $nick) {
            // Recomendado uso de prepare statement 
            $this->conFactory->query("DELETE FROM newMsg WHERE msgTo = '".$nick."'");
        }

        function receivedMsg (StringT $contactNickName,StringT $nick) {
            // Recomendado uso de prepare statement 
            $this->conFactory->query("UPDATE messages SET received = 1 WHERE messages.MsgFrom = '".$contactNickName."' and messages.MsgTo = '".$nick."'");
        }
        
        function lasIdMessage ($nick,$contactNickName) {
            return $this->conFactory->query("SELECT MAX(messages.Idmessage) as LastID From messages WHERE MsgFrom = '$nick' AND MsgTo = '$contactNickName'");  
        }

        function createMessage ($msg,StringT $contactNickName,StringT $nick) { 
            // Recomendado uso de prepare statement 
            $connection = $this->conFactoryPDO;
            $query = $connection->query("INSERT INTO messages (Messages, MsgFrom, MsgTo) VALUES (:msg, :nick,:contactNickName)");
            $query->bindParam(':msg',$msg);
            $query->bindParam(':nick',$nick);
            $query->bindParam(':contactNickName',$contactNickName);
            $connection->execute($query);  
            $this->conFactory->query("INSERT INTO newMsg (msgFrom, msgTo) VALUES ('".$nick."','".$contactNickName."')");
        }

        function deleteMessage (StringT $id,StringT $contactNickName,StringT $nick) {
            // Recomendado uso de prepare statement 
            $this->conFactory->query("call deleteMessage(".$id.",'".$nick."')");
            $this->conFactory->query("INSERT INTO newMsg (msgFrom, msgTo) VALUES ('".$nick."','".$contactNickName."')");
            $this->conFactory->query("UPDATE messages SET received = '2' WHERE messages.MsgFrom = '".$nick."' and messages.MsgTo = '".$contactNickName."'");       
        }
        
        function getNumberOfAttachments($lasIdMessage) {
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


?>