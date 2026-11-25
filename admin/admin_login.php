<?php

// require 'connect.php';

$host = "localhost";
$user = "root";
$password = "";
$database = "roboai_blog_db";
$port = 3307;

$conn =mysqli_connect($host, $user, $password, $database, $port);

if (!$conn ) {
    die("Connection failed: " . mysqli_connect_error());

}


session_start(); // starting the session
$error = '';

//submitting the form
if(isset($_POST['submit'])){ 
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT admin_id, name, password FROM admin WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($row && password_verify($pass, $row['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $row['admin_id'];
        $_SESSION['name'] = $row['name'];

        header("Location: admin_dashboard.php");
        exit;
        
    }
    $error = 'Invalid email or password.';
}


?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin Login</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <link href="admin.css" rel="stylesheet">
    </head>
    <body>

        <header>
             <div class="log-content">
                <p>
                    <a href="#"><img  class="img-logo" class="log" src="/ROBOAI/images/logo.index.png" alt="Roboai Logo"></a>
                </p>
            </div>
        </header>
        <div class="container mb-5" style="max-width: 350px;">
            <?php
            if (isset($_GET['registered'])) {
                echo '<p style="color:green;">Account Created. Please log in.</p>';
            }
            if ($error !== '') {
                echo '<p style="color:red;">' . htmlspecialchars($error) . '</p>';
            }
            ?>

        <div class="mb-3">
            <h1 style="text-align:center; margin-top: 10px; margin-bottom: 20px">Admin Login</h1>
        </div>

            <form action="admin_login.php" method="POST">
                 <div class="mb-3">
                    <label class="form-label">Email</label><br>
                    <input class="form-control" type="email" name="email" required><br>
                </div>
                <div class="mb-2">
                    <label class="form-label">Password</label><br>
                    <input class="form-control" type="password" name="password" required><br>
                </div>
                <button class="btn btn-primary w-100 mt-1" type="submit" name="submit">Login</button>
            </form>

            <a href="../home.php">
                <p style="text-align:center;">Home</p>
            </a>
        </div>        

        <footer class="footer">ROBOAI Blog 2026 | <a href="../home.php">HOME</a> | Email: ROBOAIgen@gmail.com | contact: +004566000</footer>
    </body>
</html>