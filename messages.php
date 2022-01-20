
  <?php 
    require_once 'index.php';
    $conFactory = new ConnectionFactory();
    $userNickName = $_SESSION['nickName'];
      // Create connection
      $conn = $conFactory->connect();
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

    $message = new UsersManager();  
    $messages = $message->messages($contactNickName);

    if (count($messages) > 0) {
      $msgs = "";
      echo '<br>';
      foreach ($messages as $msg) {         
          if (empty($msg[1])) {
            $msgs = "<a href=\"#\">".$msg[1]."</a>".$msgs;   
          }            
          $msgs = "<h3>".$msg[2]." ".$msg[2]."<br>".$msg[3]."</h3>".$msgs; 
          $msgs = $msg[0]."<br>".$msgs;     
      }
      echo $msgs;
    } else {
      echo "<h3> 0 results </h3>";
    }


?>
</div>
<textarea class="text" name="txtname">

</textarea>

