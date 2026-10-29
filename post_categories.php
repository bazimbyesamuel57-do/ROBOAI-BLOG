
 <?php
require 'connect.php';

// Read GET params
$page = max(1, (int) ($_GET['page'] ?? 1));
$categoryFilter = (int)($_GET['category'] ?? 0);
$search =trim($_GET['q'] ?? '');
$perPage = 10;

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
        <link href="style.css" rel="stylesheet">
        
         
        
    </head>
    <body>

        <header>
            <div class="title-area">
                <p class="site-title">
                    <!-- <a href="#" class="logo">ROBOAI</a> -->
                    <a href="home.php"><img class="log" src="images/logo.index.png" alt="Roboai Logo"></a>
                </p>
            </div>
        
            <div>
                <nav class="topnav">
                    <a href="home.php">Home</a>
                    <a class="active" href="post_categories.php">Post/Categories</a>
                    <a href="about_us.html">About Us</a>
                    <a href="contact.html">Contact Us</a>
                </nav>
            </div>
            <h1 style="text-align: center;">Posts-Categories</h1>
        </header>
        <div class="search" style="margin-left:80px; margin-right:80px; padding: 100px; width: 50%;">
            <!-- search bar with actegory filter -->
             <form class="search" method="GET" action="post_categories.php">
                <input type="text" name="q" placeholder="search posts..." value="<?= htmlspecialchars($search) ?>" style="width: 50%;">
                <button class="searchtab-button" type="submit" >Search</button>
                <select  name="category" style="width:30%;">
                    <option value="0" <?=  $categoryFilter === 0 ? 'selected' : '' ?>>All</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['category_id'] ?>" <?= $categoryFilter === (int)$cat['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="page" value="1">
                <!-- <button class="searchtab button" type="submit">Search</button> -->

             </form>
        </div>

        <diV>
            <!-- <form class="searchtab" action="search.php" method="GET"> search.php ods the filename where the files are picked and method use GET for search -->
                <!-- <input type="text" name="q" placeholder="search post..."> name='q' where q is the query sent -->
                <!-- <button type="submit">Search</button>
            </form> -->

            <!-- posts card lists -->
            <div class="posts22">
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
                    <?php foreach ($posts as $row):
                        $id = $row['post_id'];
                        $title = htmlspecialchars($row['title']);
                        $excerpt = htmlspecialchars($row['excerpt'] ?? '');
                        $author_name = htmlspecialchars($row['author_name'] ?? '');
                        $published_on =date('d M Y', strtotime($row['published_on']));
                        $category = htmlspecialchars($row['category_name'] ?? '');
                        $media = $row['media'];
                    
                    ?>

                        <div>
                            <?php if (!empty($media)): ?>
                                <img src="images/<?= htmlspecialchars($media) ?>"
                                    alt="<?= $title ?>" style="max-width:100%; float:left;
                                    margin-right:15px;">
                            <?php endif; ?>

                            <p>
                                <a href="post_details.php?id=<?= (int)$id ?>">
                                    <?= $title ?>
                                </a>
                            </p>
                            <p><?= $excerpt ?></p>
                            <span>
                                By <?= $author_name ?>
                                | <?= $published_on ?>
                                <?= $category !== '' ? ' | <em>' . $category . '</em>' : '' ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>                        
            </div>
             
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