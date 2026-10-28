<?php

require 'connect.php'; // connection to the database

//submitting the form
if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // include "roboai_blog_db.sql";
    session_start();

    $sql = "INSERT INTO admin(name, email, password) VALUES('$name', '$email', '$password')";
    $result = mysqli_query($conn, $sql);
    if (!$result){
        die("Error". mysqli_error($conn));
    }
    else {
        echo "Registration successful";
        header("Location: admin_login.php");
    }

}
?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin Registration</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="css/style.css" rel="stylesheet">
    </head>
    <body>
        <h1>Admin Registration</h1>
        <form action="admin_register.php" method="POST">
            Name: <input type="text" name="name" required><br>
            Email: <input type="email" name="email" required"><br>
            Password: <input type="password" name="password" required><br>
            <input type="submit" name="submit" value="Register">
        </form>
    </body>
</html>