<?php include 'index.php' ?>
<html>
<head>  
<link rel="stylesheet" href="assets/css/styleNoIndex.css">
</head>     
<style id="stylePic">
    .profilePic {
        background:none;
        border:solid 3px #285d33;
        border-radius:100%;
        width:300x;
        height:300px;
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
<form action="editProfilePic.php" method="post" enctype="multipart/form-data">
    <input id="editProfilePic" accept=".jpeg,.jpg,.png" onchange="display();" style="display:none;" id="editProfile" type="file" name="pic"> 
    <input class="inputSubmit salvar" type=submit value="SALVAR">
</form>
<a href="editProfile.php" class="editPro"><img src="Images/nameMediumIcon-dark.png"></a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="editPassword.php" class="editPass"><img src="Images/passwordMediumIcon-dark.png"></a>
</center>   
</div>
</body>
</html>
