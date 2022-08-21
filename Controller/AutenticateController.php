<?php
    include 'Model/AutenticateModel.php';
    class AutenticateController {
        private $conFactory;
        function __construct() {
            $this->authModel = new AutenticateModel();
            $this->conFactory = new ConnectionFactory();
        }
        // USER 
        function login (StringT $nick,$pass) {    
            if ($this->checkLogin (new StringT($nick),$pass)) {
                $_SESSION['nickName'] = $nick;
                $this->authModel->createToken();
                header("Location: index.php");
                die();   
            } else {
                echo "<center><h3 style=\"color:red;\"> nickname ou senha incorreta </h3></center>";
            }
        }

        function checkLogin (StringT $nick,$pass) {   
            $result = $this->authModel->checkLogin($nick,$this-> encrypt($nick.$pass));  
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
                if ($this->authModel->singUp(new StringT($name),new StringT($nick),$this-> encrypt($nick.$pass))) {
                    $this->login(new StringT($nick),$pass);
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
            } else if ($this->checkNick(new StringT($nick))) {
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

        function checkNick (StringT $nick) {
            $result = $this->authModel->checkNick($nick);  
            if (mysqli_num_rows($result) > 0) {
                return true;
            } else {
                return false;
            }
        }   

        function isLogged () {
            if ($this->authModel->checkToken()) {
                return true;
            } else {
                header("Location: login.php");
                die();
                return false;
            }
        }

        function logout () {
            $this->authModel->deleteToken();
            if (isset($_SERVER['HTTP_COOKIE'])) {
                $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                foreach($cookies as $cookie) {
                    $parts = explode('=', $cookie);
                    $name = trim($parts[0]);
                    setcookie($name, '', time()-1000);
                    setcookie($name, '', time()-1000, '/');
                }
            }
            header("Location: login.php");
            die();
        }

        function encrypt($value) {
            return hash("sha512", $value,false);
        }
  
    }
?>