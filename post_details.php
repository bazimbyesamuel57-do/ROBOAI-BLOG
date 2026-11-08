<?php
// print_r($_GET);
// print_r($_POST);
// exit;

require 'connect.php';
// if id is sent
if (!isset($_GET['id'])) {
    die("No post selected.");
}
$id = (int) $_GET['id'];

//requsting for the post from the database
$stmt = mysqli_prepare($conn,
// "SELECT post_id FROM posts WHERE post_id = ? AND status = 'published'");
    "SELECT p.title, p.author_name, p.published_on, p.media, p.content, a.author_image, a.author_bio
    FROM posts p
    LEFT JOIN author a ON p.author_id = a.author_id 
    WHERE post_id = ? AND status = 'published'");
if (!$stmt) {
    die("prepare failed: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

//getting the row
$result = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($result);

if (!$post) {
    die("Post not found.");
}

// escape the text before printing it
$title =htmlspecialchars($post['title']);
$author = htmlspecialchars($post['author_name']);
$date = htmlspecialchars($post['published_on']);
$media = htmlspecialchars($post['media']);
$content = nl2br(htmlspecialchars($post['content']));
$author_bio =htmlspecialchars($post['author_bio'] ?? '');
$author_image = htmlspecialchars($post['author_image'] ?? '');

//showing it
// echo "<h1>" . htmlspecialchars($post['title']) . "</h1>";

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
</head>
    <body>

        <header>
            <div class="title-area">
                <p class="site-title">
                    <!-- <a href="#" class="logo">ROBOAI</a> -->
                    <a href="home.php"><img class="log" src="images/logo.index.png" alt="Roboai Logo"></a>
                </p>
            </div>
             <!--logo -->
             <div>
                <nav class="topnav">
                    <a href="home.php">Home</a>
                    <a href="post_categories.php">Post/Categories</a>
                    <a href="about_us.html">About Us</a>
                    <a href="contact.php">Contact Us</a>
                </nav>
             </div>
        </header>
        <div class="container" style="max-width: 900px;">
            <h1><?php echo $title; ?></h1>
            <p>By <?php echo $author; ?> on <?php echo $date; ?></p>

            <!-- the image in the post -->
            <?php if (!empty($media)) { ?>
                <img src="<?php echo $media; ?>" alt="<?php echo $title; ?>" style="max-width: 100%;">
            <?php } ?>

            <div><?php echo $content; ?></div>

            <!-- author bio  -->
            <div class ="author-box">
                <?php if (!empty($author_image)) { ?>
                    <img src="<?php echo $author_image; ?>" alt="<?php echo $author; ?>"
                    style="width:80px; height:80px; border-radius:50%; object-fit:cover;">
                <?php } ?>
                <p><strong>About<?php echo $author; ?></strong></p>
                <?php if (!empty($author_bio)) { ?>
                    <p><?php echo $author_bio; ?></p>
                <?php } ?>
            </div>

            <p><a href="post_categories.php">&larr; Back to all posts</a></p> 
        </div>



        <div id="comment-form">
            <section>
                <div class="container">
                    <h3>Leave a Comment</h3>

                    <?php 
                    // post messages
                    $messages =[
                        'empty' => 'Please fill in your name and comment.',
                        'name_long' => 'Name must be 30 characters or less.',
                        'comment_long' => 'Name must be 1000 characters or less.',
                        'bad_email' => 'Please enter a valid email address.',
                    ];

                    if ((isset($_GET['error'])) && isset($messages[$_GET['error']])) {
                        echo "<p style='color: red;'>" . $messages[$_GET['error']] . "</p>";
                    }
                    ?>

                <!-- comment form to add new comments -->
                    <div>
                    
                        <form method="POST" action="script.php">
                            <!--Name input-->
                            <div class="mb-3">
                                <label class="form-label" for="name">Name:</label>
                                <input type="text" id="name" name="name" placeholder="Your Name" required maxlength="30">
                            </div>
                            <!--Email Inpuut-->
                            <div class="group-form">
                                <label class="form-label" for="email">Email:</label>
                                <input class="control-form" type="email" id="email" name="email" placeholder="your.email@123.example.com" required>
                            </div>
                            <!--comment textarea-->
                            <div class="group-form">
                                <label class="form-label" for="comment">Comment:</label>
                                <textarea class="control-form"  rows="3" id="comment" name="comment" placeholder="Write your comment here..." required aria-label="write a comment"></textarea>
                                <input type="hidden" name="post_id" value="<?php echo $id; ?>">
                            </div>
                                <button type="submit" class="btn mb-3"style="background-color: lightslategray; color: black; margin-top: 30px;">Post Comment</button>
                            
                        </form>
                    </div>
                
                    <h3>Comments</h3>

                        <?php
                        // displaying comments
                        // require 'connect.php';
                        $c_stmt = mysqli_prepare($conn, 
                            "SELECT Name, Date_Time, Message
                            FROM comments 
                            WHERE post_id = ? 
                            ORDER BY date_time DESC
                            LIMIT 8"); // the query for the data

                        if (!$c_stmt) {
                            die("Comment query failed:". mysqli_error($conn));
                        }
                        mysqli_stmt_bind_param($c_stmt,"i", $id);
                        mysqli_stmt_execute($c_stmt);
                        $c_result = mysqli_stmt_get_result($c_stmt);

                        // $result = mysqli_query($conn, $sql);

                        // if (!$result) {
                        //     die("Query failed: " . mysqli_error($conn));
                        // }

                        while ($row = mysqli_fetch_assoc($c_result)) {
                            $c_name = htmlspecialchars($row["Name"]);
                            $c_date_time = $row["Date_Time"];
                            $c_comment = htmlspecialchars($row["Message"]);
                            // $comment = $row["Message"];
                            echo "<p><strong>$c_name</strong> ($c_date_time)<br>$c_comment</p>";
                            // echo $row["Name"] . ":" . $row["Date_Time"] . $row["Message"] . "<br>";
                            // the mysqli_fetch_assoc() outputs one row per loop turn as an array. when no rows left it returens false 
                            //and the while stops


                        }


                        ?>
                </div>
                    
            </section>
        </div>
        <div>
            <footer class="footer">ROBOAI Blog 2026 | Home | About | Contact |</footer>
        </div>
    </body>
</html>