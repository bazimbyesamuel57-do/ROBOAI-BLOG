<?php
// require 'connect.php';

$host = "localhost";
$user = "root";
$password = "";
$database = "roboai_blog_db";
$port = 3307;

$conn =mysqli_connect($host, $user, $password, $database, $port);

if (!$conn ) {
    die("Connection failed: " . mysqli_connect_error());

}

// echo "Connected!";

//total number of published posts
$totalResult = mysqli_query($conn,
"SELECT COUNT(*) AS total FROM posts WHERE status='published'");

if (!$totalResult) {
    die("Count query failed: " . mysqli_error($conn));
}
$totalPosts = (int) mysqli_fetch_assoc($totalResult)['total'];


//Silde show section(4 posts)
// posts withonly images show up, skipping the those with no images
// $slidesql = "SELECT post_id, title, media
//             FROM posts
//             WHERE status = 'published' AND media <> ''
//             ORDER BY published_on DESC
//             LIMIT 4";

// $slideResult = mysqli_query($conn, $slidesql);
// if (!$slideResult) {
//     die ("Slide query failed: " . mysqli_error($conn));
// }
// $slides = mysqli_fetch_all($slideResult, MYSQLI_ASSOC);
// $slideCount =count($slides);


// card(3)
//only fetched when there are more than 4 leaving the 4 in the slides and show the next 3 posts 
$cards = [];
$cardCount = 0;

// if ($totalPosts > 0) {
    // $cardsql = "SELECT post_id, title, media, excerpt, author_name, published_on
    //             FROM posts
    //             WHERE status = 'published'
    //             ORDER BY published_on DESC
    //             LIMIT 3 OFFSET 4";


$cardsql = "SELECT post_id, title, media, excerpt, author_name, published_on
            FROM posts
            -- WHERE status = 'published' AND media <> ''
            ORDER BY published_on DESC
            LIMIT 4";

    $cardResult = mysqli_query ($conn, $cardsql);
    if (!$cardResult) {
        die("Card query failed; " . mysqli_error($conn));
    }
    $cards = mysqli_fetch_all($cardResult, MYSQLI_ASSOC);
    $cardCount = count($cards); // number of of post cards(1, 2, or 3 )
// }



// calendar
$calResult = mysqli_query($conn, "SELECT post_id, title, status, DATE(published_on) AS day FROM posts ORDER BY published_on");
if (!$calResult) {
    die("calendar query; " . mysqli_error($conn));
}

