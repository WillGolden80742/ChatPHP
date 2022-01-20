
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
        if ($msg[3]) {
          
          $float = 'right';
        } else {
          $float = 'left';
        } 
        echo "<div class=\"msg\" style=\"margin-".$float.":50%;\">";
        echo $msg[0]."<br>";                  
        echo "<p>".$msg[1]."<br><span style=\"float:right;\">".$msg[2]."</span>";    
        echo "</div>";    
      }
    } else {
      echo "<h3> 0 results </h3>";
    }


?>
</div>

<?php 
  echo "<form action=\"new.php\" method=\"post\"> <textarea id=\"text\" class=\"text\" onkeyup=\"typing()\" name=messageText> </textarea> <input type=\"hidden\" name=\"contactNickName\" value=".$contactNickName."> <input class=\"send\" type=submit value=\">\" > </form>"
?>

