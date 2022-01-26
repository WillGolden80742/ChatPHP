<?php
    include 'ConnectionFactory/ConnectionFactory.php';
    session_start();
    class AuthManager {
        private $regex = '/[^[:alpha:]_0-9]/';
        private $conFactory;
        function __construct() {
            $this->conFactory = new ConnectionFactory();
        }
        // USER 
        function login ($nick,$pass) {    
            $nick= preg_replace($this->regex,'',$nick);
            if ($this->checkLogin ($nick,$pass)) {
                $_SESSION['nickName'] = $nick;
                header("Location: index.php");
                die();   
            } else {
                echo "<center><h3 style=\"color:red;\"> nickname ou senha incorreta </h3></center>";
            }
        }

        function checkLogin ($nick,$pass) {   
            $nick= preg_replace($this->regex,'',$nick);
            $result = $this->conFactory->query("SELECT * FROM clientes where nickName = '".$nick."' and senha = '".md5($nick.$pass)."'");  
            if (mysqli_num_rows($result) > 0) {
                return true;
            } else {
                return false;
            }
        }

        function singUp ($name,$nick,$pass) { 
            if ($this->conFactory->query("INSERT INTO clientes (nomeCliente, nickName, senha) VALUES ('".$name."', '".$nick."', '".md5($nick.$pass)."')")) {
                $this->login($nick,$pass);
            } 
        }    
        
        function checkNick ($nick) {
            $result = $this->conFactory->query("SELECT * FROM clientes where nickName = '".$nick."'");  
            if (mysqli_num_rows($result) > 0) {
                return true;
            } else {
                return false;
            }
        }   

        function isLogged () {
            if (empty($_SESSION['nickName'])) {
                header("Location: login.php");
                die();
                return false;
            } else {
                return true;
            }
        }
  
    }
?>