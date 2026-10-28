<?php

require 'connect.php';

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

        
            header("Location: admin_posts.php");
            echo "login successfull!";
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
        <link href="css/style.css" rel="stylesheet">
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