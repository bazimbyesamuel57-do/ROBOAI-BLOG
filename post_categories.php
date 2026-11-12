
 <?php
require 'connect.php';

// Read GET params
$page = max(1, (int) ($_GET['page'] ?? 1));
$categoryFilter = (int)($_GET['category'] ?? 0);
$search =trim($_GET['q'] ?? '');
$perPage = 12;

// fetch the categories froom the database for the dropdown
$catResult = mysqli_query($conn, 
                    "SELECT category_id, category_name FROM categories ORDER BY category_name ASC");
IF (!$catResult) {
    die("category query failed: " . mysqli_error($conn));
} $categories = mysqli_fetch_all($catResult, MYSQLI_ASSOC);


//  THE WHERE CLAUSE
$where = "WHERE p.status ='published'";

if ($categoryFilter > 0) {
    $where .= " AND p.category_id = " . (int)$categoryFilter;
}
if ($search !== '') {
    $safe = mysqli_real_escape_string($conn, $search);
    $where .= " AND (p.title LIKE '%$safe%' OR p.excerpt LIKE '%$safe%')";
}

//counting of total matching posts
$countResult = mysqli_query($conn,
                    "SELECT COUNT(*) AS total FROM posts p $where");
if (!$countResult) {
    die("count query failed: " . mysqli_error($conn));
}
$totalPosts = (int)mysqli_fetch_assoc($countResult) ['total']; 
$totalPages = $totalPosts > 0 ? (int)ceil($totalPosts / $perPage) : 1;
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

// fetching posts for the current page
$sql = "SELECT p.post_id, p.title, p.excerpt, p.author_name, p.published_on, c.category_name, p.media
        FROM posts p
        LEFT JOIN categories c ON p.category_id = c.category_id
        $where
        ORDER BY p.published_on DESC
        LIMIT $perPage OFFSET $offset";

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

//BUILD URL KEEPING AL FILTERS
function pageUrl(int $p, int $cat, string$q) {
    $params = ['page' => $p];
    if ($cat > 0)
        $params['category'] = $cat;
    if ($q !== '')
        $params['q'] = $q;
        return 'post_categories.php?' . http_build_query($params);
}


// $sql = "SELECT * FROM posts WHERE status = 'published' ORDER BY published_on DESC";
// $result = mysqli_query($conn, $sql);

