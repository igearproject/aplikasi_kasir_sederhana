<?php
class Database
{
    private $servername = "localhost";
    private $username = "admin";
    private $password = "gede12345";
    private $dbname = "belajar_kasir_ai";
    public $conn;

    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    private function connectDB()
    {
        $conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        return $conn;
    }

    public function closeDB()
    {
        $this->conn->close();
    }
}
