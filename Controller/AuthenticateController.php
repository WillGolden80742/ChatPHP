<?php
include 'Model/AuthenticateModel.php';
class AuthenticateController
{
    private $authModel;
    function __construct()
    {
        $this->authModel = new AuthenticateModel();
    }
    // USER 
    function login(StringT $nick, $pass)
    {
        if ($this->checkLogin($nick, $pass)) {
            $_SESSION['nickName'] = $nick;
            $this->authModel->createToken();
            header("Location: index.php");
            die();
        } else {
            echo "<center class='statusMsg' onmouseover=\"removerStatusMsg();\" ><h3 style=\"color:red;\" > nickname ou senha incorreta </h3></center>";
        }
    }

    function checkLogin(StringT $nick, $pass)
    {
        $result = $this->authModel->checkLogin($nick, $this->encrypt($nick . $pass));
        if (($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function signUp($name, $nick, $pass, $passConfirmation)
    {
        $error = "";
        $nameCertification = $this->nameCertification($name);
        $nickCertification = $this->nickCertification($nick);
        $passCertification = $this->passCertification($pass, $passConfirmation);

        if ($nameCertification === "" && $nickCertification === "" && $passCertification === "") {
            if ($this->authModel->signUp($name, new StringT($nick), $this->encrypt($nick . $pass))) {
                $this->login(new StringT($nick), $pass);
            }
        } else {
            $error = "<center class='statusMsg' onmouseover=\"removerStatusMsg();\" ><h3 style=\"color:red;\">";
            if ($nameCertification !== "") {
                $error .= $nameCertification . "<br>";
            }
            if ($nickCertification !== "") {
                $error .= $nickCertification . "<br>";
            }
            if ($passCertification !== "") {
                $error .= $passCertification . "<br>";
            }
            $error .= "</h3></center>";
        }
        echo $error;
    }

    function nameCertification($name)
    {
        if (empty($name)) {
            return "O nome não pode estar vazio.";
        } else if (mb_strlen($name) > 20) {
            return "O nome deve ter no máximo 20 caracteres.";
        }
        return "";
    }

    function nickCertification($nick)
    {
        if (empty($nick)) {
            return "O nickname não pode estar vazio.";
        } else if (!preg_match("/^[a-zA-Z0-9_]+$/", $nick)) {
            return "Apenas são permitidos caracteres _, aA a zZ e 0 a 9 para o nickname.";
        } else if (strlen($nick) > 20) {
            return "O nickname deve ter no máximo 20 caracteres.";
        } else if ($this->checkNick(new StringT($nick))) {
            return "O nickname já existe.";
        }
        return "";
    }

    function passCertification($pass, $passConfirmation)
    {
        if (empty($pass)) {
            return "A senha não pode ser vazia.";
        } else if (strcmp($pass, $passConfirmation) !== 0) {
            return "As senhas não são iguais.";
        } else if (strlen($pass) < 8) {
            return "A senha não pode ser menor que 8 caracteres.";
        }
        return "";
    }


    function checkNick(StringT $nick)
    {
        $result = $this->authModel->checkNick($nick);
        if ($result > 0) {
            return true;
        } else {
            return false;
        }
    }

    function isLogged()
    {
        if ($this->authModel->checkToken()) {
            return true;
        } else {
            header("Location: authenticate.php");
            die();
            return false;
        }
    }

    function updateToken()
    {
        $this->authModel->deleteToken();
        $this->authModel->createToken();
    }

    function logout()
    {
        $this->authModel->deleteToken();
        header("Location: authenticate.php");
        die();
    }

    function encrypt($value)
    {
        return hash("sha512", $value, false);
    }
}
