<?php
class loginSystem {

    private $conn;

    public function __construct(){
        try{
            $db_host="localhost";
            $db_user="root";
            $db_pass='';
            $db_name="loginsystem";
            $this->conn =mysqli_connect($db_host,$db_user,$db_pass,$db_name);
        }catch(mysqli_sql_exception $msg){
            die("database connection Error!!". $msg->getMessage());
        }
    }

public function registration($data){
        $email = strtolower(trim($data['email'])); // lowercase + trim
        $password = $data['psw'];
        $repassword = $data['psw-repeat'];

        // 1️⃣ Validate email
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return "Invalid email format!";
        }

        // 2️⃣ Check password match
        if($password !== $repassword){
            return "Passwords do not match!";
        }

        // 3️⃣ Check duplicate email in PHP first
        $stmt = $this->conn->prepare("SELECT ad_id FROM admin_info WHERE ad_email = ?");
        if(!$stmt) return "Database error: prepare failed";

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows > 0){
            $stmt->close();
            return "Email already registered!";
        }
        $stmt->close();

        // 4️⃣ Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // 5️⃣ Insert with try-catch to handle DB UNIQUE constraint
        $stmt = $this->conn->prepare("INSERT INTO admin_info (ad_email, ad_pass) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $hashedPassword);

        try {
            $stmt->execute();
            $stmt->close();
            return true;
        } catch(mysqli_sql_exception $e) {
            $stmt->close();
            // Duplicate key error code = 1062
            if($e->getCode() == 1062){
                return "Email already registered!";
            } else {
                return "Something went wrong: " . $e->getMessage();
            }
        }
    }
    public function loginData($data){
    $logEmail = strtolower(trim($data['admin_email']));
    $logPasswd = $data['admin_pass'];

    if(!filter_var($logEmail, FILTER_VALIDATE_EMAIL)){
        return "Invalid Email Format";
    }

    $stmt = $this->conn->prepare("SELECT ad_id, ad_email ,ad_pass FROM admin_info WHERE ad_email = ?");
    if(!$stmt){
        return "Database error: prepare failed";
    }

    $stmt->bind_param("s", $logEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 0){
        $stmt->close();
        return "Email Not Found";
    }

    $user = $result->fetch_assoc();

    if(password_verify($logPasswd, $user['ad_pass'])){
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
        session_regenerate_id(true); // prevent session fixation
        $_SESSION['user_id'] = $user['ad_id'];       // ✅ fixed
        $_SESSION['user_email'] = $user['ad_email']; // ✅ fixed
        $stmt->close();
        return true; // let index.php handle redirect
    } else {
        $stmt->close();
        return "Invalid Password";
    }
}

public function logOut(){
         unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        header("location: index.php");

    }
    
    public function __destruct(){
        $this->conn->close();
    }

    
}



?>
