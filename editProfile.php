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
        foreach ($lines_array as $line) {
            echo $line;
        }
        ?>.header {
            backdrop-filter: none;
            border: none;
            box-shadow: none;
        }

        .center {
            justify-content: center;
            align-items: center;
            text-align: center;
        }
    </style>
    <script>
        function toggleTab(tabName) {
            var tabs = document.getElementsByClassName("tabContent");
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].style.display = "none";
            }
            document.getElementById(tabName).style.display = "block";
        }
    </script>
</head>

<body class="container">
    <div class="editProfile">
        <div class="center">
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
            }
            echo "<div ><img src='Images/edit.png' id=\"profilePic\" class='profilePic' style='background-image:url(" . $user->downloadProfilePic(new StringT($_SESSION['nickName'])) . ");' onclick='openfile(\"editProfilePic\");' /></div>";
            ?>
            <div id="profileContent">
                <?php include "profileEditForm.php"; ?>
            </div>
        </div>
    </div>
</body>

</html>