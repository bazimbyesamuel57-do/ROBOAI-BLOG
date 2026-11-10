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
// if id is sent
if (!isset($_GET['id'])) {
    die("No post selected.");
}
$id = (int) $_GET['id'];

//requsting for the post from the database (draft include)
$stmt = mysqli_prepare($conn,
// "SELECT post_id FROM posts WHERE post_id = ? AND status = 'published'");
    "SELECT title, excerpt, content, author_name, category_id, status, media 
    FROM posts 
    WHERE post_id = ?");
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

// categories for the drpodown
$cat_result = mysqli_query($conn, "SELECT category_id, category_name FROM categories");
if (!$cat_result) {
    die("Categories query failed: " . mysqli_error($conn));
}

$message = '';
$image_dir = '../images/';

if (isset($_POST['submit'])) {
    // $post_id = (int) ($_POST['post_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $author_name = trim($_POST['author_name'] ?? '');
    // $media = $_POST['media'] ?? 0;
    //reads the form ihe form
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $status = $_POST['status_selection'] ?? '';

    if ($title === '' || $excerpt === '' || $content === '' || $author_name === '') {
        $message = 'please fill in the fields.';
    } elseif (mb_strlen($title) > 255 || mb_strlen($author_name) > 255) {
        $message = 'Title and author name must be 255 caharcters or less.';
    } elseif ($category_id < 1) {
        $message = 'Please choose a category.';
    } elseif ($status !== 'draft' && $status !== 'published') {
        $message = 'Invalid status';
    } else {
        // image: old is keep as default if not cahnged, unless a new valid one is uploaded
        $media = $post['media'];
        $new_media = '';

        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if ($_FILES['image']['error'] !==UPLOAD_ERR_OK) {
                $message = 'The image failed.';
            } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                $message = 'The image must be 2 MB or smaller';
            } elseif (!in_array($ext, $allowed)) {
                $message = 'Only jpg, png, gif or webp images are allowed.';
            } elseif (getimagesize($_FILES['image']['tmp_name']) ===false) {
                $message = 'That file is not a real image.';
            } else {
                $new_name = uniqid('post') . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $image_dir . $new_name)) {
                $new_media = 'images/' . $new_name;
                $media = $new_media;
                } else {
                $message = 'Could not save the image.';
                }
            }
        }
    

        // saving  when nothing went wrong above
        if ($message === '') {
            $u_stmt = mysqli_prepare($conn, 
            "UPDATE posts
            SET title = ?, excerpt = ?, content = ?, author_name = ?, category_id = ?, status = ?, media = ?
            WHERE post_id = ?");
            if (!$u_stmt) {
                die('prepare failed: ' . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($u_stmt, 'ssssissi', $title, $excerpt, $content, $author_name, $category_id, $status, $media, $id);

            if (mysqli_stmt_execute($u_stmt)) {
            // old file is deleted only if the update is successfull
                $old = $post['media'];
                if ($new_media !== '' && $old !== '') {
                    $old_file = $image_dir . basename($old);
                    if (is_file($old_file)) {
                        unlink($old_file); // changing the file link
                    }
                }
            
                header('Location: admin_posts.php?updated=1');
                exit;
            } else {
                if ($new_media !== '') {
                    unlink('../' . $new_media); // if upadate fails remove the thn new upload
                }
                $message = 'Error: ' . mysqli_stmt_error($u_stmt);
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="admin.css">

    <title>Edit post</title>
</head>
    <body>
        <header>
            <div class="log-content">
                <p class="site-title">
                    <!-- <a href="#" class="logo">ROBOAI</a> -->
                    <a href="#"><img class="log" src="/ROBOAI/images/logo.index.png" alt="Roboai Logo"></a>
                </p>
            </div>
            <button style="float:right; margin-right:10px;">
                <a href="admin_login.php">Logout</a>
            </button>
        </header>

        <div>
            <h1 style="text-align: center;">EDIT POST</h1>
        </div>
    <?php
    // error displayed on the screen
    if ($message !== '') {
        echo '<p style="color: red;">' . htmlspecialchars($message) .'</p>';
    }
    ?>
        <div class="container" style=" max-width: 800px;">
            <form action="edit.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data"> <!--multipart/form-data for file uploads-->
                <div class="mb-3">
                    <label class="form-label">The Post title</label><br>
                    <input class="form-control" type="text" name="title" placeholder="Give the post title here!" value="<?php echo htmlspecialchars($post['title']); ?>" required><br>
                </div>
                <div class="mb-3">
                    <label class="form-label">Excerpt</label><br>
                    <textarea class="form-control" rows="4" name="excerpt" placeholder="write the excerpt for the post here!" required><?php echo htmlspecialchars($post['excerpt']); ?></textarea><br>
                </div>
                <div class="mb-3">
                    <label class="form-label">Post content</label><br>
                    <textarea class="form-control" rows="4" name="content" placeholder="write the post content here!" required><?php echo htmlspecialchars($post['content']); ?></textarea> <br>
                    <input class="form-control" type="text" name="author_name" placeholder="Author name" value="<?php echo htmlspecialchars($post['author_name']); ?>" required><br>
                </div>

                <div class="mb-3">
                <!-- <input type="file" name="image"> <br> selecting file/ image for the post -->
                    <label class="form-label"> add Image</label><br>
                    <?php
                        if  (!empty($post['media'])) { ?>
                            <p>Current image:</p>
                            <img src="/ROBOAI/<?php echo htmlspecialchars($post['media']); ?>" alt="current post image" style="max-width:200px;"><br>
                    <?php } ?>
                    <input class="form-control" type="file" name="image" accept="image/*"><br>
                </div>

                <div class="mb-3">    
                    <label class="form-label">Category</label><br>
                    <select class="form-select" id="category" name ="category_id">
                        <?php 
                        while ($row =mysqli_fetch_assoc($cat_result))
                            { ?>
                        <!-- <option value="select category">--select category--<option> -->
                        <option value="<?php echo $row['category_id']; ?>"
                            <?php if ((int) $row['category_id'] === (int) $post['category_id']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($row['category_name']); ?>
                        </option>

                        <?php }  ?> 
                    </select> <br>
                </div>
                
                <div class="mb-3">
                    <select class="form-select" name="status_selection">
                    <!-- <h3>Status</h3> -->
                        <!-- <option value="select status">--select status--</option> -->
                        <option value="draft"<?php if ($post['status'] === 'draft') echo " selected"; ?>>Draft</option>
                        <option value="published"<?php if ($post['status'] === 'published') echo " selected"; ?>>Published</option>
                    </select><br>
                </div>
                <!-- <input type="text" name="admin_username"> -->
                <button type="submit" value="update post" name="submit">Save</button>
                <a href="admin_posts.php">Cancel</a>
            </form>
        </div>

                <footer class="footer">ROBOAI Blog 2026 | Dashboard | <a href="admin_posts.php">Manage Posts</a> | <a href="admin_register.php">Register</a> | <a href="admin_login.php">LogOut</a> |</footer>
    </body>
</html>