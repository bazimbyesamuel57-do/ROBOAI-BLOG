
 <!-- <?php include('posts.php'); ?> -->
 <?php
require 'connect.php';
// $sql = "SELECT post_id, published_on, author_name, category_id, title, media, content FROM posts WHERE status = 'published' ORDER BY published_on DESC";
$sql = "SELECT * FROM posts WHERE status = 'published' ORDER BY published_on DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// while ($row = mysqli_fetch_assoc($result)) {
//     $id = $row["post_id"];
//     $published_on = htmlspecialchars($row["published_on"]);
//     $author_name = htmlspecialchars($row["author_name"]);
//     $category_id = $row["category_id"];
//     $title = htmlspecialchars($row["title"]);
//     $excerpt = htmlspecialchars($row["excerpt"]);
//     $media = $row["media"];
    // $content = htmlspecialchars($row["content"]);

    // echo "<p><a href ='post_details.php?id=$id'>$title</a></p>";
    // echo "{$row['title']} <br>";
    // echo "<img src = 'image/{$row['media']}'><br>";
    // echo "{$row['excerpt']} <br>";
    // echo "{$row['published_on']} <br><hr>";
    
// }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>posts</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="style.css" rel="stylesheet">
        
         
        
    </head>
    <body>

        <header>
            <div class="title-area">
                <p class="site-title">
                    <!-- <a href="#" class="logo">ROBOAI</a> -->
                    <a href="#"><img class="log" src="images/logo.index.png" alt="Roboai Logo"></a>
                </p>
            </div>
        
            <div>
                <nav class="topnav">
                    <a href="home.html">Home</a>
                    <a class="active" href="post_categories.php">Post/Categories</a>
                    <a href="about_us.html">About Us</a>
                    <a href="contact.html">Contact Us</a>
                </nav>
            </div>
            <h1 style="text-align: center;">Posts-Categories</h1>
        </header>

        <diV>
            <!-- <form class="searchtab" action="search.php" method="GET"> search.php ods the filename where the files are picked and method use GET for search -->
                <!-- <input type="text" name="q" placeholder="search post..."> name='q' where q is the query sent -->
                <!-- <button type="submit">Search</button>
            </form> -->
            <?php 
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row["post_id"];
                $published_on = htmlspecialchars($row["published_on"]);
                $author_name = htmlspecialchars($row["author_name"]);
                $category_id = $row["category_id"];
                $title = htmlspecialchars($row["title"]);
                $excerpt = htmlspecialchars($row["excerpt"]);
                $media = $row["media"];
            
            echo "<p><a href ='post_details.php?id=$id'>$title</a></p>";
            // echo "{$row['title']} <br>";
            echo "<img src = 'image/{$row['media']}'><br>";
            echo "{$row['excerpt']} <br>";
            echo "{$row['published_on']} <br><hr>";
            }
            ?>
        </diV>

        
        <div>
            <footer class="footer">ROBOAI Blog 2026 | Home | About | Contact |</footer>
        </div>
    
    </body>
</html>