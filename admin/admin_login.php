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
    // $name = $_POST['name'];
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
            // echo "login successfull!";
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
                <p class="site-title">
                    <!-- <a href="#" class="logo">ROBOAI</a> -->
                    <a href="#"><img class="log" src="/ROBOAI/images/logo.index.png" alt="Roboai Logo"></a>
                </p>
            </div>
        </header>
        <div>
            <h1 style="text-align:center; margin-top: 100px; margin-bottom: 20px">Admin Login</h1>
        </div>

        <?php
            if (isset($_GET['registered'])) {
                echo '<p style="color:green;">Account Created. Please log in.</p>';
            }
            if ($error !== '') {
                echo '<p style="color:red;">' . htmlspecialchars($error) . '</p>';
            }
        ?>

        <div class="container" style="max-width: 350px;">
            <form action="admin_login.php" method="POST">
                <!-- Name: <input type="text" name="name" required><br> -->
                 <div>
                    <label class="form-label">Email</label><br>
                    <input class="form-control" type="email" name="email" required><br>
                </div>
                <div>
                    <label class="form-label">Password</label><br>
                    <input class="form-control" type="password" name="password" required><br>
                </div>
                <button type="submit" name="submit">Login</button>
            </form>
        </div>
    </body>
</html>