$posts_by_day = [];
While ($row = mysqli_fetch_assoc($calResult)) {
    $posts_by_day[$row['day']] [] = [
        'post_id' => $row['post_id'],
        'title' => $row['title'],
        'status' => $row['status']
    ];
    // echo $row['title'] . ' was posted on ' . $row['day'] . '<br>';
}
// print_r($posts_by_day); echo '</prev>'; 
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin Dash</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

        
        <link rel="stylesheet" href="admin.css">
    </head>

    <body>
        <!-- <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <h1>This is a protected dashboard page.</h1>
        <a href="logout.php">Log Out</a> -->

        <header class="blog-header">
            <div class="logo">
                <!-- <a href="#" class="logo">ROBOAI</a> -->
                <a href="#"><img style="height: 50px; width: auto;" src="/ROBOAI/images/logo2.index.png" alt="Roboai Logo"></a>
            </div>
            <div class="log-auth">
                <a class="log-auth-btn" href="admin_login.php">Logout</a>
            </div>
        </header>

            <div id="mySidenav" class="sidenav">
                <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="admin_posts.php">Manage Posts</a>
                <a href="create.php">Create Posts</a>
                <a href="admin_contactfeed.php">Contact Messages</a>
                <a href="admin_login.php">Logout</a>
            </div>
            
        <!-- <div> -->
            <!-- <form class="hsearchtab" action="admin_posts.php" method="GET"> search.php ods the filename where the files are picked and method use GET for search -->
                <!-- <input class="hsearch" type="text" name="q" placeholder="search post..." style="width:400px"> name='q' where q is the query sent -->
                <!-- <button class="hsearch-button" type="submit">Search</button>
            </form> -->
        <!-- </div> -->
        <main id="main">
                
            <div>
                <section id="post-info">
                    <h1>Admin Dashboard.</h1>
                    <h2> Welcome!</h2>
                </section>
            </div>
            <span onclick="openNav()">&#9776; MENU</span>


            <div class="calendar-container">
                <header class="cal-title">
                    <p id="cal-title"></p>
                    <div>
                        <button type="button" id="cal-prev"> &lt; </button>
                        <button type="button" id="cal-next"> &gt; </button>
                    </div>
                </header>

                <div class="cal-body">
                    <ul class="cal-days">
                        <li>Sun</li>
                        <li>Mon</li>
                        <li>Tue</li>
                        <li>Wed</li>
                        <li>Thur</li>
                        <li >Fri</li>
                        <li>Sat</li>
                    </ul>
                    <ul class="calender-dates" id="cal-grid"></ul>
                    <div id="cal-posts">
                        Click a day to see its posts.
                    </div>
                </div>
            </div>



            <div style=" align-content: center;">
                <!-- welcome placeholder if no posts -->
                <?php if ($cardCount === 0): ?>

                    <div class="slideshow-container">
                            <div style="text-align:center; padding:60px; background:#f0f0f0;">
                                <!-- <h2>Welcome to ROBOAI Blog</h2> -->
                                <p>No posts yet,<a href="create.php">Create Post</a></p>
                            </div>
                    </div>
                    
                
                       
                <?php elseif ($cardCount > 0): ?>

                    <section class="container my-4">
                        <?php foreach ($cards as $card): ?>
                            <div class="card mb-3" style="max-width: 800px;">
                                <article class="row g-0">
                                    <div class="col-md-3">
                                    <?php if (!empty($card['media'])): ?>
                                        <img class="img-fluid rounded-start w-100 h-100" 
                                            src="/ROBOAI/<?=htmlspecialchars($card['media']) ?>" alt="<?= htmlspecialchars($card['title']) ?>" 
                                            style=" object-fit: cover; max-height:250px;">

                                    <?php else: ?>
                                        <div class="d-flex align-items-center; justify-content bg-light h-100 p-4">
                                            <p>No image</p>
                                        </div>
                                    <?php endif; ?>
                                    </div>

                                    <div class="col-md-9">
                                        <div class="card-body">
                                            <span class="post-author card-text">
                                                By <?=  htmlspecialchars($card['author_name'] ?? '') ?> | <?= date('d M Y', strtotime($card['published_on'])) ?>
                                            </span><br>

                                            <h3 class="card-title h5 mt-2">
                                                <?= htmlspecialchars($card['title']) ?>
                                            </h3>

                                            <p class="excerpt card-text">
                                                <?=  htmlspecialchars($card['excerpt'] ?? '') ?>
                                            </p>
                                            
                                            <a class="readme" href="../post_details.php?id=<?= (int)$card['post_id'] ?>">
                                                Read more
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                        
                        
                        <!-- see more link, takes you to the posts_categories list -->
                        <div style="text-align:center; margin-top:20px;">
                            <a href="admin_posts.php" class="see-more-btn">
                                Manage Posts &rarr;
                            </a>
                        </div>

                    </section>
                <?php endif; ?>
                
            </div>
            <br>

        </main>


        <footer class="footer">ROBOAI Blog 2026 | Dashboard | <a href="admin_posts.php">Manage Posts</a> | <a href="admin_register.php">Register</a> | <a href="admin_login.php">LogOut</a> |</footer>

        <script>
            /*sidenave with opacity*/
            function openNav() {
                /* adding background and setting style. opening nav*/
                document.getElementById("mySidenav").style.width = "250px";
                document.getElementById("main").style.marginLeft = "250px";
                document.body.style.backgroundColor = "white";
            }
            function closeNav() {
                /*closinfg nave styling and background*/
                document.getElementById("mySidenav").style.width = "0";
                document.getElementById("main").style.marginLeft = "0";
                document.body.style.backgroundColor = "White";
            }
        </script>

        <script>
            const postsByDay = <?php echo json_encode($posts_by_day, JSON_HEX_TAG | JSON_HEX_AMP |JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
            console.log(postsByDay);

            const now = new Date();
            const todayKey = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');

            let viewYear = new Date().getFullYear();
            let viewMonth = new Date().getMonth();

            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

            function renderCalender() {
                const row = document.getElementById('cal-grid');
                row.innerHTML = ''; 

                document.getElementById('cal-title').textContent = monthNames[viewMonth] + ' ' + viewYear;


                const firstWeekday = new Date(viewYear, viewMonth, 1). getDay();
                for (let i = 0; i < firstWeekday; i++) {
                    const blank = document.createElement('li')
                    row.appendChild(blank);
                }
                const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
                for (let n = 1; n <= daysInMonth; n++) {
                    const dayCell = document.createElement('li');
                     dayCell.textContent = n;
                    row.appendChild(dayCell);

                    const monthText = String(viewMonth + 1).padStart(2, '0');
                    const dayText = String(n).padStart(2, '0');
                    const key = viewYear + '-' + monthText + '-' + dayText;

                    if (postsByDay[key]) {
                        dayCell.classList.add('has-posts');
                    }

                    if (key === todayKey) {
                        dayCell.classList.add('today');
                    }
                    dayCell.addEventListener('click', () => {
                    const panel = document.getElementById('cal-posts');
                    panel.innerHTML = '';
            
                    const dayposts = postsByDay[key] || [];

                    if (dayposts.length === 0) {
                        panel.textContent = 'No posts on this day.';
                        return;
                    }
                        const heading = document.createElement('p');
                         heading.textContent = 'Posts on ' + key;
                        panel.appendChild(heading);


                        dayposts.forEach(function (p) {
                        const line = document.createElement('p');
                        const link = document.createElement('a');
                        link.textContent = p.title;

                        link.href = '../post_details.php?id=' + Number(p.post_id);
                        line.appendChild(link);
                        panel.appendChild(line);
                        line.append( ' (' + p.status + ')' );

                    });
                });

                }
            }
            renderCalender();

            // let viewMonth = 0;
            // let viewYear = 1;
            // renderCalender();

            document.getElementById('cal-next').addEventListener('click', () => {
                viewMonth ++;
                if (viewMonth > 11) {
                    viewMonth = 0;
                    viewYear++;                      
                }
                renderCalender();                      
            });
            document.getElementById('cal-prev').addEventListener('click', () => {
                viewMonth --;
                if (viewMonth < 0) {
                    viewMonth = 11;
                    viewYear--;                   
                }
                renderCalender();
            });
            
        </script>
    </body>
</html>
