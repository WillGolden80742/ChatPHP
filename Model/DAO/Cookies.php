<?php
   
    class Cookies {
        
        function setCookie($key,$value) {
            $_SESSION[$key->__toString()] = $value;
        }

        function getCookie($key) {
            if (empty($_SESSION[$key->__toString()])) {
                return "";
            } else {
                return $_SESSION[$key->__toString()];
            }
        }
    }

?>    