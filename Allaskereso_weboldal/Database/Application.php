<?php

class Application
{
    private $conn;

    /**
     * @param $conn
     */
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function job_apply(int $user_id,int $job_id):bool{
        $stmt=$this->conn->prepare("INSERT IGNORE INTO job_applications (job_id,user_id) VALUES (?,?) ");
        $stmt->bind_param("ii",$job_id,$user_id);
        if($stmt->execute()){
            return $stmt->affected_rows > 0;
        }
        else{
            error_log("Job apply hiba: " . $stmt->error);
            return false;
        }
    }

    public function checkApply(int $user_id,int $job_id):array{
        $stmt=$this->conn->prepare("SELECT status FROM job_applications WHERE job_id =? AND  user_id =? LIMIT 1");
        $stmt->bind_param("ii",$job_id,$user_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $row = $result->fetch_assoc();
        if($row==null){
            return [];
        }
        else{
            return $row;
        }
    }

    public function jobApplies(int $job_id):array{
        $stmt=$this->conn->prepare("SELECT status,id,user_id,job_id FROM job_applications WHERE job_id =?");
        $stmt->bind_param("i",$job_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $applications = $result->fetch_all();
        if($applications==null){
            return [];
        }
        else{
            return $applications;
        }
    }

    public function applicationInfo(int $id):array{
        $stmt=$this->conn->prepare("SELECT id,status,user_id,job_id FROM job_applications where  id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $result=$stmt->get_result();
        $application=$result->fetch_all();
        if($application==null){
            return [];
        }
        else{
            return $application;
        }

    }
    public function applicationDelete(int $id):bool{

        $stmt=$this->conn->prepare("DELETE FROM job_applications WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();

        return true;
    }

    public function applicationStatusUpdate(string $status,int $id):bool{
        $stmt=$this->conn->prepare("UPDATE job_applications SET status=? WHERE id=?");
        $stmt->bind_param("si",$status,$id);
        $stmt->execute();

        return true;
    }

}