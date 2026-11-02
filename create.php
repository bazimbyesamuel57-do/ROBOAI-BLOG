<?php

session_start();

require 'connect.php';

// categories dropdown
$sql ="SELECT category_id, category_name FROM categories ORDER BY category_name";
$result = mysqli_query($conn, $sql); // executing the query result


if (!$result) {
    die("Categories query failed: " . mysqli_error($conn));

}

// author dropdown
$author = mysqli_query($conn, 
    "SELECT author_id, author_name, author_bio FROM author ORDER BY author_name");
if (!$author) {
    die('Author query failed: ' . mysqli_error($conn));
}


$message = '';
$image_dir = 'images/';

// restoring draft saced before going to add-author.php
$draft = [];
if (isset($_GET['author_id']) && isset($_SESSION['draft'])) {
    $draft = $_SESSION['draft'];
    unset($_SESSION['draft']);

}

$chosen_author = (int) ($_POST['author_id'] ?? $_GET['author_id'] ?? 0);
$chosen_category = (int) ($_POST['category_id'] ?? $draft['category_id']?? 0);

// save draft to Session and redirect
if (isset($_POST['go_to_author'])) {
    $_SESSION['draft'] = [
        'title' => $_POST['title'] ?? '',
        'excerpt' =>$_POST['excerpt'] ?? '',
        'content' => $_POST['content'] ?? '',
        'author_id' => $_POST['author_id'] ?? '',
        'category_id' => $_POST['category_id'] ?? '',
        'new_category_name' => $_POST['new_category_name'] ?? '',
        'status_selection' => $_POST['status_selection'] ?? 'draft',
    ];
    header('Location: add_author.php?from=create');
    exit;
}

if (isset($_POST['submit'])) {
    // print_r($_POST);
    // exit;
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $author_id = (int) ($_POST['author_id'] ?? 0);
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $new_category_name = trim($_POST['new_category_name'] ?? '');
    // $category_name = trim($_POST['category_name']);
    $status = $_POST['status_selection'] ?? $draft['status_selection'] ?? 'draft';
    $admin_id = 1; // temporary, it must be an id that exists in the admin table
    

    if ($title === '' || $excerpt === '' || $content === '') {
        $message = 'please fill in the fields.';
    } elseif (mb_strlen($title) > 255) {
        $message = 'The title must be 255 characters or less.';
    } elseif ($author_id < 1) {
        $message = 'Please choose an author.';
    } elseif ($category_id < 1 && $new_category_name === '') {
        $message = 'Please choose a category or enter a new one.';
    } elseif (mb_strlen($new_category_name) > 255){
        $message = 'The category name must be 255 characters or less.';
    } elseif ($status !== 'draft' && $status !== 'published') {
        $message = 'Invalid status.';
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
        
        // the image  file  handling
        $media = '';
        
        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if ($_FILES['image']['error'] !==UPLOAD_ERR_OK) {
                $message = 'The image failed.';
            } elseif (!in_array($ext, $allowed)) {
                $message = 'Only jpg, png, gif or webp images are allowed.';
            } elseif (getimagesize($_FILES['image']['tmp_name']) ===false) {
                $message = 'That file is not a real image.';
            } else {
                $new_name = uniqid('post') . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], 'images/' . $new_name)) {
                $media = 'images/' . $new_name;
                } else {
                $message = 'Could not save the image.';
                }
            }
        }
    

        // $media =$_FILES['image']['name'];
        // //temporary file location
        // $temp_location = $_FILES['image']['tmp_name'];
        // // our file location
        // $our_location = "image/";
        // }
        //using the function
        // if (!empty($name)) {
        //     move_uploaded_file($temp_location, $our_location.$name);
        // }
        // $sql1 = "SELECT id from categories where category_name= '$category_name'";
        // $result1 = mysqli_query($conn, $sql1);
        // if($result1->num_rows>0) {
        //     $row = mysqli_fetch_assoc($result1);
        //     $idforcategory = $row['category_id'];
        //     }
        // a typed category
        if ($message === '' && $new_category_name !== '') {
            $c_stmt = mysqli_prepare($conn, "SELECT category_id FROM categories WHERE category_name = ?");
            mysqli_stmt_bind_param($c_stmt, 's', $new_category_name);
            mysqli_stmt_execute($c_stmt);
            $found = mysqli_fetch_assoc(mysqli_stmt_get_result($c_stmt));

            if ($found) {
                $category_id = (int) $found['category_id'];
            } else {
                $i_stmt = mysqli_prepare($conn, "INSERT INTO categories (category_name) VALUES (?)");
                mysqli_stmt_bind_param($i_stmt, 's', $new_category_name);
                if (mysqli_stmt_execute($i_stmt)) {
                    $category_id = (int) mysqli_insert_id($conn);
                } else {
                    $message = 'could not create the category.';
                }
            }
        }

        //saving the post 
        if ($message === '') {
            $stmt = mysqli_prepare($conn, 
                "INSERT INTO posts (category_id, admin_id, author_id, title, excerpt, content, author_name, status, published_on, media)
                VALUES(?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
            if (!$stmt) {
                die('Prepare failed: ' . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, 'iiissssss', 
                $category_id, $admin_id, $author_id, $title, $excerpt, $content, $author_name, $status, $media);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: admin_posts.php?created=1"); //route
                exit;
            } 
            else {
                $message= "Error: " . mysqli_stmt_error($stmt);
            }

        // $sql2 = "INSERT INTO posts(title, excerpt, content, media)VALUES('$title','$excerpt', '$content', '$category_name')";

        // $result2 = mysqli_query($conn, $sql2);
        // if($result2){
        //     echo "post added successfully!";
        // }
        
        }
    }
}



