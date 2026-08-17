<?php
class Database
{
    private string $host="127.0.0.1";
    private string $username= "root";
    private string $password= "";
    private string $database = "job_applications";
    private $conn;

    /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $database
     */

    public function connect(){

        $this->conn=new mysqli($this->host,$this->username,$this->password,$this->database);


        if($this->conn->connect_error){
            die("Connection failed: ". $this->conn->connect_error);
        }

        return $this->conn;

    }
}