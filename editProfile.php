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
        backdrop-filter: none;
        border: none;
        box-shadow: none;
    }

</style>
<script>



    function loadProfileContent() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var profileContent = this.responseText + editProfileMessage;
                document.getElementById("profileContent").innerHTML = profileContent;
            }
        };
        xhttp.open("GET", "contentEditProfile.php", true);
        xhttp.send();
    }

    function toggleTab(tabName) {
        var tabs = document.getElementsByClassName("tabContent");
        for (var i = 0; i < tabs.length; i++) {
            tabs[i].style.display = "none";
        }
        document.getElementById(tabName).style.display = "block";
    }

    document.addEventListener("DOMContentLoaded", function() {
        loadProfileContent(); // Carrega o conteúdo do perfil ao carregar a página
    });
    
</script>
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
        <div id="profileContent">
            <!-- Conteúdo de perfil carregado através do AJAX -->
        </div>
    </center>
</div>
</body>
</html>
