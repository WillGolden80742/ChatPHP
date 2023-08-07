<?php

use JetBrains\PhpStorm\ArrayShape;

include 'global.php';
class ConnectionFactory
{
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $connection;

    function __construct()
    {
        $this->servername = getenv('MYSQL_SERVER');
        $this->username = getenv('MYSQL_USER');
        $this->password = getenv('MYSQL_PASSWORD');
        $this->dbname = getenv('MYSQL_DB');
    }

    function query($sql)
    {
        $conn = $this->connect();
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        $query = mysqli_query($this->connection, $sql);
        $this->close();
        return $query;
    }

    function connect()
    {
        $this->connection = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
        return $this->connection;
    }

    function close()
    {
        mysqli_close($this->connection);
    }
}
