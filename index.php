<?php
session_start();
include("function.php");

$loginObj = new loginsystem();

if(isset($_POST['submit'])){

    // CSRF check
    if($_POST['token'] !== $_SESSION['token']){
        die("Invalid Request");
    }

    $result = $loginObj->LoginData($_POST);

    if($result === true){
        header("Location: dashboard.php");
    }else{
        echo $result;
    }
}

// CSRF token generate
$_SESSION['token'] = bin2hex(random_bytes(32));
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
    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">

    <button type="submit" name="submit">Login</button><br>
  </div>

</form>

</body>
</html>
