<?php
    include 'ConnectionFactory/ConnectionFactory.php';
    include 'Controller/StringT.php';
    session_start();
    class AutenticateModel {
        private $conFactory;
        function __construct() {
            $this->conFactory = new ConnectionFactory();
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
  
    }
?>