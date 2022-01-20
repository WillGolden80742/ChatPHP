
  <?php 
    require_once 'ConnectionFactory/ConnectionFactory.php';
    require_once 'index.php';
    
    $userNickName = $_SESSION['nickName'];
      // Create connection
      $conn = mysqli_connect($servername, $username, $password, $dbname);
      // Check connection
      if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
      }
  
      $URL_ATUAL= "$_SERVER[REQUEST_URI]";
      $components = parse_url($URL_ATUAL);
      parse_str($components['query'], $results);
      $contactNickName = $results['contactNickName'];
  ?>

<head>   
  <title><?php echo $contactNickName; ?></title>
</head>    
<div class="messages">
<?php

    $sql = "call messagesWithAttachment('".$userNickName."','".$contactNickName."')";
    $result = mysqli_query($conn, $sql);

    // echo md5($contactNickName);
    
    if (mysqli_num_rows($result) > 0) {
      // output data of each row
      while($row = mysqli_fetch_assoc($result)) {
        if ( strcmp($row["MsgFrom"], $contactNickName) == 0 ) {
          echo "<h2>From : ".$row["MsgFrom"]."<br>";
        } else {
          echo "<h2>You : <br>";
        }
        echo $row["messages"]." ".$row["HourMsg"]."</h2>";  
      }
    } else {
      echo "<h2> 0 results </h2>";
    }

    mysqli_close($conn);

?>
</div>
<textarea class="text" name="txtname">

</textarea>