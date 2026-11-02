<?php
require 'connect.php';


// session_start();

// redirect back to the login page
// session_destroy(); 
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: admin_login.php");
// }
// else {
//     echo "Welcome: {$_SESSION['name']}";
    
// }

$per_page = 10;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;

// filters 
$f_title = trim($_GET['title'] ?? '');
$f_author_name = trim($_GET['author_name'] ?? '');
$f_category_id  = (int) ($_GET['category_id'] ?? 0);
$f_status = $_GET['status'] ?? '';
$f_published_on = trim($_GET['published_on'] ?? '');

// leaving values that are not valid
if($f_status !== 'draft' && $f_status !== 'published') {
    $f_staus = '';
}
$d =DateTime::createfromFormat('Y-m-d', $f_published_on);
if (!$d || $d->format('Y-m-d') !== $f_published_on) {
    $f_published_on = '';
}
// if ($f_status !== 'draft' && $f_status !== 'published') {
//     $f_status = '';
// }
// $d = DateTime::createFromFormat('Y-m-d', $f_published_on);
// if (!$d || $d->format('Y-m-d') !== $f_published_on) {
//     $f_published_on = '';
// }

// build the WHERE from the filters that are filled in
$where  = [];
$types  = '';
$params = [];

if ($f_title !== '') {
    $where[]  = 'p.title LIKE ?';
    $types   .= 's';
    $params[] = '%' . $f_title . '%';
}
if ($f_author_name !== '') {
    $where[]  = 'p.author_name LIKE ?';
    $types   .= 's';
    $params[] = '%' . $f_author_name . '%';
}
if ($f_category_id > 0) {
    $where[]  = 'p.category_id = ?';
    $types   .= 'i';
    $params[] = $f_category_id;
}
if ($f_status !== '') {
    $where[]  = 'p.status = ?';
    $types   .= 's';
    $params[] = $f_status;
}
if ($f_published_on !== '') {
    $where[]  = 'DATE(p.published_on) = ?';
    $types   .= 's';
    $params[] = $f_published_on;
}

$where_sql = $where ? 'WHERE '. implode(' AND ', $where) : '';

// filters that are active, used to keep them in the Prev/Next links
$filters = array_filter([
    'title'    => $f_title,
    'author_name'   => $f_author_name,
    'category_id' => $f_category_id,
    'status'   => $f_status,
    'published_on'     => $f_published_on,
], function ($v) { return $v !== '' && $v !== 0; });

function page_link(array $filters, int $page): string {
    return '?' . htmlspecialchars(http_build_query(array_merge($filters, ['page' => $page])));
}

// categories for the filter dropdown
$cat_result = mysqli_query($conn, "SELECT category_id, category_name FROM categories ORDER BY category_name");
if (!$cat_result) {
    die('Categories query failed: ' . mysqli_error($conn));
}
//number of rows existing in total
$count_stmt = mysqli_prepare($conn, 
    "SELECT COUNT(*) AS total FROM posts p $where_sql");
if (!$count_stmt) {
    die("Count failed: " . mysqli_error($conn));
}
if ($types !== '') {
    mysqli_stmt_bind_param($count_stmt, $types, ...$params);
}
mysqli_stmt_execute($count_stmt);
$total = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt))['total'];
$total_pages =(int) ceil($total / $per_page);

