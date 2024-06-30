<!DOCTYPE html>
<html>

<head>
    <title>ChatPHP</title>
    <link rel="icon" type="image/ico" href="Images/favicon.ico">
    <script src="assets/js/javascript.js"></script>
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        /* Estilos CSS da página */
        body {
            background: #00142f1f;
            background-image: url("Images/bg.svg");
            background-size: 50%;
        }

        input {
            backdrop-filter: blur(5px);
        }

        .header {
            z-index: 100000;
            position: absolute;
            top: 0;
            margin-top: 0;
            font-size: 24px;
            height: auto;
            width: auto;
        }

        .chat_logo {
            width: 400px;
        }

        .signUp {
            display: none;
        }

        .statusMsg {
            position: relative;
        }

        .center {
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        @media only screen and (max-width: 1080px) {
            body {
                background-size: 100%;
            }

            .inputText,
            .inputNick,
            .inputPassword,
            .inputSubmit {
                font-size: 48px;
                padding: 10px;
                height: 70px;
                padding-left: 40px;
                border-radius: 40px;
            }

            .inputSubmit {
                padding: 20px;
                padding-top: 5px;
            }

            .inputPassword {
                background-size: 4%;
            }

            .inputText,
            .inputNick {
                background-size: 5%;
            }

            .inputText,
            .inputNick {
                background-position-y: 50%;
                background-position-x: 20px;
                padding-left: 70px;
            }

            .inputPassword {
                background-position-y: 50%;
                background-position-x: 20px;
                padding-left: 70px;
            }

            .chat_logo {
                position: static;
                width: 666px;
            }

            .header a {
                font-size: 64px;
            }

            .center h3 {
                font-size: 32px;
            }

            .center {
                transform: translateY(50%);
            }
        }
    </style>
</head>

<body class="container">

    <?php
    $userNickName = "";
    include 'Controller/AuthenticateController.php';
    $user = new AuthenticateController();
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    switch ($action) {
        case 'login':
            if (!empty($_POST["nickLogin"]) && !empty($_POST["passLogin"])) {
                $nick = strtolower($_POST["nickLogin"]);
                $pass = $_POST["passLogin"];
                $user->login(new StringT($nick), $pass);
                echo "<script>showLogin();</script>";
            }
            break;
        case 'signUp':
            if (!empty($_POST["name"]) && !empty($_POST["nick"]) && !empty($_POST["pass"]) && !empty($_POST["passConfirmation"])) {
                $user->signUp($_POST["name"], strtolower($_POST["nick"]), $_POST["pass"], $_POST["passConfirmation"]);
                echo "<script>showSignUp();</script>";
            }
            break;
    }
    ?>

    <div class="header">
        <h2>
            <?php
            echo "<a href='javascript:showLogin();'>Login</a> <a>|</a> <a href='javascript:showSignUp();'>Sign Up</a>";
            ?>
        </h2>
    </div>



    <div class="signUp" id="signUp">
        <div class="center">
            <img class="chat_logo" src="Images/chat.svg" />
            <form action="authenticate.php" method="post">
                <br>
                <input class="inputText" placeholder="Name" type="text" name="name"><br>
                <br>
                <input class="inputNick" placeholder="Nick Name" type="text" name="nick"><br>
                <br>
                <input class="inputPassword" placeholder="Password" type="password" name="pass"><br>
                <br>
                <input class="inputPassword" placeholder="Password Confirmation" type="password" name="passConfirmation"><br>
                <br>
                <input type="hidden" name="action" value="signUp"><br>
                <input class="inputSubmit" type="submit" value="SIGN UP">
            </form>
        </div>

    </div>

    <div class="login" id="login">
        <div class="center">
            <img class="chat_logo" src="Images/chat.svg" />
            <form action="authenticate.php" method="post">
                <br>
                <input class="inputNick" placeholder="Nick Name" type="text" name="nickLogin"><br>
                <br>
                <input class="inputPassword" placeholder="Password" type="password" name="passLogin"><br>
                <br>
                <input type="hidden" name="action" value="login"><br>
                <input class="inputSubmit" type="submit" value="LOGIN">
            </form>
        </div>

    </div>
    <script>
        function showLogin() {
            document.getElementById('login').style.display = 'block';
            document.getElementById('signUp').style.display = 'none';
        }

        function showSignUp() {
            document.getElementById('login').style.display = 'none';
            document.getElementById('signUp').style.display = 'block';
        }
        setTimeout(function() {
            removerStatusMsg();
        }, 3000);
    </script>
</body>

</html>