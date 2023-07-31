<?php 
    require_once 'index.php'; 
    $userNickName = "";
    $contactNickName = new StringT("");
    $userNickName = new StringT($_SESSION['nickName']);
    if (!empty($_GET['contactNickName'])) {
      $contactNickName = new StringT($_GET['contactNickName']);
    }
?>
  <style id="styleMsg">

  
      <?php 
          $lines_array = file("assets/css/styleNoIndex.css");
          foreach($lines_array as $line) {
              echo $line;
          }
      ?>

  </style> 

  <title><?php echo $contactNickName; ?></title>

<div class="messages" id="messages" onscroll="removeDownButton();">
  
    <?php
      echo $user->allMessages($contactNickName);
    ?>
    
</div>

<?php 
 
  echo "<textarea id=\"text\" class=\"text\" oninput=\"messageValidate();\" name=\"messageText\"></textarea> <div class=\"send_msg_box\"><input type=hidden name='contactNickName' value=\"$contactNickName\">  <input type=hidden name='userNickName' value=\"$userNickName\">  <input class=\"send\" id=\"send\" type=submit onclick=\"createMessage();\" value=\"\" > <br><div class=\"attachment\" id=\"attachment\" onclick='openfile(\"file\");'></div> <input id=\"file\" style=\"display:none;\" onchange=\"messageValidate();\" type=\"file\" name=\"arquivo\" required></div>";

?>

  <script>
    const textElement = document.querySelector(".text");
    textElement.addEventListener("click", emojiClicked);

    const detectInputAudioPermissions = async () => {
      const constraints = { audio: true };
      try {
        const stream = await navigator.mediaDevices?.getUserMedia?.(constraints);
        if (stream) {
          for (const track of stream.getTracks()) {
            track.stop();
          }
          return true;
        }
        return false;
      } catch (error) {
        return false;
      }
    };

    async function startRecording() {
        (async () => {
          if (await detectInputAudioPermissions()) {
            console.log('Audio input device has permissions.');
          } else {
            console.error('Audio input device is not available or does not have permissions.');
          }
        })();
        const sendButtom = document.querySelector(".send");
        sendButtom.style.backgroundImage = "url(\"Images/micSelectedIcon.svg\")";
        const text = document.querySelector(".text");
        text.disabled = true;
        await detectInputAudioPermissions();
        // Verificar se o navegador suporta a API de áudio do HTML5
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            // Opções de configuração para a captura de áudio
            const options = { audio: true };

            // Iniciar a captura de áudio
            navigator.mediaDevices.getUserMedia(options)
                .then(function(stream) {
                    const mediaRecorder = new MediaRecorder(stream);
                    const chunks = [];

                    // Evento disparado quando houver dados disponíveis para gravação
                    mediaRecorder.ondataavailable = function(event) {
                        chunks.push(event.data);
                    };

                    // Evento disparado quando a gravação é concluída
                    mediaRecorder.onstop = function() {
                        // Criar um objeto Blob a partir dos dados gravados
                        const blob = new Blob(chunks, { 'type' : 'audio/wav' });

                        // Chamar a função para abrir uma nova guia com o áudio gravado
                        loadFile(blob);
                    };

                    // Iniciar a gravação por 5 segundos
                    mediaRecorder.start();
                    sendButtom.onclick = () => {
                      mediaRecorder.stop();
                      sendButtom.style.backgroundImage = "url(\"Images/micIcon.svg\")";
                      text.disabled = false;
                      sendButtom.onclick = () => {
                        createMessage();
                      }
                    }
                })
                .catch(function(error) {
                    console.error("Erro ao acessar o microfone: ", error);
                });
        } else {
            console.error("A API de áudio do HTML5 não é suportada neste navegador.");
            sendButtom.style.backgroundImage = "url(\"Images/micDisabled.svg\")";
            text.disabled = false;
        }
    }

    function loadFile(blob) {
        loading(true);
        var formData = new FormData();
        formData.append('arquivo',new File([blob], "audio."+stringToMD5(Math.random()+"")+".wav", { type: 'audio/wav' }));
        formData.append('messageText','');
        formData.append('contactNickName',nickNameContact);
        uploadAttachment('uploadFile.php', formData);   
        waitingMsg();
    }
    
  </script> 
  
<div>
