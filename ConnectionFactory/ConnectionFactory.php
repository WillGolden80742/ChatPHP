<?php

use JetBrains\PhpStorm\ArrayShape;

class ConnectionFactory {
        private $servername;
        private $username;
        private $password;
        private $dbname;
        private $connection;

        function __construct() {
            $this->servername = "localhost";
            $this->username = "root";
            $this->password = "";
            $this->dbname = "Chat";
        }

        function query ($sql) {
            $conn = $this->connect() ;
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $query = mysqli_query($this->connection,$sql);
            $this->close();
            return $query;
        }

        function connect() {
            $this-> connection = mysqli_connect($this->servername,$this->username,$this->password,$this->dbname);
            return $this->connection;
        }

        function close () {
            mysqli_close($this->connection);  
        }
    }
?>