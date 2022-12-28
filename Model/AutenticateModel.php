<?php
    include 'ConnectionFactory/ConnectionFactory.php';
    include 'ConnectionFactory/ConnectionFactoryPDO.php';
    include 'Controller/StringT.php';
    session_start();
    class AutenticateModel {
        private $conFactory;
        function __construct() {
            $this->conFactory = new ConnectionFactory();
            $this->conFactoryPDO = new ConnectionFactoryPDO();
        }
        // USER 

        function checkLogin (StringT $nick,$pass) {
            return $this->conFactory->query("SELECT * FROM clientes where nickName = '".$nick."' and senha = '".$pass."'");   
        }

        function singUp (StringT $name,StringT $nick,$pass) { 
            return $this->conFactory->query("INSERT INTO clientes (nomeCliente, nickName, senha) VALUES ('".$name."', '".$nick."', '".$pass."')");
        }    

        function checkNick (StringT $nick) {
            return $this->conFactory->query("SELECT * FROM clientes where nickName = '".$nick."'");
        }   
  
        function createToken() {
            $date = new DateTime();
            $connection = $this->conFactoryPDO;
            $query = $connection->query("DELETE FROM token WHERE clienteId =:nick");
            $query->bindParam(':nick',$_SESSION['nickName'], PDO::PARAM_STR);
            $connection->execute($query);
            $query = $connection->query("INSERT INTO token (clienteID, tokenHash) VALUES (:nick,:tokenHash)");
            $query->bindParam(':nick',$_SESSION['nickName'], PDO::PARAM_STR);
            $hash = hash("sha512",$_SESSION['nickName'].$date->getTimestamp().rand(),false);
            $_SESSION['token'] = $hash;
            $query->bindParam(':tokenHash',$hash, PDO::PARAM_STR);
            $connection->execute($query);
        }

        function checkToken() {
            $connection = $this->conFactoryPDO;
            $query =  $connection->query("SELECT * FROM token where tokenHash = :token");
            $query->bindParam(':token',$_SESSION['token'], PDO::PARAM_STR);
            return $connection->execute($query)->fetch(PDO::FETCH_ASSOC);
        }

        function deleteToken() {
            $connection = $this->conFactoryPDO;
            $query = $connection->query("DELETE FROM token WHERE clienteId =:nick");
            $query->bindParam(':nick',$_SESSION['nickName'], PDO::PARAM_STR);
            $connection->execute($query);
        }


    }
?>