// if (!$result) {
//     die("Query failed: " . mysqli_error($conn));
// }

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

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        
        <link href="style.css" rel="stylesheet">
        
         
        
    </head>
    <body>

        <header>
            <div class="blog-header">
                <div class="logo">
                    <!-- <a href="#" class="logo">ROBOAI</a> -->
                <a href="#"><img style="height: 50px; width: auto;" src="/ROBOAI/images/logo2.index.png" alt="Roboai Logo"></a>
            </div>
            <!-- <div class="log-auth">
                <a class="log-auth-btn" href="admin_login.php">Logout</a>
            </div> -->
        </div>
        
            <div>
                <nav class="topnav">
                    <a href="home.php">Home</a>
                    <a class="active" href="post_categories.php">Post/Categories</a>
                    <a href="about_us.html">About Us</a>
                    <a href="contact.php">Contact Us</a>
                </nav>
            </div>
            <h1 style="text-align: center;">Posts-Categories</h1>
        </header>
        <div>
            <!-- search bar with actegory filter -->
             <form class="hsearchtab" method="GET" action="post_categories.php">
                <input class="hsearch" type="text" name="q" placeholder="search posts..." value="<?= htmlspecialchars($search) ?>" style="width: 400px;" required>
                <button class="hsearch-button" type="submit" >Search</button>

                <input type="hidden" name="category" value="<?= (int)$categoryFilter ?>">
                <input type="hidden" name="page" value="1">
            </form>
            <div class="cat-filter">
                <label>FIND BY CATEGORIES:</label>
                <div class="btn-group">
                    <button class="btn dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <?php
                        $current = 'All';
                        foreach ($categories as $cat) {
                            if ((int)$cat['category_id'] === $categoryFilter) {
                                $current = $cat['category_name'];
                            }
                        }
                        echo htmlspecialchars($current);
                        ?>
                    </button>

                    <ul class="dropdown-menu" style="max-height:250px; overflow-y:auto;">
                        <li>
                            <a class="dropdown-item <?= $categoryFilter === 0 ? 'active' : '' ?>"
                            href="<?= pageUrl(1, 0, $search) ?>">All</a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a class="dropdown-item <?= $categoryFilter === (int)$cat['category_id'] ? 'active' : '' ?>"
                                href="<?= pageUrl(1, (int)$cat['category_id'], $search) ?>">
                                    <?= htmlspecialchars($cat['category_name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <diV>
            <!-- <form class="searchtab" action="search.php" method="GET"> search.php ods the filename where the files are picked and method use GET for search -->
                <!-- <input type="text" name="q" placeholder="search post..."> name='q' where q is the query sent -->
                <!-- <button type="submit">Search</button>
            </form> -->

            <!-- posts card lists -->
            <div class="container">
                <?php if  (empty($posts)): ?>
                    <p style="text-align:center; margin-top:40px";>
                        No posts found
                        <?= $search !== '' ? ' for <strong>' . htmlspecialchars($search) . '</strong>' : '' ?>
                        <?php if ($categoryFilter > 0):
                            foreach ($categories as $cat) {
                                if((int)$cat['category_id'] === $categoryFilter) {
                                    echo "in <strong>" . htmlspecialchars($cat['category_name']) . '</strong>';
                                    break;
                                }
                            }
                        endif; ?>.
                    </p>
                
                <?php else: ?>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
                        <?php foreach ($posts as $row):
                            $id = $row['post_id'];
                            $title = htmlspecialchars($row['title']);
                            $excerpt = htmlspecialchars($row['excerpt'] ?? '');
                            $author_name = htmlspecialchars($row['author_name'] ?? '');
                            $published_on =date('d M Y', strtotime($row['published_on']));
                            $category = htmlspecialchars($row['category_name'] ?? '');
                            $media = $row['media'];
                        
                        ?>
                    
                        <div class="col">

                            <div class="card h-100">
                                    <?php if (!empty($media)): ?>
                                        <div class="img-zoom">
                                            <a href="post_details.php?id=<?= (int)$id ?>">
                                                <img class="card-img-top" style="height:200px; object-fit:cover;" src="<?= htmlspecialchars($media) ?>"
                                                    alt="<?= $title ?>">
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex align-items-center; justify-content bg-light h-100 p-4">
                                            <p>No image</p>
                                        </div>
                                    <?php endif; ?>

                                <div class="card-body">
                                    <p class="card-title">
                                        <a href="post_details.php?id=<?= (int)$id ?>">
                                            <?= $title ?>
                                        </a>
                                    </p>
                                    <p class="card-text"><?= $excerpt ?></p>
                                    <span>
                                        By <?= $author_name ?>
                                        | <?= $published_on ?>
                                        <?= $category !== '' ? ' | <em>' . $category . '</em>' : '' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                                        
                        <?php endforeach; ?>
                    </div>
                    
                <?php endif; ?>                        
         
             
            <!-- the previous and next pagination -->
            <?php if ($totalPages > 1): ?>
                <div>
                    <?php if ($page > 1): ?> 
                        <a href="<?= pageUrl($page - 1, $categoryFilter, $search) ?>">
                            &laquo; Previous
                        </a>
                    <?php endif; ?>

                    <span style="margin:0 15px;">
                        Page <?= $page ?> of <?= $totalPages ?>&nbsp;(<?=  $totalPosts ?> posts)
                    </span>

                    <?php if ($page < $totalPages): ?>
                        <a href="<?= pageUrl($page + 1, $categoryFilter, $search) ?>">
                            Next &raquo;
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </diV>

        
        <div>
            <footer class="footer">ROBOAI Blog 2026 | Home | About | Contact |</footer>
        </div>
    
    </body>
</html>