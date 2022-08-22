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

        function createMessage (Message $msg,StringT $contactNickName,StringT $nick) { 
            // Recomendado uso de prepare statement 
            $this->conFactory->query("INSERT INTO messages (Messages, MsgFrom, MsgTo) VALUES ('".$msg."', '".$nick."', '".$contactNickName."')");
            $this->conFactory->query("INSERT INTO newMsg (msgFrom, msgTo) VALUES ('".$nick."','".$contactNickName."')");
        }

        function deleteMessage (StringT $id,StringT $contactNickName,StringT $nick) {
            // Recomendado uso de prepare statement 
            $this->conFactory->query("call deleteMessage(".$id.",'".$nick."')");
            $this->conFactory->query("INSERT INTO newMsg (msgFrom, msgTo) VALUES ('".$nick."','".$contactNickName."')");
            $this->conFactory->query("UPDATE messages SET received = '2' WHERE messages.MsgFrom = '".$nick."' and messages.MsgTo = '".$contactNickName."'");       
        }
        
  
    }
?>