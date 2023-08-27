<?php
include 'Model/UsersModel.php';

class UsersController
{
    private $auth;
    private $sessions;
    private $user;
    private $nickSession;
    function __construct()
    {
        $this->auth = new AuthenticateController();
        $this->user = new UsersModel();
        $this->sessions = new Sessions();
        $this->auth->isLogged();
        $this->nickSession = $_SESSION['nickName'];
    }

    function uploadFile($file, $msg, $contactNickName)
    {
        return $this->user->uploadFile($file, $msg, $this->nickSession, $contactNickName);
    }

    function downloadFile($nomeHash)
    {
        return base64_encode($this->user->downloadFile($nomeHash));
    }

    function uploadProfilePic(StringT $nick, $pic, $format)
    {
        $this->user->uploadProfilePic($nick, $pic, $format);
        $this->sessions->clearSession($nick);
    }

    function uploadProfile($pass, StringT $newNick, $name)
    {
        $maxCharLimit = 20;

        if (mb_strlen($newNick) > $maxCharLimit || mb_strlen($name) > $maxCharLimit) {
            echo
            <<<HTML
            <div class='statusMsg center'>
                <h3 style="color:red;">O apelido e o nome devem ter no máximo {$maxCharLimit} caracteres.</h3>
            </div>
            HTML;
            return;
        }

        if ($this->auth->checkLogin(new StringT($this->nickSession), $pass)) {
            if (!$this->auth->checkNick(new StringT($newNick)) || strcmp($this->nickSession, $newNick) == 0) {
                if ($this->user->uploadProfile(new StringT($this->nickSession), $this->auth->encrypt($newNick . $pass), $newNick, $name)) {
                    $this->nickSession = $newNick;
                    $_SESSION['nickName'] = $newNick;
                    $this->auth->updateToken();
                    echo
                    <<<HTML
                    <div class='statusMsg center'>
                        <h3 style="color:white;">Alteração com sucesso!</h3>
                    </div>
                    HTML;
                } else {
                    echo
                    <<<HTML
                    <div class='statusMsg center'>
                        <h3 style="color:red;">Erro!</h3>
                    </div>
                    HTML;
                }
            } else if ($this->auth->checkNick($newNick)) {
                echo
                <<<HTML
                <div class='statusMsg center'>
                    <h3 style="color:red;">@$newNick já existente</h3>
                </div>
                HTML;
            }
        } else {
            echo
            <<<HTML
            <div class='statusMsg center'>
                <h3 style="color:red;">senha incorreta</h3>
            </div>
            HTML;
        }
    }


    function uploadPassword($pass, $newPass, $newPassConfirmation)
    {
        if ($this->auth->checkLogin(new StringT($this->nickSession), $pass)) {
            $passCertification = $this->auth->passCertification($newPass, $newPassConfirmation);

            if ($passCertification === "") {
                if ($this->user->uploadPassword(new StringT($this->nickSession), $this->auth->encrypt($this->nickSession . $newPass))) {
                    $this->auth->updateToken();
                    echo
                    <<<HTML
                    <div class='statusMsg'>
                        <h3 style="color:white;">Senha alterada com sucesso!</h3>
                    </div>
                    HTML;
                }
            } else {
                echo
                <<<HTML
                <div class='statusMsg center'>
                    <h3 style="color:red;">$passCertification</h3>
                </div>
                HTML;
            }
        } else {
            echo
            <<<HTML
            <div class='statusMsg center'>
                <h3 style="color:red;">senha incorreta</h3>
            </div>
            HTML;
        }
    }


    function name(StringT $nick)
    {
        $result = $this->user->name($nick);
        foreach ($result as $value) {
            echo  $value;
        }
    }

