<?php
    include 'ConnectionFactory/ConnectionFactory.php';
    session_start();
    class usersManager {
        private $regex = '/[^[:alpha:]_0-9]/';
        private $conFactory;
        function __construct() {
            $this->conFactory = new ConnectionFactory();
        }
        // USER 
        function login ($nick,$pass) {    
            $nick= preg_replace($this->regex,'',$nick);
            $result = $this->conFactory->query("SELECT * FROM clientes where nickName = '".$nick."' and senha = '".md5($nick.$pass)."'");  
            if (mysqli_num_rows($result) > 0) {
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
        
        function uploadProfilePic ($nick,$pic,$format) {
            $this->conFactory->query("DELETE FROM profilepicture WHERE clienteId = '".$nick."'");
            $this->conFactory->query("INSERT INTO profilepicture (clienteId,picture,format) VALUES ('".$nick."','".$pic."','".$format."')");
        }

        function uploadProfile ($nick,$pass,$newNick,$name) {
            $nick= preg_replace($this->regex,'',$nick);
            $newNick= preg_replace($this->regex,'',$newNick);           
            if ($this->checkLogin($nick,$pass)) {
                if (!$this->checkNick ($newNick) || strcmp($nick,$newNick) == 0) {
                    if($this->conFactory->query("UPDATE clientes SET nickName = '".$newNick."', nomeCliente = '".$name."', senha = '".md5($newNick.$pass)."' WHERE nickName = '".$nick."' ")) {
                        $_SESSION['nickName']=$newNick;
                        header("Location: editProfile.php?message=Alteração com sucesso!");
                        die();
                    }
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
                    if($this->conFactory->query("UPDATE clientes SET senha = '".md5($nick.$newPass)."' WHERE nickName = '".$nick."' ")) {
                        header("Location: editPassword.php?message=Senha alterada com sucesso!");
                        die();
                    }
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
            $result = $this->conFactory->query("SELECT * FROM clientes where nickName = '".$nick."'");  
            if (mysqli_num_rows($result) > 0) {
                return true;
            } else {
                return false;
            }
        }   
        
        function name($nick) {
            $result =  $this->conFactory->query("SELECT nomeCliente FROM clientes WHERE nickName ='".$nick."'");
            while($row = mysqli_fetch_assoc($result)) { 
                return $row["nomeCliente"];
            }
        }

        function contacts ($nick,$nickNameContact) {
            $result =  $this->conFactory->query("call contatos('".$nick."')");
            $count=0;
            $contacts = array();
            while($row = mysqli_fetch_assoc($result)) { 
                $contacts[$count++]=array($row["Contato"],$row["nickNameContato"]);
            }
            $html="";
            if (count($contacts) > 0) {
                $html.= "<div class='contacts' >";
                foreach ($contacts as $contact)  {
                    $html.= "<a href='messages.php?contactNickName=".$contact[1]."' >";
                    $html.= "<h2 ";
                  if (!empty($nickNameContact)){
                    if (!strcmp($nickNameContact,$contact[1])){
                        $html.= "style='color:white; background-color: #285d33;box-shadow: 0px 0px 10px 5px rgb(0 0 0 / 35%);'";
                    }
                  }
                  $html.= " ><div class='picContact' ><img src='Images/blank.png' style='background-image:url(".$this ->downloadProfilePic($contact[1]).");' /></div>&nbsp&nbsp".$contact[0]." &nbsp".$this->newMg($contact[1])."</h2></a>";
                }
                $html.= "</div>"; 
            }    
            return $html;        
        }

        function searchContact ($nick) {
            $result =  $this->conFactory->query("call searchContato('".$nick."')");
            $count=0;
            $contacts = array();
            while($row = mysqli_fetch_assoc($result)) { 
                $contacts[$count++]=array($row["Contato"],$row["nickNameContato"]);
            }
            if (count($contacts) > 0) {
                // output data of each row
                foreach ($contacts as $contact)  {
                  echo "<a href=\"messages.php?contactNickName=".$contact[1]."\" >";
                  echo "<h2 ";
                  if (!empty($_GET['contactNickName'])){
                    if (!strcmp($_GET['contactNickName'],$contact[1])){
                      echo "style=\"color:white; background-color: #285d33;box-shadow: 0px 0px 10px 5px rgb(0 0 0 / 35%);\"";
                    }
                  }
                  echo " ><img src=".$this ->downloadProfilePic($contact[1])." class=\"picContact\"/>&nbsp&nbsp".$contact[0]."</h2></a>";
                }
             }   
        }


        function downloadProfilePic ($contactNickName) {
            $result = $this->conFactory->query("SELECT * FROM profilepicture WHERE clienteId = '".$contactNickName."'");
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $pic = "data:image/jpeg;base64," . base64_encode($row["picture"]);
                }
            } else {
                $pic = "Images/profilePic.png";
            }
            return $pic;
        }        
        
        // MESSAGES 
        
        function messages ($nickName,$contactNickName) {
            $this->receivedMsg($contactNickName );
            $result = $this->conFactory->query("call messages('".$nickName."','".$contactNickName."')");
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
            if (count($messages) > 0) {
               $mensagens = "<center id='down' ><img  onclick='down();' style='position:fixed;bottom: 30%;' width='30px' height='30px' src='Images/down.png'/></center>";
               $mensagens.= "<br>";
                foreach ($messages as $msg) { 
                  if ($msg[4]) {
                    $color = "#285d33";
                    $margin = "right";
                    $float = "left";
                  } else {
                    $color = "#1d8634";
                    $margin = "left";
                    $float = "right";
                    $mensagens.= "<div class='delete' style='color:grey;margin-top:10px;margin-left:45%;margin-right:2%;float:".$float.";'> ●●●";  
                    $mensagens.= "<a href='delete.php?id=".$msg[3]."&contactNickName=".$contactNickName."' style='background-color:".$color."'><b>Deletar</b></a>";
                    $mensagens.= "</div>";
                  }        
                  $mensagens.= "<br>";
                  $mensagens.= "<div class='msg' style='background-color:".$color.";margin-".$margin.":50%;'>";
                  $mensagens.= $msg[0];                  
                  $mensagens.= "<p>".$msg[1]."<br><span style='float:right;'>".$msg[2]."</span></p>";    
                  $mensagens.= "</div>";    
                }
            } else {
               $mensagens= "<h3><center>Nenhuma mensagem de @".$contactNickName." até o momento<br>Faça seu primeiro envio!</center></h3>";
            }
            return $mensagens;
        }

        function newCurrentMsgs ($contactNickName){
            $result = $this->conFactory->query("SELECT COUNT(messages.Idmessage) as countMsg FROM messages WHERE messages.MsgFrom = '".$contactNickName."' AND messages.MsgTo = '".$_SESSION['nickName']."' AND messages.received = '0'");
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $count = $row["countMsg"];
                    if(preg_replace("[0]","",$count."") == 0){
                        $result = $this->conFactory->query("SELECT COUNT(messages.Idmessage) as countMsg FROM messages WHERE messages.MsgFrom = '".$contactNickName."' AND messages.MsgTo = '".$_SESSION['nickName']."' AND messages.received = '2'");
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $count = $row["countMsg"];
                                if(preg_replace("[0]","",$count."") == 0){
                                    return array("0","0");
                                } else {
                                    return array("2",$this->messages ($_SESSION['nickName'],$contactNickName));
                                }
                            }                   
                        }
                    } else {
                        return array("1",$this->messages ($_SESSION['nickName'],$contactNickName));
                    }
                }                   
            }
        }

        function newMg ($contactNickName) {
            $result = $this->conFactory->query("call newMsg('".$_SESSION['nickName']."','".$contactNickName."','0')");
            $count="0";
            while($row = mysqli_fetch_assoc($result)) {
                $count = $row["countMsg"];
                if(preg_replace("[0]","",$count."") == 0){
                    $result = $this->conFactory->query("call newMsg('".$_SESSION['nickName']."','".$contactNickName."','2')");
                    while($row = mysqli_fetch_assoc($result)) {
                        $count = $row["countMsg"];
                        if(preg_replace("[0]","",$count."") == 0){
                            $count = "";
                        } else {
                            $count = "<span id=".$contactNickName." class='newMsg'>&nbsp1</span>";
                        }
                    }
                } else {
                    $count = "<span id=".$contactNickName." class='newMsg'>&nbsp".$count."</span>";
                }
            }
            return $count;
        }

        function newContacts ($nickNameContact) {
            $result = $this->conFactory->query("call newMsgs('".$_SESSION['nickName']."')");
            $count="0";
            while($row = mysqli_fetch_assoc($result)) {
                $count = $row["countMsg"];
                if(preg_replace("[0]","",$count."") == 0){
                    return "0";
                } else {
                    $this->conFactory->query("DELETE FROM newMsg WHERE msgTo = '".$_SESSION['nickName']."'");
                    return $this->contacts($_SESSION['nickName'],$nickNameContact);
                }
            }
        }        

        function receivedMsg ($contactNickName) {
            $this->conFactory->query("UPDATE messages SET received = 1 WHERE messages.MsgFrom = '".$contactNickName."' and messages.MsgTo = '".$_SESSION['nickName']."'");
        }

        function createMessage ($msg,$contactNickName) { 
            if (!empty($_SESSION['nickName'])) {
                $this->conFactory->query("INSERT INTO messages (Messages, MsgFrom, MsgTo) VALUES ('".$msg."', '".$_SESSION['nickName']."', '".$contactNickName."')");
                $this->conFactory->query("INSERT INTO newMsg (msgFrom, msgTo) VALUES ('".$_SESSION['nickName']."','".$contactNickName."')");
                header("Location: messages.php?contactNickName=".$contactNickName);
                die(); 
            } else {
                header("Location: login.php");
                die(); 
            }
        }

        function deleteMessage ($id,$contactNickName) { 
            $this->conFactory->query("call deleteMessage(".$id.",'".$_SESSION['nickName']."')");
            $this->conFactory->query("INSERT INTO newMsg (msgFrom, msgTo) VALUES ('".$_SESSION['nickName']."','".$contactNickName."')");
            $this->conFactory->query("UPDATE messages SET received = '2' WHERE messages.MsgFrom = '".$_SESSION['nickName']."' and messages.MsgTo = '".$contactNickName."'");          
            header("Location: messages.php?contactNickName=".$contactNickName);
            die(); 
        }
  
    }
?>