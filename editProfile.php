<?php 
    include 'index.php';
    include 'Controller/FileController.php';
?>
<html>
<head>  
    <style id="stylePic">
        <?php 
            $lines_array = file("assets/css/styleNoIndex.css");
            foreach($lines_array as $line) {
                echo $line;
            }
        ?>
        .header {
            backdrop-filter:none;
            border: none;
            box-shadow: none;
        }
    </style>    
</head>     
<body class="container">
<div class="editProfile">
<center> 
<?php 
    $pic=null;
    if (!empty($_FILES["pic"])) {
        $fileController = new FileController($_FILES["pic"]);
        $file = $fileController->getFile();
        if ($file) {
            $user->uploadProfilePic(new StringT($_SESSION['nickName']),$file,'jpg');
        } else {
            echo $fileController->getError();
        }
        echo "<div ><img src='Images/edit.png' class='profilePic' style='background-image:url(".$user ->downloadProfilePic(new StringT($_SESSION['nickName'])).");' onclick='openfile(\"editProfilePic\");' /></div>";
    } else {
        echo "<div ><img src='Images/edit.png' class='profilePic' style='background-image:url(".$user ->downloadProfilePic(new StringT($_SESSION['nickName'])).");' onclick='openfile(\"editProfilePic\");' /></div>";
    }
?>
<form action="editProfile.php" method="post" enctype="multipart/form-data">
    <input id="editProfilePic" accept=".jpeg,.jpg,.png" onchange="display();" style="display:none;" id="editProfile" type="file" name="pic"> 
    <input class="inputSubmit salvar" type=submit value="SALVAR">
</form>
<form action="uploadProfile.php" method="post" enctype="multipart/form-data">
    <input class="inputText" placeholder="Name"  type=text value="<?php echo $user->name(new StringT($_SESSION['nickName'])) ?>" name=name><br>
    <br><input class="inputNick" placeholder="Nick Name" type=text value="<?php echo $_SESSION['nickName'] ?>" name=nick><br><br>
    <input class="inputPassword" placeholder="Password"  type=password name=pass><br><br>
    <input class="inputSubmit" type=submit value="ATUALIZAR"> 
</form>
<a href="editPassword.php" class="editPass"><img src="Images/passwordMediumIcon-dark.png"></a>
<?php
    if (!empty($_GET['error'])) {
        echo "<center class='statusMsg'><h3 style=\"color:red;\">".$_GET['error']."</h3></center>";
    }
    if (!empty($_GET['message'])) {
        echo "<center class='statusMsg'><h3 style=\"color:green;\">".$_GET['message']."</h3></center>";
    }
?>
</center>   
</div>
</body>
</html>
