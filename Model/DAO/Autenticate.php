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

        function singUp ($name,$nick,$pass,$passConfirmation) { 
            echo $error = "";
            $nameCertification = $this->nameCertification($name);
            $nickCertification = $this->nickCertification($nick);
            $passCertification = $this->passCertification ($pass,$passConfirmation);
            if ($nameCertification[0] && $nickCertification[0] && $passCertification[0]) {
                if ($this->conFactory->query("INSERT INTO clientes (nomeCliente, nickName, senha) VALUES ('".$name."', '".$nick."', '".md5($nick.$pass)."')")) {
                    $this->login($nick,$pass);
                } 
            } else {
                $error = "<center><h3 style=\"color:red;\">";
                $error.=$nameCertification[1];
                $error.=$nickCertification[1];
                $error.=$passCertification[1];
                $error.="</h3></center>";
            }
            return $error;
        }    

        function nameCertification($name) {
            $error = "";
            $nameTreated=false;
            if (empty($name)) {
                $error.="nome não pode ser vazia,";
            } else if (!preg_match("/^[a-zA-Z0-9_ ]+$/", $name)) {
                $error.=" permitido apenas _, aA a zZ e 0 a 9 para name,";
            } else {
                $nameTreated = true;
            }
            return array($nameTreated,$error);
        }

        function nickCertification ($nick) {
            $error = "";
            $nickTreated = false;
            if (empty($nick)) {
                $error.=" nickname não pode ser vazia,";
            }  else if (!preg_match("/^[a-zA-Z0-9_]+$/", $nick)) {
                $error.=" permitido apenas _, aA a zZ e 0 a 9 para nick name,";
            } else if ($this->checkNick($nick)) {
                $error.=" nickname já existente,";
            } else {
                $nickTreated = true;
            }
            return array($nickTreated,$error);
        }
        
        function passCertification ($pass,$passConfirmation) {
            $error = "";
            $passTreated = false;
            if (empty($pass)) {
                $error.=" senhas não pode ser vazia,";
            } else if (strcmp($pass,$passConfirmation) !== 0) { 
                $error.=" senhas não são iguais";
            } else if (strlen($pass) < 8) {
                $error.=" senha não pode ser menor que 8 caracteres";
            } else {
                $passTreated = true;
            }
            return array ($passTreated,$error);
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