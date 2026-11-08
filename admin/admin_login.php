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

//submitting the form
if(isset($_POST['submit'])){ 
    // $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die ("Error" . mysqli_error($conn));
    }
    else {
        if($result-> num_rows > 0){
            $row = mysqli_fetch_assoc($result); // fetching the $result

            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['name'] = $row['name'];

                    
            header("Location: admin/admin_dashboard.php");
            // echo "login successfull!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="admin.css" rel="stylesheet">
    </head>
    <body>
        <form action="admin_login.php" method="POST">
            <!-- Name: <input type="text" name="name" required><br> -->
            Email: <input type="email" name="email" required"><br>
            Password: <input type="password" name="password" required><br>
            <button type="submit" name="submit">Login</button>
        </form>
    </body>
</html>