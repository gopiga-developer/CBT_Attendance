<?php 

class Database
{
    protected mysqli $connection;

    public function __construct()
    {
        $this->connection = new mysqli("localhost", "root", "9566","attendance_system");

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);

        
        }
    }

    public function getConnection(): mysqli
    {
        return $this->connection;
    }
}