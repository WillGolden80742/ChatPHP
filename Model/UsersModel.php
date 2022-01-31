<?php
    include 'Controller/AutenticateController.php';
    include 'Controller/Sessions.php';
    class UsersModel {
        private $conFactory;
        private $auth;
        function __construct() {
            $this->conFactory = new ConnectionFactory();
            $this->auth = new AutenticateController();
            $this->auth->isLogged();
        } 
        
        function uploadProfilePic (StringT $nick,$pic,$format) {
            $this->conFactory->query("DELETE FROM profilepicture WHERE clienteId = '".$nick."'");
            $this->conFactory->query("INSERT INTO profilepicture (clienteId,picture,format) VALUES ('".$nick."','".$pic."','".$format."')");
        }

        function uploadProfile (StringT $nick,$pass,StringT $newNick,$name) {   
            return $this->conFactory->query("UPDATE clientes SET nickName = '".$newNick."', nomeCliente = '".$name."', senha = '".$pass."' WHERE nickName = '".$nick."' ");      
        }

        function uploadPassword ($nick,$newPass) { 
            return $this->conFactory->query("UPDATE clientes SET senha = '".$newPass."' WHERE nickName = '".$nick."' ");    
        }        
        
        function name(StringT $nick) {
           return $this->conFactory->query("SELECT nomeCliente FROM clientes WHERE nickName ='".$nick."'");
        }

        function contacts (StringT $nick) {
            return $this->conFactory->query("call contatos('".$nick."')");
        }

        function searchContact (StringT $nick) {
            return $this->conFactory->query("call searchContato('".$nick."')");
        }

        function downloadProfilePic ($contactNickName) {
            return $this->conFactory->query("SELECT * FROM profilepicture WHERE clienteId = '".$contactNickName."'");
        }  
        
        // MESSAGES 
        
        function messages (StringT $nickName,StringT $contactNickName) {
            return $this->conFactory->query("call messages('".$nickName."','".$contactNickName."')");
        }

        function newCurrentMsgs (StringT $contactNickName,$nick){
            return $this->conFactory->query("SELECT COUNT(messages.Idmessage) as countMsg FROM messages WHERE messages.MsgFrom = '".$contactNickName."' AND messages.MsgTo = '".$nick."' AND messages.received = '0'");
        }

        function isDeleteMessage (StringT $contactNickName,$nick) {
            return $this->conFactory->query("SELECT COUNT(messages.Idmessage) as countMsg FROM messages WHERE messages.MsgFrom = '".$contactNickName."' AND messages.MsgTo = '".$nick."' AND messages.received = '2'");
        }

        function newMsg (StringT $contactNickName,$nick,$value) {
            return $this->conFactory->query("call newMsg('".$nick."','".$contactNickName."','".$value."')");
        }

        function newContacts ($nick) {
            return $this->conFactory->query("call newMsgs('".$nick."')");
        }  
        
        function delMsg ($nick) {
            $this->conFactory->query("DELETE FROM newMsg WHERE msgTo = '".$nick."'");
        }

        function receivedMsg ($contactNickName, $nick) {
            $this->conFactory->query("UPDATE messages SET received = 1 WHERE messages.MsgFrom = '".$contactNickName."' and messages.MsgTo = '".$nick."'");
        }

        function createMessage ($msg,$contactNickName, $nick) { 
            $this->conFactory->query("INSERT INTO messages (Messages, MsgFrom, MsgTo) VALUES ('".$msg."', '".$nick."', '".$contactNickName."')");
            $this->conFactory->query("INSERT INTO newMsg (msgFrom, msgTo) VALUES ('".$nick."','".$contactNickName."')");
        }

        function deleteMessage (StringT $id,StringT $contactNickName,$nick) {
            $this->conFactory->query("call deleteMessage(".$id.",'".$nick."')");
            $this->conFactory->query("INSERT INTO newMsg (msgFrom, msgTo) VALUES ('".$nick."','".$contactNickName."')");
            $this->conFactory->query("UPDATE messages SET received = '2' WHERE messages.MsgFrom = '".$nick."' and messages.MsgTo = '".$contactNickName."'");       
        }
  
    }
?>