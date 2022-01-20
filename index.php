<DOCTYPE html>
<html>
<head>  
<style>
  .messages {
    display: inline-block;
    padding: 0px 10px 10px 10px;
    margin: 5px;
    position:absolute;
    top:0px;
    right:500px;
    width:500px;
    height:400px;
    border: 3px solid #3e3661;
    border-radius: 10px;
    overflow-y: scroll;
  }
  a {
    text-decoration:none;
    color:cornflowerblue;
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
</style> 
</head>    
<body class="container">

<?php
    require_once 'ConnectionFactory/ConnectionFactory.php';
    session_start();
    echo "<h2>".$_SESSION['nickName'];
    if (empty($_SESSION['nickName'])) { 
      echo "<a href='login.php'>Login</a></h2>";
    } else {
      echo " <a href='logout.php'>Logout</a></h2>";
    }
    $userNickName = $_SESSION['nickName'];

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "call contatos('".$userNickName."')";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
      // output data of each row
      while($row = mysqli_fetch_assoc($result)) { 
        echo "<a href=\"messages.php?contactNickName=".$row["nickNameContato"]."\"><h2>".$row["nickNameContato"]."</h2></a>";
      }
    } else {
      echo "<h2> 0 results </h2>";
    }

    mysqli_close($conn);
  
?>

</body>
</html>