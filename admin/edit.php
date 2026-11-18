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
    "SELECT title, excerpt, content, author_id, author_name, category_id, status, media 
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

// author dropdown
$author = mysqli_query($conn, 
    "SELECT author_id, author_name, author_bio FROM author ORDER BY author_name");
if (!$author) {
    die('Author query failed: ' . mysqli_error($conn));
}

$chosen_author = (int) ($_POST['author_id'] ?? $post['author_id'] ?? 0);


$message = '';
$image_dir = '../images/';

if (isset($_POST['submit'])) {
    // $post_id = (int) ($_POST['post_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    // $author_name = trim($_POST['author_name'] ?? '');
    $author_id = (int) ($_POST['author_id'] ?? 0);
    // $media = $_POST['media'] ?? 0;
    //reads the form ihe form
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $status = $_POST['status_selection'] ?? '';



    if ($title === '' || $excerpt === '' || $content === '') {
        $message = 'please fill in the fields.';
    } elseif (mb_strlen($title) > 255) {
        $message = 'Title must be 255 caharcters or less.';
    } elseif ($author_id < 1) { 
        $message = 'Please choose an author.'; 
    } elseif ($category_id < 1) {
        $message = 'Please choose a category.';
    } elseif ($status !== 'draft' && $status !== 'published') {
        $message = 'Invalid status';
    } else {

        // for exiting author
        $a_stmt = mysqli_prepare($conn, 
            "SELECT author_name FROM author WHERE author_id = ?");
        if (!$a_stmt) {
            die('Prepare failed: ' . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($a_stmt, 'i', $author_id);
        mysqli_stmt_execute($a_stmt);
        $author_row = mysqli_fetch_assoc(mysqli_stmt_get_result($a_stmt));
        
        if (!$author_row) {
            $message ='That author doenot exist.';
        } else {
            $author_name = $author_row['author_name'];
        }

        

        // image: old is keep as default if not cahnged, unless a new valid one is uploaded
        $media = $post['media'];
        $new_media = '';

        if ($message === '' && !empty($_FILES['image']['name'])) {

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
            SET title = ?, excerpt = ?, content = ?, author_name = ?, author_id = ?, category_id = ?, status = ?, media = ?
            WHERE post_id = ?");
            if (!$u_stmt) {
                die('prepare failed: ' . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($u_stmt, 'ssssiissi', $title, $excerpt, $content, $author_name, $author_id, $category_id, $status, $media, $id);

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
        <header class="blog-header">
            <div class="logo">
                <!-- <a href="#" class="logo">ROBOAI</a> -->
                <a href="#"><img style="height: 50px; width: auto;" src="/ROBOAI/images/logo2.index.png" alt="Roboai Logo"></a>
            </div>
            <div class="log-auth">
                <a class="log-auth-btn" href="admin_login.php">Logout</a>
            </div>
        </header>

        <div>
            <h1 style="text-align: center;">EDIT POST</h1>
        </div>
   
        <div class="container" style=" max-width: 800px;">

             <?php
            // error displayed on the screen
            if ($message !== '') {
                echo '<p style="color: red;">' . htmlspecialchars($message) .'</p>';
            }
            ?>

            <form action="edit.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data"> <!--multipart/form-data for file uploads-->
                <div class="mb-3">
                    <label class="form-label">The Post title</label>
                    <input class="form-control" type="text" name="title" placeholder="Give the post title here!" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Excerpt</label>
                    <textarea class="form-control" rows="4" name="excerpt" placeholder="write the excerpt for the post here!" required><?php echo htmlspecialchars($post['excerpt']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Post content</label>
                    <textarea class="form-control" rows="4" name="content" placeholder="write the post content here!" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Choose Author</label>
                    <select class="form-select form-select-lg mb-2" name="author_id">
                        <option value="0">--choose an author--</option>
                            <?php while ($a = mysqli_fetch_assoc($author)) { ?>
                                <option value="<?php echo $a['author_id']; ?>"
                                <?php if ($chosen_author === (int) $a['author_id']) echo ' selected'; ?>>
                                <?php echo htmlspecialchars($a['author_name']); ?>
                                </option>
                            <?php } ?>
                    </select>
                    <!-- <label class="form-label">Author Name</label>
                    <input class="form-control" type="text" name="author_name" placeholder="Author name" value="<?php echo htmlspecialchars($post['author_name']); ?>" required> -->
                </div>

                <div class="mb-3">
                <!-- <input type="file" name="image"> <br> selecting file/ image for the post -->
                    <label class="form-label"> Add/Change Image</label>
                    <?php
                        if  (!empty($post['media'])) { ?>
                            <p>Current image:</p>
                            <img src="/ROBOAI/<?php echo htmlspecialchars($post['media']); ?>" alt="current post image" class="img-fluid" style="max-width:200px;">
                    <?php } ?>
                    <input class="form-control" type="file" name="image" accept="image/*">
                </div>

                <div class="mb-3">    
                    <label class="form-label">Category</label>
                    <select class="form-select" id="category" name ="category_id">
                        <option value="0">--select category--</option>
                        <?php 
                        while ($row =mysqli_fetch_assoc($cat_result))
                            { ?>
                        <option value="<?php echo $row['category_id']; ?>"
                            <?php if ((int) $row['category_id'] === (int) $post['category_id']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($row['category_name']); ?>
                        </option>

                        <?php }  ?> 
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status_selection">
                    <!-- <h3>Status</h3> -->
                        <!-- <option value="select status">--select status--</option> -->
                        <option value="draft"<?php if ($post['status'] === 'draft') echo " selected"; ?>>Draft</option>
                        <option value="published"<?php if ($post['status'] === 'published') echo " selected"; ?>>Published</option>
                    </select>
                </div>
                <!-- <input type="text" name="admin_username"> -->
                <button class="btn btn-primary mt-2" type="submit" value="update post" name="submit">Save</button>
                <a class="btn btn-secondary mt-2" href="admin_posts.php">Cancel</a>
            </form>
        </div>

        <footer class="footer">ROBOAI Blog 2026 | Dashboard | <a href="admin_posts.php">Manage Posts</a> | <a href="admin_register.php">Register</a> | <a href="admin_login.php">LogOut</a> |</footer>
    </body>
</html>