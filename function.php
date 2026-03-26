<?php
class loginsystem{
    private $conn;

    public function __construct(){
        try{
            $db_host="localhost";
            $db_user="root";
            $db_pass='';
            $db_name="loginsystem";

            $this->conn =mysqli_connect($db_host,$db_user,$db_pass,$db_name);

        }catch(mysqli_sql_exception $msg){
            die("Database connection Error".$msg->getMessage());
        }
    }
    
}

?>