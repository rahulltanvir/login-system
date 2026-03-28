<?php
class LoginSystem {

    private $conn;
    
    public function __construct(){
        try{
            $this->conn = mysqli_connect("localhost","root","","loginsystem");
        }catch(mysqli_sql_exception $msg){
            die("Database connection Error ".$msg->getMessage());
        }
    }

    public function LoginData($data) {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Validate
        $email = filter_var(trim($data['admin_email']), FILTER_VALIDATE_EMAIL);
        $password = $data['admin_pass'];

        if (!$email || empty($password)) {
            return "Invalid Input";
        }

        // Query
        $stmt = $this->conn->prepare("SELECT * FROM admin_info WHERE ad_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['ad_pass'])) {

                session_regenerate_id(true);

                $_SESSION['user'] = $row['ad_email'];
                $_SESSION['login_time'] = time();

                return true;
            }
        }

        return "Invalid Email or Password";
    }
}


?>