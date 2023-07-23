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

    #profileContent {
        display:none;
    }

</style>
<script>

    function fadeIn(element) {
        $(element).fadeIn(250); // 250 milissegundos (0.25 segundos)
    }



    function loadProfileContent() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var profileContent = this.responseText + editProfileMessage;
                document.getElementById("profileContent").innerHTML = profileContent;

                var profileContentElement = document.getElementById("profileContent");
                fadeIn(profileContentElement);  // Aplica a transição de opacidade
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
        <div id="profileContent">
            <!-- Conteúdo de perfil carregado através do AJAX -->
        </div>
    </center>
</div>
</body>
</html>
