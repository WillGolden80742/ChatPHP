<DOCTYPE html>
<html>
<head>  
<style>
  a {
    text-decoration:none;
    color:cornflowerblue;
  }
  * {

    font-family: Arial, Helvetica, sans-serif;
  }
  .messages {
    display: inline-block;
    padding: 0px 10px 10px 10px;
    margin: 5px;
    position:absolute;
    top:0px;
    left:245px;
    width:500px;
    height:400px;
    border: 3px solid #3e3661;
    border-radius: 10px;
    overflow-y: scroll;
  }
  .contacts {
    padding: 0px 10px 10px 10px;
    margin: 5px;
    position:absolute;
    top:40px;
    left:0px;
    width:220px;
    height:440px;
    border: 3px solid #3e3661;
    border-radius: 10px;
    overflow-y: scroll;
  }  
  .text {
    display: inline-block;
    padding: 0px 10px 10px 10px;
    border: 3px solid #3e3661;
    background: none;
    margin: 5px;
    position:absolute;
    top:420px;
    left:245px;
    width:525px;
    height:75px;
  }

  ::-webkit-scrollbar {

    width:7px;
    border-radius:10px;

  }

  ::-webkit-scrollbar-thumb:hover {

    background:#3e3661;

  }   

  ::-webkit-scrollbar-thumb {

    background:white;
    border-radius:10px;
    border:solid 1px rgba(0,0,0,0.25);


  }

  ::-webkit-scrollbar-track {

    border-radius:10px;
    box-shadow:inset 0px 0px 5px rgba(0,0,0,0.25);

  }
</style> 
</head>    
<body class="container">

<?php
    include 'Model/DAO/UsersManager.php';    
    $user = new UsersManager();
    echo "<h2>";
    $userNickName = "";
    if (empty($_SESSION['nickName'])) { 
      echo "<a href='login.php'>Login</a></h2>";
    } else {
      echo $_SESSION['nickName'];
      echo " <a href='logout.php'>Logout</a></h2>";
      $userNickName = $_SESSION['nickName'];
    }

    $contacts = $user->contacts($userNickName);

    if (count($contacts) > 0) {
      // output data of each row
      echo "<div class='contacts'>"; 
      foreach ($contacts as $contact)  {
        echo "<a href=\"messages.php?contactNickName=".$contact."\"><h2>".$contact."</h2></a>";
      }
      echo "</div>";
    } else {
      echo "<h2> 0 results </h2>";
    }

  
?>

</body>
</html>