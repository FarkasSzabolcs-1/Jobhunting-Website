<?php
require_once "Application.php";

class Job
{

    private $conn;

    /**
     * @param $conn
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function jobUpload(string $title, string $company, string $position,string $category,string $city,float $salary, string $description,string $created_by){
        $stmt=$this->conn->prepare("INSERT INTO jobs (title,company,position,category,city,salary,description,created_by) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssdss",$title,$company,$position,$category,$city,$salary,$description,$created_by);
        if($stmt->execute()){
            return true;
        }
        else{
            return false;
        }
    }

    public function jobInformation($sql_filter,$parameterek,$tipusok){

        $sql_lekerdezes=$sql_filter;
        $stmt=$this->conn->prepare($sql_lekerdezes);
        if(!empty($parameterek)){
            $stmt->bind_param($tipusok,...$parameterek);
        }

        $stmt->execute();
        $result=$stmt->get_result();
        $information=$result->fetch_all();
        return $information;
    }


    public function jobUpdate($sql_filter,$parameterek,$tipusok){

        $sql_lekerdezes=$sql_filter;
        $stmt=$this->conn->prepare($sql_lekerdezes);
        if(!empty($parameterek)){
            $stmt->bind_param($tipusok,...$parameterek);
        }


        if($stmt->execute()){
            return true;
        }
        $stmt->close();
        return false;
    }

    public function jobDelete(int $id){

        $application=new Application($this->conn);

        $applications_list=$application->jobApplies($id);

        foreach ($applications_list as $app){
            $application->applicationDelete($app[1]);
        }


        $stmt=$this->conn->prepare("DELETE FROM jobs WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        return true;
    }





}