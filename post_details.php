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
    "SELECT title, author_name, published_on, media, content
    FROM posts 
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

//showing it
// echo "<h1>" . htmlspecialchars($post['title']) . "</h1>";

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
    <body>

        <header>
            <div class="title-area">
                <p class="site-title">
                    <!-- <a href="#" class="logo">ROBOAI</a> -->
                    <a href="#"><img class="log" src="images/logo.index.png" alt="Roboai Logo"></a>
                </p>
            </div>
             <!--logo -->
             <div>
                <nav class="topnav">
                    <a href="home.html">Home</a>
                    <a href="post_categories.php">Post/Categories</a>
                    <a href="about_us.html">About Us</a>
                    <a href="contact.html">Contact Us</a>
                </nav>
             </div>
        </header>

        <h1><?php echo $title; ?></h1>
        <p>By <?php echo $author; ?> on <?php echo $date; ?></p>

        <!-- the image in the post -->
        <?php if (!empty($media)) { ?>
            <img src="<?php echo $media; ?>" alt="<?php echo $title; ?>" style="max-width: 100%;">
        <?php } ?>

        <div><?php echo $content; ?></div>

        <p><a href="post_categories.php">&larr; Back to all posts</a></p> 




        <div id="comment-form">
            <section>
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
                        <div class="group-form">
                            <label for="name">Name:</label>
                            <input type="text" id="name" name="name" placeholder="Your Name" required maxlength="30">
                        </div>
                        <!--Email Inpuut-->
                        <div class="group-form">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" placeholder="your.email@123.example.com" required>
                        </div>
                        <!--comment textarea-->
                        <div class="group-form">
                            <label for="comment">Comment:</label>
                            <textarea id="comment" name="comment" placeholder="Write your comment here..." required aria-label="write a comment" maxlength="1000"></textarea>
                            <input type="hidden" name="post_id" value="<?php echo $id; ?>">
                        </div>
                            <button type="submit" style="background-color: lightslategray; color: black;">Post Comment</button>
                        
                            <!-- This form sits inside the comment block, hidden until "Reply" is clicked -->
                            <!-- <input type="text" placeholder="Reply to this comment..." required>
                            <button type="submit">Reply</button> -->
                        <!-- <footer> -->
                            <!-- Inside the comment footer actions -->
                            <!-- <button class="action-btn delete-btn" aria-label="Delete comment">
                                Delete
                            </button> -->
                            <!-- Active states are usually handled by adding a CSS class via JavaScript -->
                            <!-- <button class="action-btn like-btn liked" aria-label="Unlike comment">
                                <span class="count">13 likes</span>
                            </button>
                        </footer> -->
                    </form>

                    <h3>Comments</h3>

                        <?php

                        require 'connect.php';
                        $c_stmt = mysqli_prepare($conn, 
                            "SELECT Name, Date_Time, Message
                            FROM comments 
                            WHERE post_id = ? 
                            ORDER BY date_time DESC"); // the query for the data

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