//one page of posts (left JOIN so that posts without a category still show)
$stmt = mysqli_prepare($conn,
        "SELECT p.post_id, p.title, p.author_name, p.published_on, p.status, c.category_name
        FROM posts p
        LEFT JOIN categories c ON p.category_id = c.category_id
        $where_sql
        ORDER BY p.published_on DESC, p.post_id DESC
        LIMIT ? OFFSET ?");
if (!$stmt) {
    die('Query failed: ' . mysqli_error($conn));
}

$page_params = array_merge($params, [$per_page, $offset]);
mysqli_stmt_bind_param($stmt, $types . 'ii', ...$page_params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add post</title>
        <!-- <link rel="stylesheet" href="style.css"> -->
        <link rel="stylesheet" href="admin.css">
    </head>
    <body>
        <button style="float:right;">
            <a href="admin_login.php">Logout</a>
        </button>


        <div id="mySidenav" class="sidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            <a href="#">Dashboard</a>
            <a href="#">Create Posts</a>
            <a href="#">Edit posts</a>
            <a href="#">View posts</a>
        </div>

        <span onclick="openNav()">&#10143;</span>
        

        <div id="main">
            <h1 style="text-align: center;">Welcome Admin</h1>

            <div>
                <h1 > Manage Posts</h1>
                <p><a href="create.php">+ Add a post</a></p>
            </div>

            <div>
                <?php
                if (isset($_GET['updated'])) {
                    echo '<p style="color:green;">Post updated.</p>';
                }
                if (isset($_GET['deleted'])) {
                    echo '<p style="color:green;">Post deleted.</p>';
                }
                if (isset($_GET['created'])) {
                    echo '<p stle="color:green;">Post created.</p>';
                }
                ?>

                <form method="GET" action="admin_posts.php">
                    <input type="text" name="title" placeholder="Title" 
                        value="<?php echo htmlspecialchars($f_title); ?>">

                    <input type="text" name="author_name" placeholder="author Name"
                        value="<?php echo htmlspecialchars($f_author_name); ?>">

                    <select name="category_id">
                        <option value="0"> All categories</option>
                        <?php while ($c = mysqli_fetch_assoc($cat_result)) { ?>
                            <option value="<?php echo $c['category_id']; ?>"
                            <?php if ($f_category_id === (int) $c['category_id']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($c['category_name']); ?>
                            </option>
                        <?php } ?>
                    </select>

                    <select name="status">
                        <option value="">All statuses</option>
                        <option value="draft"<?php if ($f_status === 'draft') echo ' selected'; ?>>Draft</option>
                        <option value="published"<?php if ($f_status === 'published') echo ' selected'; ?>>Published</option>
                    </select>

                    <input type="date" name="published_on" value="<?php echo htmlspecialchars($f_published_on); ?>">

                    <button type="submit">Filter</button>
                    <a href="admin_posts.php">Reset</a>

                </form>

                <?php if  ($total == 0) { ?>
                    <?php if ($filters) { ?>               
                        <p> There is no post match.<a href="admin_posts.php">Reset</a></p>
                    <?php } else { ?>
                        <p>No posts yet.</p>
                    <?php } ?>    
                <?php } elseif ($page > $total_pages) { ?>
                    <p>No more posts <a href="<?php echo page_link($filters, 1); ?>">Back to first page</a></p>
                <?php } else { ?>
                    <p><?php echo $total; ?> post(s) found</p>
            
                    <table border="1">
                        <tr>
                            <th>Title</th>
                            <th>Author Name</th>
                            <th>Published on</th>
                            <th>status</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                        
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['author_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['published_on']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td><?php echo htmlspecialchars($row['category_name'] ?? 'No category'); ?></td>
                                <td>
                                    <a href="post_details.php?id=<?php echo $row['post_id']; ?>">view posts</a>
                                    <a href="edit.php?id=<?php echo $row['post_id']; ?>">Edit</a>
                                    <!-- <a href="delete.php?id=<?php echo $row['post_id']; ?>">Delete</a> -->
                                    <form method="POST" action="delete.php"
                                        onsubmit="return confirm('Delete post. This cannot be undone.');">
                                        <input type="hidden" name="post_id" value="<?php echo $row['post_id']; ?>">
                                        <button type="submit">Delete</button>
                                    </form>
                                </td>

                            </tr>
                        <?php } ?>
                    </table>

                    <p>Page <?php echo $page; ?> of <?php echo $total_pages; ?></p>
                    <?php if ($page > 1) { ?><a href="<?php echo page_link($filters, $page -1); ?>">Prev</a><?php } ?>
                    <?php if ($page < $total_pages) { ?><a href="<?php echo page_link($filters, $page + 1); ?>">Next</a><?php } ?>
                <?php } ?>
            </div>
        </div>

        <script>
            /*sidenave with opacity*/
            function openNav() {
                /* adding background and setting style. opening nav*/
                document.getElementById("mySidenav").style.width = "250px";
                document.getElementById("main").style.marginLeft = "250px";
                document.body.style.backgroundColor = "rgba(0,0,0,0.4)";
            }
            function closeNav() {
                /*closinfg nave styling and background*/
                document.getElementById("mySidenav").style.width = "0";
                document.getElementById("main").style.marginLeft = "0";
                document.body.style.backgroundColor = "White";
            }
        </script>
    </body>

</html>