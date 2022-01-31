<?php
   
    class Sessions {
        
        function setSession($key,$value) {
            $key="a".$key;
            $_SESSION[$key] = $value;
        }

        function getSession($key) {
            $key="a".$key;
            if (empty($_SESSION[$key])) {
                return "";
            } else {
                return $_SESSION[$key];
            }
        }

        function clearSession($key) {
            $key="a".$key;
            $_SESSION[$key] = "";
        }
    }

?>    