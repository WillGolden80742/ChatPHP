<?php

    class Environment {

        public static function load($dir) {
            if(!file_exists($dir.'/.env')) {
                echo ".env not found";
            } else {
                $lines = file($dir.'/.env');
                foreach($lines as $line) {
                    putenv(trim($line));
                }
            }
        }
    }

?>