<?php
// the post detail page-comment form, podt comment
    // print_r($_GET);
    // file_put_contents('data.txt', print_r($_POST, true));
    // echo "your data hase been submitted";
    // var_dump(get_magic_quotes_gpc());
    // print_r($_POST);

    require 'connect.php'; // the file that creates $conn / connection
//making the $_POST safe to read
     if($_SERVER['REQUEST_METHOD'] !=='POST') {
        ECHO "Please fill in the form.";
        exit;
    }

    $name =$_POST['name'];
    $email =$_POST['email'];
    $comment =$_POST['comment'];

    $stmt = mysqli_prepare($conn,
    //the insert statement to the database
     "INSERT INTO comments (`Name`, `Email`, `Message`) VALUES(?, ?, ?)"); // the ??? are the palceholder, 

    // var_dump($name, $email, $comment); // diplays the outcome 
    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $comment);
    // mysqli_stmt_execute($stmt);

   
    if (mysqli_stmt_execute($stmt)) {
        echo "saved!"; 
    }
    else {
        echo "Error: " . mysqli_stmt_error($stmt);
    }

//  vistor goes back to the page
    if (mysqli_stmt_execute($stmt)) {
        header("Location: post_detail.html");
        exit;
    } 
    else {
        echo "Error: " . mysqli_stmt_error($stmt);
    }

?>
