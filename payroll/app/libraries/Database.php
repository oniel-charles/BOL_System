<?php
 class Database {
     private $dbHost=DB_HOST;
     private $dbUser=DB_USER;
     private $dbPass=DB_PASS;
     private $dbName=DB_NAME;
     
     public $statement;
     private $dbHandler;
     private $error;
     public  $models;
     public $conn;
     public $result;

     public function __construct(){
         $this->conn=  new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);  
         if (mysqli_connect_errno()){
            header("HTTP/1.1 401 Unauthorized");
            exit(mysqli_connect_error());
          }
          $this->getModels();
     }
     public function getModels(){
        if(file_exists('tables.json')){ 
            $myfile = fopen("tables.json", "r") or die("Unable to open file!");
            $str= fread($myfile,filesize("tables.json"));
            fclose($myfile);
            $this->models = json_decode($str);
        }
     }
        //Execute the prepared statement
        public function execute() {
            $this->result= mysqli_query($this->conn,$this->statement);
        }

        //Return an array
        public function returnResultSet() {
           
        }

        //Return a specific row as an object
        public function single() {
            $this->execute();
            return $this->statement->fetch(PDO::FETCH_OBJ);
        }

        //Get's the row count
        public function rowCount() {
            return $this->statement->rowCount();
        }
 }
?>