    function generateContactsHtml($contact, $nickNameContact, $sync)
    {
        $html = "<a id='contact$contact[1]'";

        if (basename($_SERVER['PHP_SELF']) == "messages.php" || $sync) {
            $html .= " onclick=\"updateMessages('$contact[1]','$contact[0]')\">";
        } else {
            $html .= " href=\"messages.php?contactNickName={$contact[1]}\">";
        }

        $html .= "<h2";

        if (!empty($nickNameContact) && !strcmp($nickNameContact, $contact[1])) {
            $html .= " style='color:white; background-color: #2b5278;box-shadow: 0px 0px 10px 5px rgb(0 0 0 / 35%)'";
        }

        $html .= "><div class='picContact' id='picContact$contact[1]'><img src='Images/blank.png' style='background-image:url(Images/profilePic.svg);' /></div>&nbsp&nbsp{$contact[0]} &nbsp</h2></a>";
        $html .= "<script>downloadAllPicContacts();</script>";
        return $html;
    }

    function contacts(StringT $nick, StringT $nickNameContact, $sync = false)
    {
        $result = $this->user->contacts($nick);
        $contacts = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $contacts[] = [$row["Contato"], $row["nickNameContato"]];
        }

        $html = <<<HTML
            <form action="index.php" method="post">
                <input class="search" placeholder='Pesquisar contatos ...' type="text" name="search">
            </form>
    HTML;

        foreach ($contacts as $contact) {
            $html .= $this->generateContactsHtml($contact, $nickNameContact, $sync);
        }

