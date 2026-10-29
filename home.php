<?php
require 'connect.php';

//total number of published posts
$totalResult = mysqli_query($conn,
"SELECT COUNT(*) AS total FROM posts WHERE status='published'");

if (!$totalResult) {
    die("Count query failed: " . mysqli_error($conn));
}
$totalPosts = (int) mysqli_fetch_assoc($totalResult)['total'];


//Silde show section(4 posts)
// posts withonly images show up, skipping the those with no images
$slidesql = "SELECT post_id, title, media
            FROM posts
            WHERE status = 'published' AND media <> ''
            ORDER BY published_on DESC
            LIMIT 4";

$slideResult = mysqli_query($conn, $slidesql);
if (!$slideResult) {
    die ("Slide query failed: " . mysqli_error($conn));
}
$slides = mysqli_fetch_all($slideResult, MYSQLI_ASSOC);
$slideCount =count($slides);


// card(3)
//only fetched when there are more than 4 leaving the 4 in the slides and show the next 3 posts 
$cards = [];
$cardCount = 0;

if ($totalPosts > 4) {
    $cardsql = "SELECT post_id, title, media, excerpt, author_name, published_on
                FROM posts
                WHERE status = 'published'
                ORDER BY published_on DESC
                LIMIT 3 OFFSET 4";

    $cardResult = mysqli_query ($conn, $cardsql);
    if (!$cardResult) {
        die("Card query failed; " . mysqli_error($conn));
    }
    $cards = mysqli_fetch_all($cardResult, MYSQLI_ASSOC);
    $cardCount = count($cards); // number of of post cards(1, 2, or 3 )
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Home</title>
        <link rel="stylesheet" href="style.css">
    </head>

    <body>
        <!-- <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <h1>This is a protected dashboard page.</h1>
        <a href="logout.php">Log Out</a> -->
        <header>
            <div class="title-area">
                <p class="site-title">
                    <!-- <a href="#" class="logo">ROBOAI</a> -->
                    <a href="#"><img class="log" src="images/logo.index.png" alt="Roboai Logo"></a>
                </p>
            </div>
            
            <!--logo -->
            <div class="topnav">
                <nav>
                    <a class="active" href="#">Home</a>
                    
                    <a href='post_categories.php'>Post/Categories</a>
                    <div class="dropdown">
                        <button class="dropbtn">News&Trends 
                            <i class="fa fa-caret-down"></i>
                        </button>
                        <div class="dropdown-content">
                            <a href="#">News in the Tech world</a>
                            <!-- <a href="#">link2</a> -->
                        </div>
                    </div>

                    <div class="dropdown">
                        <button class="dropbtn">Resources 
                            <i class="fa fa-caret-down"></i>
                        </button>
                        <div class="dropdown-content">
                            <a href="#">AI trends</a>
                            <a href="#">Robotics trends</a>
                        </div>
                    </div>
                
                
                    
                        <!-- <div class="dropdown">
                            <button class="dropbtn"><a href="#">Resources</a></button>
                            <div class="dropdown-content">
                                <a href="#">link1</a>
                                <a href="#">link2</a>
                            </div>
                        </div> -->

                    <a href="about_us.html">About Us</a>
                    <a href="contact.html">Contact Us</a>
                </nav>
            </div>
            
        </header>
        
        <form class="searchtab" action="search.php" method="GET"> <!--search.php ods the filename where the files are picked and method use GET for search -->
            <input type="text" name="q" placeholder="search post..."> <!--name='q' where q is the query sent-->
            <button type="submit">Search</button>
        </form>
        <!-- <div id="sidenav" class="sidenav"> hover side bar -->
            <!-- <a href="#" id="home">Home</a>
            <a href="post_categories.html" id="categories">Categories</a>
            <a href="#" id="posts">Posts</a>
        </div> -->
        <!-- <div class="Menubar">
            <a href="#home">Menu</a>
            <a href="#news">Categories</a>
            <a href="#Posts">Posts</a>
            <div class="dropdown">
                <button class="dropbtn">Menu
                <i class="fa fa-caret-down"></i>
                </button>
                <div class="dropdown-content">
                    <a href="#">Home</a>
                    <a href="#">Categories</a>
                    <a href="#">Posts</a>
                </div>
            </div>
        </div> -->
        <main>
            <div>
                <div>
                    <section id="post-info">
                        <h1>ROBOTICS AND AI TRENDS.</h1>
                        <h2>Welcome!</h2>
                        <p>This blog shares different learning experience, research and news to new trends regarding AI and Robotics. You can find different ideas.</p>
                        <p>It also shares quality information on how AI and Robotics has affected different sectors like health, environment and economy of countries</p>
                    </section>
                </div>

                <!-- slide show -->

                <div>
                    <!-- welcome placeholder if no posts -->
                    <?php if ($slideCount === 0): ?>

                        <div class="slideshow-container">
                            <div class="slides fade">
                                <div class="number">1/1</div>
                                <div style="text-align:center; padding:600px; background:#f0f0f0;">
                                    <h2>Welcome to ROBOAI Blog</h2>
                                    <p>No posts yet -check back soon!</p>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                            <div class="slideshow-container">
                                
                                <?php foreach ($slides as $i => $slide): ?>
                                    <div class="slides fade">
                                        <div class="number">
                                            <?= $i + 1 ?>/<?= $slideCount ?>
                                        </div>
                                        <a href="post_details.php?id=<?= (int)$slide['post_id'] ?>">
                                            <img src="images/<?= htmlspecialchars($slide['media']) ?>" alt="<?= htmlspecialchars($slide['title']) ?>" style="max-width: 100%;">
                                        </a>
                                        <div class="text">
                                            <?=  htmlspecialchars($slide['title']) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <a class="prev" onclick="plusSlides(-1)">&#10094;</a>

                                <a class="next" onclick="plusSlides(1)">&#10095;</a>
                            </div><br>

                            <!-- dots per slide generated automatically -->
                            <div style="text-align:center">
                                <?php for ($d = 1; $d <= $slideCount; $d++): ?>
                                    <span class="dot" onclick="currentslide(<?= $d ?>)"></span>
                                <?php endfor; ?>
                            </div>

                    <?php endif; ?>


                    <!-- Post cards (appear when there is more than 4 posts-->
                    <?php if ($totalPosts > 4 && $cardCount > 0): ?>

                        <section style="margin-top:50px;">
                            <?php foreach ($cards as $card): ?>
                                <div class="home-arcticle">
                                    <article class="post-card">

                                        <h3 class="post-title">
                                            <?= htmlspecialchars($card['title']) ?>
                                        </h3>

                                        <?php if (!empty($card['media'])): ?>
                                            <img class="img" 
                                            src="images/<?= htmlspecialchars($card['media']) ?>" alt="<?= htmlspecialchars($card['title']) ?>" 
                                            style="padding:50px; max-width:100%;">

                                        <?php else: ?>
                                            <div style="width:30%; padding:20px;  background:#eee; display:inline-block; text-align:center;">
                                                No image
                                            </div>
                                        <?php endif; ?>

                                        <p class="excerpt">
                                            <?=  htmlspecialchars($card['excerpt'] ?? '') ?>
                                        </p>
                                        <span class="post-author">
                                            By <?=  htmlspecialchars($card['author_name'] ?? '') ?>|<?= date('d M Y', strtotime($card['published_on'])) ?>
                                        </span>
                                        <a class="readme" href="post_details.php?id=<?= (int)$card['post_id'] ?>">
                                            Read more
                                        </a>
                                
                                    </article>
                                </div>
                            <?php endforeach; ?>
                            
                            
                            <!-- see more link, takes you to the posts_categories list -->
                            <div style="text-align:center; margin-top:20px;">
                                <a href="post_categories.php" class="see-more-btn">
                                    See More Posts &rarr;
                                </a>
                            </div>

                        </section>
                    <?php endif; ?>
                    
                </div>
                <br>

        </main>


        <footer class="footer">ROBOAI Blog 2026 | Home | <a href="about_us.html">About</a> | <a href="contact.html">Contact</a> |</footer>

        <script>
            let slideIndex = 1;
            showSlides(slideIndex);

            function plusSlides(n) {
                showSlides(slideIndex += n);
            }

            function currentslide(n) {
                showSlides(slideIndex = n);
            }

            function showSlides(n) {
                const slides = document.getElementsByClassName("slides");
                const dots   = document.getElementsByClassName("dot");

                if (slides.length === 0) return;

                if (n > slides.length) slideIndex = 1;
                if (n < 1)             slideIndex = slides.length;

                for (let i = 0; i < slides.length; i++)
                    slides[i].style.display = "none";

                for (let i = 0; i < dots.length; i++)
                    dots[i].className = dots[i].className.replace(" active", "");

                slides[slideIndex - 1].style.display = "block";
                if (dots.length > 0)
                    dots[slideIndex - 1].className += " active";
 
            }
            //auto slide every 3 seconds
            setInterval(() => plusSlides(1), 3000);
        </script>
    </body>
</html>
