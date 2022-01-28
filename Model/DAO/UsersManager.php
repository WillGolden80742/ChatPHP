<?php
    include 'Autenticate.php';
    class usersManager {
        private $conFactory;
        private $auth;
        function __construct() {
            $this->conFactory = new ConnectionFactory();
            $this->auth = new AuthManager();
            $this->auth->isLogged();
        } 
        
        function uploadProfilePic ($nick,$pic,$format) {
            $this->conFactory->query("DELETE FROM profilepicture WHERE clienteId = '".$nick."'");
            $this->conFactory->query("INSERT INTO profilepicture (clienteId,picture,format) VALUES ('".$nick."','".$pic."','".$format."')");
        }

        function uploadProfile ($nick,$pass,$newNick,$name) {
            $nick=$this->auth->clearString($nick);
            $newNick=$this->auth->clearString($newNick);           
            if ($this->auth->checkLogin($nick,$pass)) {
                if (!$this->auth->checkNick ($newNick) || strcmp($nick,$newNick) == 0) {
                    if($this->conFactory->query("UPDATE clientes SET nickName = '".$newNick."', nomeCliente = '".$name."', senha = '".$this->auth->encrypt($newNick.$pass)."' WHERE nickName = '".$nick."' ")) {
                        $_SESSION['nickName']=$newNick;
                        header("Location: editProfile.php?message=Alteração com sucesso!");
                        die();
                    } else {
                        header("Location: editProfile.php");
                        die(); 
                    }
                } else if ($this->auth->checkNick ($newNick)) {
                    header("Location: editProfile.php?error=@".$newNick." já existente");
                    die();
                }
            } else {
                header("Location: editProfile.php?error=senha incorreta");
                die();
            }         
        }

        function uploadPassword ($nick,$pass,$newPass,$newPassConfirmation) {
            if ($this->auth->checkLogin($nick,$pass)) {
                $passCertification = $this->auth->passCertification($newPass,$newPassConfirmation);
                if ($passCertification[0]) {
                    if($this->conFactory->query("UPDATE clientes SET senha = '".$this->auth-> encrypt($nick.$newPass)."' WHERE nickName = '".$nick."' ")) {
                        header("Location: editPassword.php?message=Senha alterada com sucesso!");
                        die();
                    }
                } else {
                    header("Location: editPassword.php?error=".$passCertification[1]);
                    die();
                }
            } else {
                header("Location: editPassword.php?error=senha incorreta");
                die();
            }        
        }        
        
        function name($nick) {
            $nick=$this->auth->clearString($nick);
            $result =  $this->conFactory->query("SELECT nomeCliente FROM clientes WHERE nickName ='".$nick."'");
            while($row = mysqli_fetch_assoc($result)) { 
                return $row["nomeCliente"];
            }
        }

        function contacts ($nick,$nickNameContact) {
            $nick = $this->auth->clearString($nick);
            $nickNameContact = $this->auth->clearString($nickNameContact);
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
            $nick = $this->auth->clearString($nick);
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
                  echo " ><div class='picContact' ><img src='Images/blank.png' style='background-image:url(".$this ->downloadProfilePic($contact[1]).");' /></div>&nbsp&nbsp".$contact[0]." &nbsp".$this->newMg($contact[1])."</h2></a>"; 
                }
             }   
        }

        function downloadProfilePic ($contactNickName) {
            $contactNickName = $this->auth->clearString($contactNickName);
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
            $nickName = $this->auth->clearString($nickName);
            $contactNickName = $this->auth->clearString($contactNickName);
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
                            $from = "<span class='from'>From : @".$row["MsgFrom"]."</span>";
                            $left = true;
                        } else {
                            $left = false;
                            $from = "<span class='from'>You : </span>";
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
               $mensagens = "<center id='down' ><img  onclick='down();' style='position:fixed;bottom: 30%; background:white; border-radius: 100%;' width='50px' src='Images/down.png'/></center>";
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
                    $mensagens.= "<a href='#' style='background-color:".$color."' onclick='deleteMessage(".$msg[3].");'><b>Apagar</b></a>";
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
            $contactNickName = $this->auth->clearString($contactNickName);
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
            $contactNickName = $this->auth->clearString($contactNickName);
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
            $nickNameContact = $this->auth->clearString($nickNameContact);
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
            $contactNickName = $this->auth->clearString($contactNickName);
            $this->conFactory->query("UPDATE messages SET received = 1 WHERE messages.MsgFrom = '".$contactNickName."' and messages.MsgTo = '".$_SESSION['nickName']."'");
        }

        function createMessage ($msg,$contactNickName) { 
            $msg = preg_replace('[\']','',$msg);
            $msg = preg_replace('[\--]','',$msg);
            $contactNickName = $this->auth->clearString($contactNickName);
            if (strlen($msg) > 1 && strlen($msg) <= 500 && !empty($contactNickName)) {
                $this->conFactory->query("INSERT INTO messages (Messages, MsgFrom, MsgTo) VALUES ('".$msg."', '".$_SESSION['nickName']."', '".$contactNickName."')");
                $this->conFactory->query("INSERT INTO newMsg (msgFrom, msgTo) VALUES ('".$_SESSION['nickName']."','".$contactNickName."')");
                return $this->messages($_SESSION['nickName'],$contactNickName);
            } else {
                return "0";
            }
        }

        function deleteMessage ($id,$contactNickName) { 
            $id = $this->auth->clearString($id);
            $contactNickName = $this->auth->clearString($contactNickName);
            $this->conFactory->query("call deleteMessage(".$id.",'".$_SESSION['nickName']."')");
            $this->conFactory->query("INSERT INTO newMsg (msgFrom, msgTo) VALUES ('".$_SESSION['nickName']."','".$contactNickName."')");
            $this->conFactory->query("UPDATE messages SET received = '2' WHERE messages.MsgFrom = '".$_SESSION['nickName']."' and messages.MsgTo = '".$contactNickName."'");          
            return $this->messages($_SESSION['nickName'],$contactNickName);
        }
  
    }
?>