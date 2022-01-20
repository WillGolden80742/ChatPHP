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
            $this->conFactory->close();
            return $contacts;
        }

        function messages ($contactNickName) {
            $conn = $this->conFactory->connect();
            $sql = "call messagesWithAttachment('".$_SESSION['nickName']."','".$contactNickName."')";
            $result = $this->conFactory->query($sql);
            $messages = array();
            if (mysqli_num_rows($result) > 0) {
              $idMessage = '0';   
              $count=0;
              while($row = mysqli_fetch_assoc($result)) {
                if (strcmp($row["Idmessage"],$idMessage) !== 0) {
                    if (strcmp($row["MsgFrom"], $contactNickName) == 0 ) {
                        $from = "From : ".$row["MsgFrom"];
                    } else {
                        $from = "You : ";
                    } 
                    $hashArquivo=$row["hashArquivo"];                      
                    $message = $row["messages"]; 
                    $hour = $row["HourMsg"];              
                } 
                $idMessage = "".$row["Idmessage"];
                $messages[$count++] = array($from,$hashArquivo,$message,$hour);
              }
            } 
            $this->conFactory->close();
            return $messages;
        }
  
    }
?>