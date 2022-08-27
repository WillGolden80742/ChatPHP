<?php 
    include 'index.php';
    include 'Controller/FileController.php';
?>
<html>
<head>  
<link rel="stylesheet" href="assets/css/styleNoIndex.css">
</head>     
<style id="stylePic">
    .salvar, .editPic{
        display:none;
    }

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

    @media only screen and (max-width: 1080px) {
        .profilePic {
          width:320px;
          height:320px;
        }
        center h3 {
            font-size:32px;
        }
        .header {
            height:50px;
        }

        .header {
            height:0;
            border-bottom:0;
            border-radius:0;
            position:fixed;
            box-shadow: none;
        }
        .back {
            margin-top:36px;
        }
    } 

</style>    
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
        echo "<div ><img src='Images/edit.png' class='profilePic' style='background-image:url(".$user ->downloadProfilePic(new StringT($_SESSION['nickName'])).");' onclick='openfile();' /></div>";
    } else {
        echo "<div ><img src='Images/edit.png' class='profilePic' style='background-image:url(".$user ->downloadProfilePic(new StringT($_SESSION['nickName'])).");' onclick='openfile();' /></div>";
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