?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Create Post</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="css/style.css" rel="stylesheet">
    </head>
    <body>

        <?php
        if (isset($_GET['saved'])) {
            echo '<p style="color:green;">Post add successfully!</p>';
        }
        if ($message !== '') {
            echo '<p style="color:red;">' . htmlspecialchars($message) . '</p>';
        }
        ?>
        <div>
            <form action="create.php" method="POST" enctype="multipart/form-data"> <!--multipart/form-data for file uploads-->
                <div>
                    <label>Post title</label><br>
                    <input type="text" name="title" placeholder="Give the post title here!" value="<?php echo htmlspecialchars($_POST['title'] ?? $draft['title'] ?? ''); ?>" required><br>
                </div>
                
                <div>
                    <label>Excerpt</label><br>
                    <textarea name="excerpt" placeholder="write the excerpt for the post here!" required><?php echo htmlspecialchars($_POST['excerpt'] ?? $draft['excerpt'] ?? ''); ?></textarea><br>
                </div>
                
                <div>
                    <label>Enter Post Content</label><br>
                    <textarea name="content" placeholder="write the post content here!" required><?php echo htmlspecialchars($_POST['content'] ?? $draft['content'] ?? ''); ?></textarea> <br>
                </div>

                <div>
                    <label>Choose Author</label><br>
                    <select name="author_id">
                        <option value="">--choose an author--</option>
                            <?php while ($a = mysqli_fetch_assoc($author)) { ?>
                                <option value="<?php echo $a['author_id']; ?>"
                                <?php if ($chosen_author === (int) $a['author_id']) echo ' selected'; ?>>
                                <?php echo htmlspecialchars($a['author_name']); ?>
                                </option>
                            <?php } ?>
                    </select>
                    <button type="submit" name="go_to_author"> + New author</button>
                    
                    <!-- <input type="text" name="author_name" placeholder="Author name" required><br> -->
                </div>

                <div>
                    <label>Insert image</label><br>
                    <input type="file" name="image"> <br> <!--selecting file/ image for the post-->
                </div>

                <div>
                    <label>Select category</label><br>
                    <select id="category" name="category_id">
                        <option>--select category--</option>
                        <?php
                        while ($row =mysqli_fetch_assoc($result))
                            { ?>
                        <option value="<?php echo $row['category_id']; ?>"
                        <?php if ($chosen_category === (int) $row['category_id']) echo ' selected'; ?>>
                        <?php echo htmlspecialchars($row['category_name']); ?>
                        </option>
                         <!--option selection fetched from the database-->
                        <?php }  ?> 
                    </select> <br>
                    <label>Or Enter category</label><br>
                    <input type="text" name="new_category_name" placeholder="A category is not under the list" value="<?php echo htmlspecialchars($_POST['new_category_name'] ?? $draft['new_category_name'] ?? ''); ?>"><br>
                </div>    
                
                <div>
                    <label>Status</label><br>
                    <?php $savedStatus = $_POST['status_selection'] ?? $draft['status_selection'] ?? 'draft'; ?>
                    <select name="status_selection">
                        <!-- <option value="default">--select--</option> -->
                        <option value="draft" <?php if ($savedStatus === 'draft') echo 'selected'; ?>>Draft</option>
                        <option value="published" <?php if ($savedStatus === 'published') echo 'selected'; ?>>Published</option>
                    </select>
                    <!-- <input type="text" name="admin_username"> -->
                </div>
                <button type="submit" name="submit">Save</button>
                <a href="admin_posts.php">Cancel</a>
            </form>
        </div>

        <div>

        </div>
    
    </body>
</html>