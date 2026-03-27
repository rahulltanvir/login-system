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

public function LoginData($data) {
        $email = trim($data['admin_email']);
        $password = $data['admin_pass']; // plain password from form

        $stmt = $this->conn->prepare("SELECT * FROM admin_info WHERE ad_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Check old MD5 first
            if (md5($password) === $row['ad_pass']) {
                // Login success, now upgrade to password_hash
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $update = $this->conn->prepare("UPDATE admin_info SET ad_pass=? WHERE ad_email=?");
                $update->bind_param("ss", $newHash, $email);
                $update->execute();

                // Session
                session_regenerate_id(true);
                $_SESSION['user'] = $row['ad_email'];
                return true;
            }

            // Check password_hash
            if (password_verify($password, $row['ad_pass'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = $row['ad_email'];
                return true;
            }

            return "Invalid Email or Password";

        } else {
            return "Invalid Email or Password";
        }
    }

 
}




?>