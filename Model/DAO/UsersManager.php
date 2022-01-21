<?php
    include 'ConnectionFactory/ConnectionFactory.php';
    session_start();
    class usersManager {
        private $conFactory;
        function __construct() {
            $this->conFactory = new ConnectionFactory();
        }
        
        function login ($nick,$pass) {
            $conn = $this->conFactory->connect();
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $result = $this->conFactory->query("SELECT * FROM clientes where nickName = '".$nick."' and senha = '".md5($nick.$pass)."'");  
            $this->conFactory->close();
            if (mysqli_num_rows($result) > 0) {
                $_SESSION['nickName'] = $nick;
                return true;
            } else {
                return false;
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
                $contacts[$count++]=$row["nickNameContato"];
            }
            $conn->close();
            return $contacts;
        }

        function messages ($contactNickName) {
            $conn = $this->conFactory->connect();
            $result = $this->conFactory->query("call messages('".$_SESSION['nickName']."','".$contactNickName."')");
            $messages = array();
            if (mysqli_num_rows($result) > 0) {
              $idMessage = '0';   
              $count=0;
              while($row = mysqli_fetch_assoc($result)) {
                if (strcmp($row["Idmessage"],$idMessage) !== 0) {
                    if (!empty($row["Messages"])) {
                        if (strcmp($row["MsgFrom"], $contactNickName) == 0 ) {
                            $from = "From : ".$row["MsgFrom"];
                            $left = true;
                        } else {
                            $left = false;
                            $from = "Você : ";
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
            $conn = $this->conFactory->connect();
            $this->conFactory->query("INSERT INTO messages (Messages, MsgFrom, MsgTo) VALUES ('".$msg."', '".$_SESSION['nickName']."', '".$contactNickName."')");
            $conn->close();
            header("Location: messages.php?contactNickName=".$contactNickName);
            die(); 
        }

        function deleteMessage ($id,$contactNickName) { 
            $conn = $this->conFactory->connect();
            $this->conFactory->query("call deleteMessage(".$id.",'".$_SESSION['nickName']."')");
            $conn->close();
            header("Location: messages.php?contactNickName=".$contactNickName);
            die(); 
        }
  
    }
?>