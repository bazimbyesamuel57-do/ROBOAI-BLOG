<?php
require 'connect.php';


session_start();

// redirect back to the login page
session_destroy(); 
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
}
else {
    echo "Welcome: {$_SESSION['name']}";
}

// Left join to fetch all posts
$sql = "SELECT p.post_id, p.title, p.author_name, p.published_on, p.status, c.category_name
        FROM posts p
        LEFT JOIN categories c ON p.category_id = c.category_id
        ORDER BY p.published_on DESC, p.post_id DESC";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add post</title>
</head>
<body>

        <button >
            <a href="admin_login.php">Logout</a>
        </button>


    <h1> All posts</h1>
    <p><a href="create.php">+ Add a post</a></p>

    <?php
    if (isset($_GET['updated'])) {
        echo '<p style="color:green;">Post updated.</p>';
    }
    if (isset($_GET['deleted'])) {
        echo '<p style="color:green;">Post deleted.</p>';
    }
    ?>


    <table border="1">
        <tr>
            <th>Title</th>
            <th>Author Name</th>
            <th>Published on</th>
            <th>status</th>
            <th>Category</th>
            <th>Actions</th>
        </tr>

        <?php if (mysqli_num_rows($result) ===0) { ?>
            <tr><td colspan="6"> No posts yet.</td></tr>
            <?php } ?>
        
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
</body>
</html>