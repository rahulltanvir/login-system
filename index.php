<?php
session_start(); // Must be first

include("function.php");
$adminLoginobj = new loginSystem();

$error = "";

// Generate CSRF token if not exists
if(!isset($_SESSION['token'])){
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Redirect if already logged in
if(isset($_SESSION['user_id'])){
    header("Location: dashboard.php");
    exit();
}

// Handle form submission
if(isset($_POST['submit'])){
    // CSRF check
    if(!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']){
        die("Invalid CSRF token");
    }

    $loginResult = $adminLoginobj->loginData($_POST);
    if($loginResult === true){
        header("Location: dashboard.php");
        exit();
    } else {
        $error = $loginResult; // show error
    }
}
?>



<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Login Form</h2>

<form class="form-control" action="" method="post">

  <div class="container">
    <label><b>Email</b></label><br>
    <input type="email" name="admin_email" required><br>

    <label><b>Password</b></label><br>
    <input type="password" name="admin_pass" required><br>

    <!-- CSRF TOKEN -->
    <input type="hidden" name="token" value="<?php if(isset($_SESSION))echo $_SESSION['token']; ?>">

    <button type="submit" name="submit">Login</button><br>
  </div>

</form>

</body>
</html>
