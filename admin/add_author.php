<?php 
// require '/connect.php';

$host = "localhost";
$user = "root";
$password = "";
$database = "roboai_blog_db";
$port = 3307;

$conn =mysqli_connect($host, $user, $password, $database, $port);

if (!$conn ) {
    die("Connection failed: " . mysqli_connect_error());

}


$message = '';
$author_dir = '../images/authors/';
$from = $_POST['from'] ?? $_GET['from'] ?? '';
$from = ($from === 'create') ? 'create' : '';

if (isset($_POST['submit'])) {
    $author_name =trim($_POST['author_name'] ?? '');
    $author_bio = trim($_POST['author_bio'] ?? '');

    if ($author_name === '') {
        $message = 'Please enter the author name.';
    }
    elseif (mb_strlen($author_name) > 255) {
        $message = 'The name must be 255 characters or less';
    }
    else {
        //image, if no photo
        $author_image = '';

        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if ($_FILES['image']['error'] !==UPLOAD_ERR_OK) {
                $message = 'The image failed.';
            } elseif ($_FILES['image'] ['size'] > 2 * 1024 * 1024) {
                $message = 'The image must be 2 MB or smaller.';
            } elseif (!in_array($ext, $allowed)) {
                $message = 'Only jpg, png, gif or webp images are allowed.';
            } elseif (getimagesize($_FILES['image']['tmp_name']) ===false) {
                $message = 'That file is not a real image.';
            } else {
                $new_name = uniqid('author_') . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $author_dir . $new_name)) {
                    $author_image = 'images/authors/' . $new_name;
                } else {
                    $message = 'Could not save the image.';
                }
            }
        }

        if ($message === '') {
            $stmt = mysqli_prepare($conn, 
                "INSERT INTO author(author_name, author_bio, author_image) VALUES (?, ?, ?)");
            if (!$stmt) {
                die('Prepare failed: ' . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, 'sss', $author_name, $author_bio, $author_image);
            
            if (mysqli_stmt_execute($stmt)) {
                // header('Location: add_author.php?saved=1');
                $new_id = mysqli_insert_id($conn);
                $back = ($from === 'create') 
                    ? 'create.php?author_id=' . $new_id
                    : 'add_author.php?saved=1';
                header('Location: ' . $back);
                exit;
            }
            $message = 'Error: ' . mysqli_stmt_error($stmt);
            if ($author_image !== ''){
                unlink('images/authors/' .$author_image);
            } 
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Add author</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="admin.css" rel="stylesheet">
    </head>
    <body>
        <h1>Enter New Author</h1>
        <div>
            <?php
            if (isset($_GET['saved'])) {
                echo '<p style="color:green;">Author added. You can choose them below.</p>';
            }
            if ($message !== '') {
                echo '<p style="color:red;">' . htmlspecialchars($message) . '</p>';
                
            }
            ?>
            <form action="add_author.php" method="POST" enctype="multipart/form-data">
                <label>Name:</label><br>
                <input type="text" name="author_name" placeholder="Enter Author name..." maxlength="255" value="<?php echo htmlspecialchars($_POST['author_name'] ?? ''); ?>" required><br>

                <label>Biography:</label><br>
                <textarea name="author_bio" placeholder="Enter author's bio..."><?php echo htmlspecialchars($_POST['author_bio'] ?? ''); ?></textarea><br>
                <!-- <select>
                    <option></option>
                </select> -->
                <label>Insert image:</label><br>
                <input type="file" name="image" accept="image/*"><br>
                <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
                <button type="submit" name="submit">Save author</button>
                <a href="<?php echo $from === 'create' ? 'create.php' : 'admin_posts.php'; ?>">Cancel</a>
            </form>
        </div>
    </body>
</html>