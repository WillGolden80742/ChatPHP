<link rel="icon" type="image/ico" href="Images/favicon.ico">
<?php
include 'Controller/UsersController.php';
$user = new UsersController();
$auth = new AuthenticateModel();
?>

<link rel="stylesheet" href="assets/css/styles.css">
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/md5.min.js"></script>
<script src="assets/js/javascript.js"></script>
<script>
    <?php
    $nickNameContact = "";
    if (!empty($_GET['contactNickName'])) {
        $nickNameContact = new StringT($_GET['contactNickName']);
        $sessions = new Sessions();
        $sessions->clearSession($nickNameContact);
    }
    ?>

    const nickNameContact = "<?php echo $nickNameContact; ?>";
    const cacheMap = new Map();
    const currentUrl = window.location.href;
    const home = currentUrl.split("<?php echo $_SERVER['HTTP_HOST']; ?>/")[1];

    if (home.includes('index.php') || home == '') {
        document.title = "CHATPHP";
    } else if (home.includes('editProfile.php')) {
        document.title = "Edite Perfil";
    }

    document.addEventListener("DOMContentLoaded", function () {
        if (nickNameContact !== "") {
            down();
        }
    });

    function getServer() {
        if (hasCache('server')) {
            return getCache('server');
        } else {
            return "<?php echo $_SERVER['HTTP_HOST']; ?>";
        }
    }

    let server = getServer();
    let ws = new WebSocket(`ws://${server}:8080`);

    ws.onopen = () => {
        console.log('Conexão estabelecida.');
        sendSocket("online");
    };

    ws.onmessage = (event) => {
        const data = JSON.parse(event.data);
        const message = data.message;
        hasNewMsg(data);
        console.log(event.data);

        if (data.offer) {
            handleOffer(data.offer);
        } else if (data.answer) {
            handleAnswer(data.answer);
        } else if (data.candidate) {
            handleCandidate(data.candidate);
        }
    };

    ws.onclose = () => {
        console.log('Conexão fechada.');
        const reload = confirm('A conexão com o servidor falhou. Deseja recarregar a página para tentar novamente?');
        if (reload) {
            location.reload();
        }
    };

    function setServer() {
        const newServer = prompt('Digite o endereço do novo servidor:');
        if (newServer !== null) {
            const formattedServer = newServer.replace(/^(https?:\/\/)|(\/)$/g, '');
            setCache('server', formattedServer);
            location.reload();
        }
    }

    function sendSocket(value) {
        const nickNameFrom = '<?php echo new StringT($_SESSION["nickName"]); ?>';
        const nickNameTo = '<?php echo $nickNameContact; ?>';

        try {
            if (value.trim() !== '' && nickNameFrom.trim() !== '') {
                ws.send(JSON.stringify({
                    nickNameFrom: nickNameFrom,
                    nickNameTo: nickNameTo,
                    message: value
                }));
            }
        } catch (error) {
            console.error('Erro ao enviar mensagem via WebSocket:', error);
        }
    }

    // WebRTC setup
    const configuration = { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] };
    let peerConnection = new RTCPeerConnection(configuration);

    peerConnection.onicecandidate = (event) => {
        if (event.candidate) {
            ws.send(JSON.stringify({ candidate: event.candidate }));
        }
    };

    peerConnection.ontrack = (event) => {
        const remoteStream = event.streams[0];
        // Attach the stream to a video element here
    };

    function startCall() {
        navigator.mediaDevices.getUserMedia({ video: true, audio: true })
            .then((stream) => {
                stream.getTracks().forEach((track) => peerConnection.addTrack(track, stream));
            })
            .then(() => peerConnection.createOffer())
            .then((offer) => peerConnection.setLocalDescription(offer))
            .then(() => {
                ws.send(JSON.stringify({ offer: peerConnection.localDescription }));
            })
            .catch((error) => {
                console.error('Erro ao iniciar chamada:', error);
            });
    }

    function handleOffer(offer) {
        peerConnection.setRemoteDescription(new RTCSessionDescription(offer))
            .then(() => navigator.mediaDevices.getUserMedia({ video: true, audio: true }))
            .then((stream) => {
                stream.getTracks().forEach((track) => peerConnection.addTrack(track, stream));
            })
            .then(() => peerConnection.createAnswer())
            .then((answer) => peerConnection.setLocalDescription(answer))
            .then(() => {
                ws.send(JSON.stringify({ answer: peerConnection.localDescription }));
            });
    }

    function handleAnswer(answer) {
        peerConnection.setRemoteDescription(new RTCSessionDescription(answer));
    }

    function handleCandidate(candidate) {
        peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
    }

</script>


<style id="styleIndex">


</style>

<?php

echo "<div  class=\"header\"><h2>";
echo "<a class='logout' href='logout.php' ><img src=\"Images/logout.svg\" /></a>";
echo "<a class='back' onclick='toggle();' ><img src=\"Images/left-arrow.svg\" /></a>";
if (!empty($nickNameContact)) {
  echo "<a class='picMessage' >";
  $pic = "";
  $data = $user->downloadProfilePic(new StringT($nickNameContact));
  if (!empty($data) > 0) {
    $pic = "data:image/jpg;base64," . $data;
  } else {
    $pic = "Images/profilePic.svg";
  }
  echo "<img src='$pic' />";
  echo "<a class='userName' id='userName'>";
  echo $user->name($nickNameContact);
  echo "</a>";
  echo "</a>";
}
echo "<span class='user' >&nbsp;";
echo $user->name(new StringT($_SESSION['nickName']));
echo "<a href=\"editProfile.php\" class='menuProfile' > <img src=\"Images/editProfileIcon.svg\" class=\"editProfileIcon\" > </a></span></h2>";
$userNickName = new StringT($_SESSION['nickName']);
echo "</div>";
echo "<div class='contacts' id='contacts'>";
if (empty($_POST["search"])) {
  if (empty($nickNameContact)) {
    echo $user->contacts($userNickName, new StringT(null));
  } else {
    echo $user->contacts($userNickName, $nickNameContact);
  }
} else {
  echo $user->searchContact(new StringT($_POST["search"]));
}
echo "</div>";

?>

<div class="home">
  <div></div>
</div>