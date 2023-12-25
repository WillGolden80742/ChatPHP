<link rel="icon" type="image/ico" href="Images/favicon.ico">
<?php
include 'Controller/UsersController.php';
$user = new UsersController();
$auth = new AuthenticateModel();
?>

<link rel="stylesheet" href="assets/css/styles.css">
<script>
  <?php
  $nickNameContact = "";
  if (!empty($_GET['contactNickName'])) {
    $nickNameContact = new StringT($_GET['contactNickName']);
    $sessions = new Sessions();
    $sessions->clearSession($nickNameContact);
  }
  ?>
  var nickNameContact = "<?php echo $nickNameContact; ?>";
  const cacheMap = new Map();
  let customWebSocketServer = "<?php echo $_SERVER['HTTP_HOST']; ?>";
  const currentUrl = customWebSocketServer;
  const home = currentUrl.split(customWebSocketServer)[1];
  if (home.includes('index.php') || home == '') {
    document.title = "CHATPHP";
  } else if (home.includes('editProfile.php')) {
    document.title = "Edit Profile";
  }
  document.addEventListener("DOMContentLoaded", function() {
    var nickNameContact = <?php echo json_encode($nickNameContact); ?>;

    if (nickNameContact !== "") {
      down();
    }
  });

  // Prompt the user to choose WebSocket server address
  const changeServer = confirm("Do you want to change the WebSocket server address?");

  if (changeServer) {
    customWebSocketServer = prompt("Enter the new WebSocket Server Address:");
  }

  const ws = new WebSocket("ws://"+customWebSocketServer+":8080");

  ws.onopen = () => {
    console.log('Connection established.');
    sendSocket("online");
  };

  ws.onmessage = (event) => {
    const data = JSON.parse(event.data);
    const message = data.message;
    hasNewMsg(data);
    console.log(event.data);
  };

  ws.onclose = () => {
    console.log('Connection closed.');
    const reload = confirm('The connection to the server failed. Do you want to reload the page to try again?');
    if (reload) {
      location.reload();
    }
  };

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
      console.error('Error sending message via WebSocket:', error);
      // You can add additional handling here if needed.
    }
  }
</script>



<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/md5.min.js"></script>
<script src="assets/js/javascript.js"></script>

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