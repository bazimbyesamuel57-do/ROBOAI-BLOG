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
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

        
        <link rel="stylesheet" href="style.css">
    </head>

    <body>
        <!-- <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <h1>This is a protected dashboard page.</h1>
        <a href="logout.php">Log Out</a> -->
        <header>
            <div class="log-content">
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
                    <a href="about_us.html">About Us</a>
                    <a href="contact.php">Contact Us</a>
                </nav>
            </div>
            
        </header>
        <div>
            <form class="hsearchtab" action="post_categories.php" method="GET"> <!--search.php ods the filename where the files are picked and method use GET for search -->
                <input class="hsearch" type="text" name="q" placeholder="search post..." style="width:400px;" required> <!--name='q' where q is the query sent-->
                <button class="hsearch-button" type="submit">Search</button>
            </form>
        </div>
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
                <div class="container" style="max-width:900px">

                    <div style=" align-content: center;">
                        <!-- welcome placeholder if no posts -->
                        <?php if ($slideCount === 0): ?>

                            <div class="slideshow-container">
                                <div class="slides fade">
                                    <div class="number">1/1</div>
                                    <div style="text-align:center; padding:60px; background:#f0f0f0;">
                                        <h2>Welcome to ROBOAI Blog</h2>
                                        <p>No posts yet, check back soon!</p>
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
                                            <img src="<?= htmlspecialchars($slide['media']) ?>" alt="<?= htmlspecialchars($slide['title']) ?>" style="width: 100%; height:350px; object-fit:cover;">
                                        </a>
                                        <div class="text" style="bottom: 30px;">
                                            <?=  htmlspecialchars($slide['title']) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                
                                    <a class="prev" onclick="plusSlides(-1)">&#10094;</a>

                                    <a class="next" onclick="plusSlides(1)">&#10095;</a>

                                <!-- dots per slide generated automatically -->
                                <div style="text-align:center">
                                    <?php for ($d = 1; $d <= $slideCount; $d++): ?>
                                        <span class="dot" onclick="currentslide(<?= $d ?>)"></span>
                                    <?php endfor; ?>
                                </div>
                            </div> 
                        <?php endif; ?>
                            
                    

                    <!-- Post cards (appear when there is more than 4 posts-->
                    <?php if ($totalPosts > 4 && $cardCount > 0): ?>

                        <section class="mt-4">
                            <div class="row row-cols-3 g-2">
                            <?php foreach ($cards as $card): ?>
                                <div class="col" style="min-width:0;">
                                    <article class="card h-100">
                                        <div>
                                        <?php if (!empty($card['media'])): ?>
                                            <img class="card-img-top" 
                                            src="<?= htmlspecialchars($card['media']) ?>" alt="<?= htmlspecialchars($card['title']) ?>" 
                                            style=" object-fit: cover; height:160px;">

                                        <?php else: ?>
                                            <div class="d-flex align-items-center justify-content-center bg-light" style="height: 160px;">
                                                No image
                                            </div>
                                        <?php endif; ?>
                                        </div>

                                        <div class="card-body">
                                            <span class="post-author">
                                                By <?=  htmlspecialchars($card['author_name'] ?? '') ?> | <?= date('d M Y', strtotime($card['published_on'])) ?>
                                            </span><br>

                                            <h3 class="post-title h5 mt-2">
                                                <?= htmlspecialchars($card['title']) ?>
                                            </h3>

                                            <p class="excerpt card-text">
                                                <?=  htmlspecialchars($card['excerpt'] ?? '') ?>
                                            </p>
                                            
                                            <a class="readme" href="post_details.php?id=<?= (int)$card['post_id'] ?>">
                                                Read more
                                            </a>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        </section>
                    <?php endif; ?>
                </div>
                      <!-- see more link, takes you to the posts_categories list -->
                            <div style="text-align:center; margin-top:20px;">
                                <a href="post_categories.php" class="see-more-btn">
                                    See More Posts &rarr;
                                </a>
                            </div>
                    
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
            //auto slide every 2 seconds
            setInterval(() => plusSlides(1), 2000);
        </script>
    </body>
</html>
