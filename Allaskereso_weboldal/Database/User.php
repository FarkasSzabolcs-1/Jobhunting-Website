<?php

class User
{
    private $conn;

    /**
     * @param int $id
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }


    public function userRegister(string $username, string $email,string $password):bool{
        $stmt=$this->conn->prepare("SELECT id,username,password FROM users WHERE username = ? OR email = ?");

        $stmt->bind_param("ss",$username,$email);
        $stmt->execute();
        $result=$stmt->get_result();

        if($result->num_rows===0) {
            $hashed_password=password_hash($password,PASSWORD_DEFAULT);
            $stmt=$this->conn->prepare("INSERT INTO users (username,email,password) VALUE (?,?,?)");
            $stmt->bind_param("sss",$username,$email,$hashed_password);
            if($stmt->execute()){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }

    }


    public function userLogin(string $username,string $password){
        $stmt=$this->conn->prepare("SELECT id,username,password FROM users WHERE username = ?");

        $stmt->bind_param("s",$username);
        $stmt->execute();
        $result=$stmt->get_result();
        $user=$result->fetch_assoc();
            if(password_verify($password,$user["password"])){
                return $user["id"];

            }
            else {
                return false;
            }
        }

    public function userInformation(int $id):array{

        $stmt=$this->conn->prepare("SELECT id,username, email, cv FROM users WHERE id=? ");

        $stmt->bind_param("i",$id);
        $stmt->execute();
        $result=$stmt->get_result();
        $information=$result->fetch_assoc();
        return $information;

    }

    public function userCreatedJobs(string $user_name):array{
        $stmt=$this->conn->prepare("SELECT id,title,company,position,category,city,salary,description,created_by,is_active FROM jobs WHERE created_by=?");
        $stmt->bind_param("s",$user_name);
        $stmt->execute();
        $result=$stmt->get_result();
        $information=$result->fetch_all();
        return $information;
        
        
    }

    public function userApplies(int $user_id):array{
        $stmt=$this->conn->prepare("SELECT id,job_id,user_id,status FROM job_applications WHERE   user_id =?");
        $stmt->bind_param("i",$user_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $row = $result->fetch_all();
        if($row==null){
            return [];
        }
        else{
            return $row;
        }
    }

    public function cvUpdate(int $id,string $cv){
        $stmt=$this->conn->prepare("UPDATE users SET cv=? WHERE id=?");
        $stmt->bind_param("si",$cv,$id);
        $stmt->execute();
        return true;





    }

}