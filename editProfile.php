<?php
    include 'index.php';
    include 'Controller/FileController.php';
?>

<!DOCTYPE html>
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
                $pic = null;
                if (!empty($_FILES["pic"])) {
                    $fileController = new FileController($_FILES["pic"]);
                    $file = $fileController->getImage();
                    $format = $fileController->getFormat();
                    if ($file) {
                        $user->uploadProfilePic(new StringT($_SESSION['nickName']), $file, $format);
                    } else {
                        echo $fileController->getError();
                    }
                    echo "<div ><img src='Images/edit.png' id=\"profilePic\" class='profilePic' style='background-image:url(" . $user->downloadProfilePic(new StringT($_SESSION['nickName'])) . ");' onclick='openfile(\"editProfilePic\");' /></div>";
                } else {
                    echo "<div ><img src='Images/edit.png' id=\"profilePic\" class='profilePic' style='background-image:url(" . $user->downloadProfilePic(new StringT($_SESSION['nickName'])) . ");' onclick='openfile(\"editProfilePic\");' /></div>";
                }
            ?>

            <input id="editProfilePic" accept=".jpeg,.jpg,.webp,.png" onchange="handlePhotoUpload(event)" style="display:none;" id="editProfile" type="file" name="pic"> 
            <input class="inputSubmit salvar" onclick="uploadPic()" type="submit" value="SALVAR">
            </br>


            <div id="profileTab" class="tabContent">
                <form action="uploadProfile.php" method="post" enctype="multipart/form-data">
                    <input class="inputText" placeholder="Nome" type="text" value="<?php echo $user->name(new StringT($_SESSION['nickName'])); ?>" name="name"><br>
                    <br><input class="inputNick" placeholder="Nome de Usuário" type="text" value="<?php echo $_SESSION['nickName']; ?>" name="nick"><br><br>
                    <input class="inputPassword" placeholder="Senha" type="password" name="pass"><br><br>
                    <input class="inputSubmit" type="submit" value="ATUALIZAR">
                </form>
                <a onclick="toggleTab('passwordTab');" class="editPass"><img src="Images/passwordIcon-dark.svg"></a>
            </div>

            <div id="passwordTab" class="tabContent" style="display:none;">
                <form action="uploadPassword.php" method="post" >
                    <input class="inputPassword" placeholder="Current Password"  type=password name=currentPass><br><br>
                    <input class="inputPassword" placeholder="New Password"  type=password name=pass><br><br>
                    <input class="inputPassword" placeholder="Password Confirmation"  type=password name=passConfirmation><br><br>    
                    <input class="inputSubmit" type=submit value="ATUALIZAR"> 
                </form>
                <a onclick="toggleTab('profileTab');" class="editPro"><img src="Images/nameIcon-dark.svg"></a>
            </div>

            <?php
                if (!empty($_GET['error'])) {
                    echo "<center class='statusMsg'><h3 style=\"color:red;\">" . $_GET['error'] . "</h3></center>";
                }
                if (!empty($_GET['message'])) {
                    echo "<center class='statusMsg'><h3 style=\"color:green;\">" . $_GET['message'] . "</h3></center>";
                }
            ?>
        </center>
    </div>

    <script>
        function toggleTab(tabName) {
            var tabs = document.getElementsByClassName("tabContent");
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].style.display = "none";
            }
            document.getElementById(tabName).style.display = "block";
        }
    </script>
</body>
</html>
