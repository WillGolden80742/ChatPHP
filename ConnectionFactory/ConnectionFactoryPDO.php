<?php

class ConnectionFactoryPDO
{
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $conn;

    function __construct()
    {
        $this->servername = getenv('MYSQL_SERVER');
        $this->username = getenv('MYSQL_USER');
        $this->password = getenv('MYSQL_PASSWORD');
        $this->dbname = getenv('MYSQL_DB');
    }

    function connect()
    {
        try {
            $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    function query($sql)
    {
        $query = $this->connect()->prepare($sql);
        return $query;
    }

    function execute($query)
    {
        $query->execute();
        $this->close();
        return $query;
    }

    function close()
    {
        $this->conn = null;
    }
}
