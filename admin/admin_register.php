<?php

// require 'connect.php'; // connection to the database

$host = "localhost";
$user = "root";
$password = "";
$database = "roboai_blog_db";
$port = 3307;

$conn =mysqli_connect($host, $user, $password, $database, $port);

if (!$conn ) {
    die("Connection failed: " . mysqli_connect_error());

}


$error = '';
//submitting the form
if(isset($_POST['submit'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($name === '' ||$email === '' ||$pass === '') {
        $error = 'Please fill in all fields.';
    } elseif (strlen($pass) < 8) {
        $error ='Password must be at least 8 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email.';
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "INSERT INTO admin(name, email, password) VALUES(?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $hash);

        if (mysqli_stmt_execute($stmt)){
            header("Location: admin_login.php?registered=1");
            exit;
        }
        if (mysqli_stmt_errno($stmt) === 1062) {
            $error = 'That email is already registered.';
        } else {
            $error = 'Could not register. Please try again.';
        }
    }
    
}


?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin Registration</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <link href="admin.css" rel="stylesheet">
    </head>
    <body>

        <header>
            <div class="log-content">
                <p class="site-title">
                    <!-- <a href="#" class="logo">ROBOAI</a> -->
                    <a href="#"><img class="log" src="/ROBOAI/images/logo.index.png" alt="Roboai Logo"></a>
                </p>
            </div>
            <button style="float:right; margin-right:10px;">
                <a href="admin_login.php">Logout</a>
            </button>
        </header>

        <div>
            <h1 style="text-align: center;">Admin Registration</h1>
        </div>
        

        <?php if (!empty($error))
             echo '<p style="color:red;">' . htmlspecialchars($error) . '</p>'; 
        ?>
        <div class="container" style="max-width: 500px;">
            <form action="admin_register.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Name</label><br>
                    <input class="form-control" type="text" name="name" required><br>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label><br>
                    <input class="form-control" type="email" name="email" required><br>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label><br>
                    <input class="form-control" type="password" name="password" required><br>
                </div>
                <input type="submit" name="submit" value="Register">
            </form>
        </div>


        <div>
            <footer class="footer">ROBOAI Blog 2026 | <a href="admin_dashboard.php">Dashboard</a> | <a href="admin_posts.php">Manage Posts</a> | Register | <a href="admin_login.php">LogOut</a> |</footer>
        </div>
    </body>
</html>