        return $html;
    }

    function searchContact(StringT $nick)
    {
        $result = $this->user->searchContact($nick);
        $contacts = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $contacts[] = [$row["Contato"], $row["nickNameContato"]];
        }

        $html = <<<HTML
            <form action="index.php" method="post">
                <input class="search" placeholder='Pesquisar contatos ...' type="text" name="search">
            </form>
            <a href="index.php"><h3>Limpar busca</h3></a>
    HTML;

        foreach ($contacts as $contact) {
            if (strcmp($contact[1], $this->nickSession) !== 0) {
                $html .= $this->generateContactsHtml($contact, "", false);
            }
        }

        $html .= "</div>";

        return $html;
    }


    function downloadProfilePic(StringT $contactNickName)
    {
        $result = $this->user->downloadProfilePic($contactNickName);
        $pic = "";
        if (!empty($result) > 0) {
            foreach ($result as $value) {
                $pic = base64_encode($value["picture"]);
            }
        } 
        return $pic;
    }

    // MESSAGES 
    function allMessages(StringT $contactNickName)
    {
        $query = $this->allMessagesQuery($contactNickName);
        return $this->messages($query, $contactNickName);
    }

    function allMessagesQuery(StringT $contactNickName)
    {
        return $this->user->messages(new StringT($this->nickSession), $contactNickName);
    }

    function lastMessage(StringT $contactNickName)
    {
        $query = $this->lastMessageQuery($contactNickName);
        return $this->messages($query, $contactNickName);
    }


    function lastMessageQuery(StringT $contactNickName)
    {
        return $this->user->lastMessage(new StringT($this->nickSession), $contactNickName);
    }

    function messageByID(StringT $contactNickName, StringT $id)
    {
        $query = $this->messageByIDQuery($contactNickName, $id);
        return $this->messages($query, $contactNickName);
    }

    function messageByIDQuery(StringT $contactNickName, StringT $id)
    {
        return $this->user->messageByID(new StringT($this->nickSession), $contactNickName, $id);
    }

    function messageByPag(StringT $contactNickName, StringT $pag)
    {
        $query = $this->messageByPagQuery($contactNickName, $pag);
        return $this->messages($query, $contactNickName);
    }

    function messageByPagQuery(StringT $contactNickName, StringT $pag)
    {
        return $this->user->messages(new StringT($this->nickSession), $contactNickName, $pag);
    }

    function messages($queryMessages, StringT $contactNickName)
    {
        $messages = array();

        if (mysqli_num_rows($queryMessages) > 0) {
            $idMessage = '0';
            $count = 0;

            while ($row = mysqli_fetch_assoc($queryMessages)) {
                if (strcmp($row["Idmessage"], $idMessage) !== 0) {
                    if (!empty($row["Messages"])) {
                        if (strcmp($row["MsgFrom"], $contactNickName) == 0) {
                            $left = true;
                        } else {
                            $left = false;
                        }

                        $message = new Message($row["Messages"]);
                        $hour = $row["HourMsg"];
                        $id = $row["Idmessage"];
                        $nome_anexo = $this->getMedia($row["nome_anexo"], $row["arquivo_anexo"]);

                        $messages[$count++] = array($message . $nome_anexo, $hour, $id, $left);
                    }
                }

                $idMessage = "" . $row["Idmessage"];
            }
        }

        if (count($messages) > 0) {
            $mensagens = "";

            foreach (array_reverse($messages) as $msg) {
                $margin = $msg[3] ? "right" : "left";
                $mensagens .=
                    <<<HTML
                    <div class='msg msg-$margin' id="msg$msg[2]" >
                    HTML;
                if (!$msg[3]) {
                    $mensagens .=
                        <<<HTML
                    <a class="delete" onclick='deleteMessage($msg[2]);'><b>Apagar</b><br></a>
                    HTML;
                }
                $mensagens .=
                    <<<HTML
                    <p>$msg[0]<br><span style='float:right;'>$msg[1]</span></p>
                    </div>
                    HTML;
            }
            $mensagens .= "<script>main();</script>";
        } else {
            $mensagens = "";
        }
        return $mensagens;
    }


    function getMedia($nome, $hash)
    {
        $extensao = pathinfo($nome, PATHINFO_EXTENSION);

        if (empty($nome)) {
            return '';
        }

        if ($this->isVideo($extensao)) {
            return
                <<<HTML
                <div class="attachment_file" onclick="showPlayer('$hash',event);">
                    <a><img class="videoIcon" src="Images/blank.png" style="float:left" />$nome</a>
                </div>
                HTML;
        } elseif ($this->isAudio($extensao)) {
            return
                <<<HTML
                <div class="media_file">
                    <div class="center"><p class="name">$nome</p></div>
                    <div class="player">
                        <div class="controls">
                            <div class="play-button" onclick="togglePlay('$hash',event);">
                                <audio class="audioPlayer" id="$hash" style="display:none;width:-webkit-fill-available;" controls>Seu navegador não suporta a reprodução deste áudio.</audio>
                            </div>
                            <div class="time">
                                <span class="current-time">0:00</span>
                                <span class="duration">0:00</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress"></div>
                            </div>
                            <div class="download-button" onclick="downloadFile('$hash','$nome')"></div>
                        </div>
                    </div>
                </div>
                HTML;
        } elseif ($this->isImage($extensao)) {
            return
                <<<HTML
                <div class="image_file">
                    <div class="center">
                        <img id="$hash" onclick="embedImage('$hash',event)" src="Images/download.gif" height="250px">
                    </div>
                </div>
                HTML;
        } else {
            return
                <<<HTML
                <div class="attachment_file">
                    <a onclick="downloadFile('$hash','$nome')"><img class="fileIcon" src="Images/blank.png"/>$nome</a>
                </div>
                HTML;
        }
    }



    function isVideo($extensao)
    {
        $videoExtensions = array('mp4'); // Adicione aqui as extensões de vídeo suportadas

        return in_array($extensao, $videoExtensions);
    }

    function isAudio($extensao)
    {
        $audioExtensions = array('mp3', 'wav', 'm4a', 'ogg'); // Adicione aqui as extensões de áudio suportadas

        return in_array($extensao, $audioExtensions);
    }

    function isImage($extensao)
    {
        $imageExtensions = array('jpg', 'jpeg', 'png', 'webp', 'gif'); // Adicione aqui as extensões de imagem suportadas
        return in_array($extensao, $imageExtensions);
    }

    function lasIdMessage($nick, $contactNickName)
    {
        $row = mysqli_fetch_assoc($this->user->lasIdMessage($nick, $contactNickName));
        return $row["LastID"];
    }

    function createMessage($msg, StringT $contactNickName)
    {
        if (strlen($msg) > 1 && strlen($msg) <= 500 && !empty($contactNickName)) {
            $this->user->createMessage($msg, $contactNickName, new StringT($this->nickSession));
            return $this->lasIdMessage(new StringT($this->nickSession), $contactNickName);
        } else {
            return "0";
        }
    }

    function deleteMessage(StringT $id, StringT $contactNickName)
    {
        return $this->user->deleteMessage($id, $contactNickName, new StringT($this->nickSession));
    }
}
