<form action="login.php" method="post">
Login: <input type=text name=nick><br>
Senha: <input type=password name=pass><br>
<input type=submit value="OK">
</form>
<?php 
    require_once 'ConnectionFactory/ConnectionFactory.php';
    $nick = "";
    $pass = "";
    session_start();

    if (!empty($_POST["nick"]) && !empty($_POST["pass"])) {
        $nick = $_POST["nick"];
        $pass = $_POST["pass"];  
        // Create connection
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
            
        $sql = "SELECT * FROM clientes where nickName = '".$nick."' and senha = '".md5($nick.$pass)."'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            // output data of each row
            while($row = mysqli_fetch_assoc($result)) {    
                $_SESSION['nickName'] = $row["nickName"];    
                header("Location: index.php");
                die();   
            }
        } else {
            echo "<h2> Nickname ou senha incorreta </h2>";
        }  
        
        mysqli_close($conn);            
    }       
?>

