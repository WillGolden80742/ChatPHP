<?php
    include 'Model/UsersModel.php';

    class UsersController {
        private $auth;
        private $sessions;
        function __construct() {
            $this->conFactory = new ConnectionFactory();
            $this->auth = new AutenticateController();
            $this->user = new UsersModel();
            $this->sessions = new Sessions();
            $this->auth->isLogged();
            $this->nickSession = $_SESSION['nickName'];
        } 

        function uploadProfilePic (StringT $nick,$pic,$format) {
            $this->user->uploadProfilePic($nick,$pic,$format);
            $this->sessions->clearSession($nick);
        }
        
        function uploadProfile ($pass,StringT $newNick,$name) {      
            if ($this->auth->checkLogin(new StringT($this->nickSession),$pass)) {
                if (!$this->auth->checkNick (new StringT($newNick)) || strcmp($this->nickSession,$newNick) == 0) {
                    if($this->user->uploadProfile(new StringT($this->nickSession),$this->auth->encrypt($newNick.$pass),$newNick,$name)) {
                        $this->nickSession=$newNick;
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

        function uploadPassword ($pass,$newPass,$newPassConfirmation) {
            if ($this->auth->checkLogin(new StringT($this->nickSession),$pass)) {
                $passCertification = $this->auth->passCertification($newPass,$newPassConfirmation);
                if ($passCertification[0]) {
                    if($this->user->uploadPassword(new StringT($this->nickSession),$this->auth-> encrypt($this->nickSession.$newPass))) {
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
        
        function name (StringT $nick) {
            $result = $this->user->name($nick); 
            foreach($result as $value) {
                echo  $value;
            }
        }

        function contacts (StringT $nick,StringT $nickNameContact) {
            $result =  $this->user->contacts($nick);
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
                  $html.= " ><div class='picContact' ><img src='Images/blank.png' style='background-image:url(".$this ->downloadProfilePic(new StringT($contact[1])).");' /></div>&nbsp&nbsp".$contact[0]." &nbsp".$this->newMg(new StringT($contact[1]))."</h2></a>";
                }
                $html.= "</div>"; 
            }    
            return $html;        
        }

        function searchContact (StringT $nick) {
            $result =  $this->user->searchContact($nick);
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
                  echo " ><div class='picContact' ><img src='Images/blank.png' style='background-image:url(".$this ->downloadProfilePic(new StringT($contact[1])).");' /></div>&nbsp&nbsp".$contact[0]." &nbsp".$this->newMg(new StringT($contact[1]))."</h2></a>"; 
                }
             }   
        }

        function downloadProfilePic (StringT $contactNickName) {
            $result = $this->user->downloadProfilePic($contactNickName);
            if (!empty($result) > 0) {
                foreach($result as $value) {
                    $pic = "data:image/jpeg;base64," . base64_encode($value["picture"]);
                }
            } else {
                $pic = "Images/profilePic.png";
            }
            return $pic;
        } 
        
        // MESSAGES 
        function allMessages (StringT $contactNickName) {
            $query = $this->allMessagesQuery($contactNickName);
            return $this->messages ($query,$contactNickName,false);
        }     

        function allMessagesQuery (StringT $contactNickName) {
            return $this->user->messages(new StringT($this->nickSession),$contactNickName);
        }          

        function messages ($queryMessages,StringT $contactNickName,$async) {
            $this->receivedMsg($contactNickName );
            $messages = array();
            if (mysqli_num_rows($queryMessages) > 0) {
              $idMessage = '0';   
              $count=0;
              while($row = mysqli_fetch_assoc($queryMessages)) {
                if (strcmp($row["Idmessage"],$idMessage) !== 0) {
                    if (!empty($row["Messages"])) {
                        if (strcmp($row["MsgFrom"], $contactNickName) == 0 ) {
                            $from = "<span class='from'>From : @".$row["MsgFrom"]."</span>";
                            $left = true;
                        } else {
                            $left = false;
                            $from = "<span class='from'>You : </span>";
                        }                      
                        $message = new Message($row["Messages"],$async); 
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
                    $mensagens.= "<div class='delete' id=\"del$msg[3]\" style='color:grey;margin-left:45%;margin-right:2%;float:".$float.";'> ●●●";  
                    $mensagens.= "<a href='#' style='background-color:".$color."' onclick='deleteMessage(".$msg[3].");'><b>Apagar</b></a>";
                    $mensagens.= "</div>";
                  }        
                  $mensagens.= "<br id=\"br$msg[3]\" >";
                  $mensagens.= "<div class='msg msg-".$margin."' id=\"msg$msg[3]\" style='background-color:".$color.";'>";
                  $mensagens.= $msg[0];                  
                  $mensagens.= "<p>".$msg[1]."<br><span style='float:right;'>".$msg[2]."</span></p>";    
                  $mensagens.= "</div>";    
                }
            } else {
               $mensagens= "<h3><center>Nenhuma mensagem de @".$contactNickName." até o momento<br>Faça seu primeiro envio!</center></h3>";
            }
            return $mensagens;
        }

        function newCurrentMsgs (StringT $contactNickName){
            usleep(500000);
            $query = $this->allMessagesQuery($contactNickName);
            $result = $this->user->newCurrentMsgs($contactNickName,new StringT($this->nickSession));
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $count = $row["countMsg"];
                    if(strpos($count, "0") !== false){
                        $result = $this->user->isDeleteMessage($contactNickName,new StringT($this->nickSession));
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $count = $row["countMsg"];
                                if(strpos($count, "0") !== false){
                                    return array("0","0");
                                } else {
                                    return array("2",$this->messages($query,$contactNickName,true));
                                }
                            }                   
                        }
                    } else {
                        return array("1",$this->messages ($query,$contactNickName,true));
                    }
                }                   
            }
        }

        function newMg (StringT $contactNickName) {
            $result = $this->user->newMsg($contactNickName,new StringT($this->nickSession),0);
            $count="0";
            while($row = mysqli_fetch_assoc($result)) {
                $count = $row["countMsg"];
                if(strpos($count, "0") !== false){
                    $result =  $this->user->newMsg($contactNickName,new StringT($this->nickSession),2);
                    while($row = mysqli_fetch_assoc($result)) {
                        $count = $row["countMsg"];
                        if(strpos($count, "0") !== false){
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

        function newContacts (StringT $nickNameContact) {
            usleep(500000);
            $result = $this->user->newContacts(new StringT($this->nickSession));
            $count="0";
            while($row = mysqli_fetch_assoc($result)) {
                $count = $row["countMsg"];
                if (strpos($count, "0") !== false) {
                    return "0";
                } else {
                    $this->user->delMsg(new StringT($this->nickSession));
                    return $this->contacts(new StringT($this->nickSession),new StringT($nickNameContact));
                }
            }
        }        

        function receivedMsg (StringT $contactNickName) {
            $this->user->receivedMsg($contactNickName,new StringT($this->nickSession));
        }

        function lasIdMessage ($nick,$contactNickName) {
            $row = mysqli_fetch_assoc($this->user->lasIdMessage($nick,$contactNickName));
            return $row["LastID"];
        }

        function createMessage ($msg,StringT $contactNickName) { 
            if (strlen($msg) > 1 && strlen($msg) <= 500 && !empty($contactNickName)) {
                $this->user->createMessage($msg,$contactNickName,new StringT($this->nickSession));
                return $this->lasIdMessage(new StringT($this->nickSession),$contactNickName);
            } else {
                return "0";
            }
        }

        function deleteMessage (StringT $id,StringT $contactNickName) {
            return $this->user->deleteMessage($id,$contactNickName,new StringT($this->nickSession));          
        }
  
    }
?>