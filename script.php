<?php
// die("version 3 of script.php");
// the post detail page-comment form, podt comment
    // print_r($_GET);
    // file_put_contents('data.txt', print_r($_POST, true));
    // echo "your data hase been submitted";
    // var_dump(get_magic_quotes_gpc());
    // print_r($_POST);
    // print_r($_POST);
    // exit;

    require 'connect.php'; // the file that creates $conn / connection
//making the $_POST safe to read
     if($_SERVER['REQUEST_METHOD'] !=='POST') {
        ECHO "Please fill in the form.";
        exit;
    }

    $name =trim($_POST['name']);
    $email =trim($_POST['email']);
    $comment =trim($_POST['comment']);
    $post_id = (int) $_POST['post_id'];

    // spaces only name/ omment
    if ($name === '' || $comment === '') {
        // die("please write an appropriate name,. <a href='javascript:history.back()'>GO back</a>");
        header("Location: post_details.php?id=$post_id&error=empty");
        exit;
    }

    //rejecting names and comments that are too long
    if (mb_strlen($name) > 30) {
        // die("must be 30 characters or less. <a href='javascript:history.back()'> Go back</a>");
        header("Location: post_details.php?id=$post_id&error=name_long");
        exit;
    }

    if (mb_strlen($comment) > 1000) {
        // die("1Must be 1000 characters or less. <a href='javascript:history.back()'> Go back</a>");
        header("Location: post_details.php?id=$post_id&error=comment_long");
        exit;
    }

    // validating email
    if (!filter_var($email,  FILTER_VALIDATE_EMAIL)) {
        // die("Please enter a valid email address. <a href='javascript:history.back()'>Go back</a>"); // validate email with go back
        header("Location: post_details.php?id=$post_id&error=bad_email");
        exit;
    }

    //the insert statement to the database
    $stmt = mysqli_prepare($conn,
     "INSERT INTO comments (`Name`, `Email`, `Message`,`post_id` ) VALUES(?, ?, ?, ?)"); // the ??? are the palceholder, 


    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($conn));
}

    // var_dump($name, $email, $comment); // diplays the outcome 
    mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $comment, $post_id);
    // mysqli_stmt_execute($stmt);


   
    // if (mysqli_stmt_execute($stmt)) {
    //     echo "saved!"; 
    // }
    // else {
    //     echo "Error: " . mysqli_stmt_error($stmt);
    // }

//  vistor goes back to the page
    if (mysqli_stmt_execute($stmt)) {
        header("Location: post_details.php?id=$post_id");
        exit;
    } 
    else {
        echo "Error: " . mysqli_stmt_error($stmt);
    }

//     if (empty($_POST["email"])) {
//     $emailErr = "Email is required";
//    }
//     else{
//     $email = ($_POST["email"]);
//     checks for a well formed email address
//     if (!filter_var($email,  FILTER_VALIDATE_EMAIL)) {
//         $emailErr = "Invalid email format";
//     }
//    }


?>
