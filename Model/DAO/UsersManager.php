<?php
    include 'ConnectionFactory/ConnectionFactory.php';
    session_start();
    class usersManager {
        private $conFactory;
        function __construct() {
            $this->conFactory = new ConnectionFactory();
        }
        // USER 
        function login ($nick,$pass) {    
            $nick= preg_replace('/[^[:alpha:]_]/','',$nick);
            $conn = $this->conFactory->connect();
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $result = $this->conFactory->query("SELECT * FROM clientes where nickName = '".$nick."' and senha = '".md5($nick.$pass)."'");  
            $this->conFactory->close();
            if (mysqli_num_rows($result) > 0) {
                $_SESSION['nickName'] = $nick;
                header("Location: index.php");
                die();   
            } else {
                echo "<center><h3 style=\"color:red;\"> nickname ou senha incorreta </h3></center>";
            }
        }

        function checkLogin ($nick,$pass) {   
            $nick= preg_replace('/[^[:alpha:]_]/','',$nick);
            $conn = $this->conFactory->connect();
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $result = $this->conFactory->query("SELECT * FROM clientes where nickName = '".$nick."' and senha = '".md5($nick.$pass)."'");  
            $this->conFactory->close();
            if (mysqli_num_rows($result) > 0) {
                return true;
            } else {
                return false;
            }
        }

        function singUp ($name,$nick,$pass) { 
            $conn = $this->conFactory->connect();
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            if ($this->conFactory->query("INSERT INTO clientes (nomeCliente, nickName, senha) VALUES ('".$name."', '".$nick."', '".md5($nick.$pass)."')")) {
                $this->conFactory->close();
                $this->login($nick,$pass);
            } 
        }    
        
        function uploadProfilePic ($nick,$pic,$format) {
            $conn = $this->conFactory->connect();
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $this->conFactory->query("INSERT INTO profilepicture (clienteId,picture,format) VALUES ('".$nick."','".$pic."','".$format."')");
            $this->conFactory->close();
        }

        function uploadProfile ($nick,$pass,$newNick,$name) {
            $nick= preg_replace('/[^[:alpha:]_]/','',$nick);
            $newNick= preg_replace('/[^[:alpha:]_]/','',$newNick);           
            if ($this->checkLogin($nick,$pass)) {
                if (!$this->checkNick ($newNick) || strcmp($nick,$newNick) == 0) {
                    $conn = $this->conFactory->connect();
                    if (!$conn) {
                        die("Connection failed: " . mysqli_connect_error());
                    }
                    if($this->conFactory->query("UPDATE clientes SET nickName = '".$newNick."', nomeCliente = '".$name."', senha = '".md5($newNick.$pass)."' WHERE nickName = '".$nick."' ")) {
                        $_SESSION['nickName']=$newNick;
                        header("Location: editProfile.php?message=Alteração com sucesso!");
                        die();
                    }
                    $this->conFactory->close();
                } else if ($this->checkNick ($newNick)) {
                    header("Location: editProfile.php?error=@".$newNick." já existente");
                    die();
                }
            } else {
                header("Location: editProfile.php?error=senha incorreta");
                die();
            }         
            echo "Erro de especificações";
        }

        function uploadPassword ($nick,$pass,$newPass,$newPassConfirmation) {
            if ($this->checkLogin($nick,$pass)) {
                if (strcmp($newPass,$newPassConfirmation) == 0) {
                    $this->conFactory->connect();
                    if($this->conFactory->query("UPDATE clientes SET senha = '".md5($nick.$newPass)."' WHERE nickName = '".$nick."' ")) {
                        header("Location: editPassword.php?message=Senha alterada com sucesso!");
                        die();
                    }
                    $this->conFactory->close();
                } else {
                    header("Location: editPassword.php?error=senha de confirmação não coincide");
                    die();
                }
            } else {
                header("Location: editPassword.php?error=senha incorreta");
                die();
            }        
        }        

        function checkNick ($nick) {
            $conn = $this->conFactory->connect();
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $result = $this->conFactory->query("SELECT * FROM clientes where nickName = '".$nick."'");  
            $this->conFactory->close();
            if (mysqli_num_rows($result) > 0) {
                return true;
            } else {
                return false;
            }
        }   
        
        function name($nick) {
            $conn = $this->conFactory->connect();
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $result =  $this->conFactory->query("SELECT nomeCliente FROM clientes WHERE nickName ='".$nick."'");
            while($row = mysqli_fetch_assoc($result)) { 
                return $row["nomeCliente"];
            }
        }

        function contacts ($nick) {
            $conn = $this->conFactory->connect();
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $result =  $this->conFactory->query("call contatos('".$nick."')");
            $count=0;
            $contacts = array();
            while($row = mysqli_fetch_assoc($result)) { 
                $contacts[$count++]=array($row["Contato"],$row["nickNameContato"]);
            }
            $conn->close();
            return $contacts;
        }

        function searchContact ($nick) {
            $conn = $this->conFactory->connect();
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $result =  $this->conFactory->query("call searchContato('".$nick."')");
            $count=0;
            $contacts = array();
            while($row = mysqli_fetch_assoc($result)) { 
                $contacts[$count++]=array($row["Contato"],$row["nickNameContato"]);
            }
            $conn->close();
            return $contacts;
        }


        function downloadProfilePic ($contactNickName) {
            $conn = $this->conFactory->connect();
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $result = $this->conFactory->query("SELECT * FROM profilepicture WHERE clienteId = '".$contactNickName."'");
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $pic = "data:image/jpeg;base64," . base64_encode($row["picture"]);
                }
            } else {
                $pic = "Images/profilePic.png";
            }
            $conn->close();
            return $pic;
        }        
        
        // MESSAGES 

        function messages ($contactNickName) {
            $conn = $this->conFactory->connect();
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $result = $this->conFactory->query("call messages('".$_SESSION['nickName']."','".$contactNickName."')");
            $messages = array();
            if (mysqli_num_rows($result) > 0) {
              $idMessage = '0';   
              $count=0;
              while($row = mysqli_fetch_assoc($result)) {
                if (strcmp($row["Idmessage"],$idMessage) !== 0) {
                    if (!empty($row["Messages"])) {
                        if (strcmp($row["MsgFrom"], $contactNickName) == 0 ) {
                            $from = "From : @".$row["MsgFrom"];
                            $left = true;
                        } else {
                            $left = false;
                            $from = "You : ";
                        }                      
                        $message = $row["Messages"]; 
                        $hour = $row["HourMsg"];  
                        $id = $row["Idmessage"];
                        $messages[$count++] = array($from,$message,$hour,$id,$left); 
                    }           
                } 
                $idMessage = "".$row["Idmessage"];
              }
            } 
            $conn->close();
            return $messages;
        }

        function createMessage ($msg,$contactNickName) { 
            if (!empty($_SESSION['nickName'])) {
                $conn = $this->conFactory->connect();
                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }
                $this->conFactory->query("INSERT INTO messages (Messages, MsgFrom, MsgTo) VALUES ('".$msg."', '".$_SESSION['nickName']."', '".$contactNickName."')");
                $conn->close();
                header("Location: messages.php?contactNickName=".$contactNickName);
                die(); 
            } else {
                header("Location: login.php");
                die(); 
            }
        }

        function deleteMessage ($id,$contactNickName) { 
            $conn = $this->conFactory->connect();
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $this->conFactory->query("call deleteMessage(".$id.",'".$_SESSION['nickName']."')");
            $conn->close();
            header("Location: messages.php?contactNickName=".$contactNickName);
            die(); 
        }
  
    }
?>