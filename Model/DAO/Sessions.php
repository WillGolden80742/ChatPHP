<?php
   
    class Sessions {
        
        function setSession($key,$value) {
            $_SESSION[$key->__toString()] = $value;
        }

        function getSession($key) {
            if (empty($_SESSION[$key->__toString()])) {
                return "";
            } else {
                return $_SESSION[$key->__toString()];
            }
        }

        function clearSession($key) {
            $_SESSION[$key->__toString()] = "";
        }
    }

?>    