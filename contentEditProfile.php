<?php
    include 'Controller/UsersController.php'; 
    $user = new UsersController();  
    $auth = new AuthenticateModel();

?>

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
            
                <input class="inputText" placeholder="Name"  type=text value="<?php echo $user->name(new StringT($_SESSION['nickName'])) ?>" name=name><br>
                <br><input class="inputNick" placeholder="Nick Name" type=text value="<?php echo $_SESSION['nickName'] ?>" name=nick><br><br>
                <input class="inputPassword" placeholder="Password"  type=password name=pass><br><br>
                <input class="inputSubmit" type=submit onclick="uploadProfile();" value="ATUALIZAR"> 
                <br><br><a onclick="toggleTab('passwordTab');" class="editPass"><img src="Images/passwordIcon-dark.svg"></a>
          
            </div>

            <div id="passwordTab" class="tabContent" style="display:none;">
            
                <input class="inputPassword" placeholder="Current Password"  type=password name=currentPass><br><br>
                <input class="inputPassword" placeholder="New Password"  type=password name=pass><br><br>
                <input class="inputPassword" placeholder="Password Confirmation"  type=password name=passConfirmation><br><br>    
                <input class="inputSubmit" type=submit onclick="uploadPassword();" value="ATUALIZAR"> 
                <br><br><a onclick="toggleTab('profileTab');" class="editPro"><img src="Images/nameIcon-dark.svg"></a>
            
            </div>
        </center>