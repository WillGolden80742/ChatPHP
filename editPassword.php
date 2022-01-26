<?php include 'index.php' ?>
<html>
<head>  
</head>    
<script>
</script>    
<style id="stylePic">
    .profilePic {
        background:none;
        border:solid 3px #285d33;
        border-radius:100%;
        width:150px;
        height:150px;
        background-image: url("Images/profileMedium.png");
        background-size: 100vw auto;
        background-position-x:50%;
        background-size: cover;   
    }
    .salvar {
        display:none;
    }
</style>    
<body class="container">
<div class="editProfile">
<center> 
<?php 
    $conFactory = new ConnectionFactory();
    $user = new UsersManager();
    $pic=null;
    if (!empty($_FILES["pic"])) {
        $pic=$_FILES["pic"];
    } 
    if($pic != NULL) {
        $name = time().'.jpg';
        if (move_uploaded_file($pic['tmp_name'], $name)) {
            $size = filesize($name);     
            $maxSize = 1000000;    
            if ($size < $maxSize) {   
                $mysqlImg = addslashes(fread(fopen($name, "r"), $size));
                $user->uploadProfilePic($_SESSION['nickName'],$mysqlImg,'jpg');
            } else {
                echo "<p>Tamanho máximo de ".$maxSize." bytes</p>";
            }
        } 
        echo "<div ><img src='Images/blank.png' class='profilePic' style='background-image:url(".$user ->downloadProfilePic($_SESSION['nickName']).");' onclick='openfile();' /></div>";
    } else {
        echo "<div ><img src='Images/blank.png' class='profilePic' style='background-image:url(".$user ->downloadProfilePic($_SESSION['nickName']).");' onclick='openfile();' /></div>";
    }
?>
<form action="editPassword.php" method="post" enctype="multipart/form-data">
    <input id="editProfilePic" accept=".jpg,.png,.jpge" onchange="display();" style="display:none;" id="editProfile" type="file" name="pic"> 
    <input class="inputSubmit salvar" type=submit value="SALVAR">
</form>
<form action="uploadPassword.php" method="post" >
    <input class="inputPassword" placeholder="Current Password"  type=password name=currentPass><br><br>
    <input class="inputPassword" placeholder="New Password"  type=password name=pass><br><br>
    <input class="inputPassword" placeholder="Password Confirmation"  type=password name=passConfirmation><br><br>    
    <input class="inputSubmit" type=submit value="ATUALIZAR"> 
</form>
<a href="editProfile.php"><img src="Images/nameIcon-dark.png"></a>
<?php
    if (!empty($_GET['error'])) {
        echo "<center><h3 style=\"color:red;\">".$_GET['error']."</h3></center>";
    }
    if (!empty($_GET['message'])) {
        echo "<center><h3 style=\"color:green;\">".$_GET['message']."</h3></center>";
    }
?>
</center>   
</div>
</body>
</html>
