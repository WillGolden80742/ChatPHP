<?php 
    include 'Controller/UsersController.php';    
    $user = new UsersController();  
    $auth = new AutenticateModel();
?>
<DOCTYPE html>
<html>
<head>
<script src="assets/js/javascript.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        
        $(document).ready(function(){
          <?php
            if (!empty($nickNameContact)) {
              echo "down ();";
            } 
          ?>
          newContact();     
        });

        <?php 
            $lines_array = file("assets/js/javascript.js");
            foreach($lines_array as $line) {
                echo $line;
            }
        ?>

   </script> 
  <style id="styleIndex">


  </style>  

</head>    
<body class="container">

<?php

    echo "<div  class=\"header\"><h2>";
    echo "<a class='logout' href='logout.php' ><img src=\"Images/logout.svg\" /></a>";
    echo "<a class='back' href='index.php' ><img src=\"Images/left-arrow.svg\" /></a>";    
    if (!empty($nickNameContact)) {
      echo "<a class='picMessage' >";
      echo "<img src='Images/blank.png' style='background-image:url(".$user ->downloadProfilePic($nickNameContact).");' />";
      echo "<a class='userName'>";
      echo $user->name($nickNameContact);
      echo "</a>";
      echo "</a>";
    }
    echo "<span class='user' >&nbsp;";
    echo $user->name(new StringT($_SESSION['nickName']));
    echo "<a href=\"editProfile.php\"> ••• </a></span></h2>";
    echo "&nbsp&nbsp<form action=\"index.php\" method=\"post\"><input class=\"search\" placeholder='Pesquisar contatos ...' type=text name=search></form>";
    $userNickName = new StringT($_SESSION['nickName']);
    echo "</div>";
    echo "<div id=\"contacts\">";
    if (empty($_POST["search"])) {
      if (empty($nickNameContact)) {
        echo $user->contacts($userNickName,new StringT(null));
      } else {
        echo $user->contacts($userNickName,$nickNameContact);
      }
    } else {  
      echo "<div class='contacts'>";  
      $contacts = $user->searchContact(new StringT($_POST["search"]));
      echo "</div>"; 
    }
    echo "</div>";

?>   

</body>
</html>

