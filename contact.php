<?php
require 'connect.php';


$message = '';
// reading the fields into variables
if (isset($_POST['submit'])) {   // the isset($_POST ['submit ]) checks the form actually submitted beform reading anything
    // read fields
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject =trim($_POST['subject'] ?? '');


    //validation, checking the data before saving
    if ($firstname === '' || $lastname === '' || $subject === '') {
        $message = 'Please  fill in the fields.';
    } elseif (mb_strlen($firstname) > 100){
        $message = 'The firstname should be 100 characters or less.';
    } elseif (mb_strlen($lastname) > 100) {
        $message  = 'The lastname should be 100 characters or less.';
    } elseif (mb_strlen($subject) > 500) {
        $message = 'The subject should be 500 chatacters or less.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email.';
    }

    //save to the database
    if ($message === '')  {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO contact_us(firstname, lastname, email, subject) VALUES (?, ?, ?, ?)");

        mysqli_stmt_bind_param($stmt, 'ssss', $firstname, $lastname, $email, $subject);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: contact.php?sent=1");
            exit;
        } else {
            $message = 'Error: ' . mysqli_stmt_error($stmt);

        }
    }
}
//message when successful
if (isset($_GET['sent'])) {
    $message = 'Thanks for reaching out, sent successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CONTACT US</title>
    <link rel="stylesheet" href="style.css">
</head>

    <body class="contact">
        <header>
            <div class="blog-header">
                <div class="logo">
                    <!-- <a href="#" class="logo">ROBOAI</a> -->
                    <a href="#"><img class="logo-img" style="height: 50px; width: auto;" src="/ROBOAI/images/logo2.index.png" alt="Roboai Logo"></a>
                </div>
            </div>
            <div class="topnav">       
                <nav>
                    <a href="home.php">Home</a>
                    <a href="post_categories.php">Post/Categories</a>
                    <a href="about_us.html">About Us</a>
                    <a class="active" href="contact.php">Contact Us</a>
                </nav>
            </div>
        </header>
       

        <div class="container">
            <div style="text-align:center">
                <h2>Contact Us</h2>
                <p>To let us know how we can be of help</p>
            </div>

            <div>
                <?php if ($message !== '') {
                    $color =str_starts_with($message, 'Thanks') ? 'green' : 'red';
                    echo '<p style="color:' . $color . ';">' . htmlspecialchars($message) . '</p>';
                } ?>
            </div>
            <div class="box">
                <form action="contact.php" method="POST">
                    <label for="fname">First Name</label>
                    <input type="text" value="<?php echo htmlspecialchars($_POST['firstname'] ?? ''); ?>" id="fname" name="firstname" placeholder="Your name..">
                    <label for="lname">Last Name</label>
                    <input type="text" value="<?php echo htmlspecialchars($_POST['lastname'] ?? ''); ?>" id="lname" name="lastname" placeholder="Your last name..">
                    <label for="email">Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" id="email" name="email" placeholder="Enter your email..">
                    <label for="subject">Subject</label>
                    <textarea id="subject" name="subject" placeholder="Write something.." style="height:170px"><?php echo htmlspecialchars($_POST['subject'] ?? ''); ?></textarea>
                    <input type="submit" value="Submit" name="submit">
                </form>
            </div>
        </div>
        <footer class="footer">ROBOAI Blog 2026 | <a href="home.php">Home</a> | <a href="about_us.html">About Us</a></footer>
    </body>
</html>
