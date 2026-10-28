<?php
require 'connect.php';

$sql ="SELECT category_id, category_name FROM categories";
$result = mysqli_query($conn, $sql); // executing the query result


if (!$result) {
    die("Categories query failed: " . mysqli_error($conn));

}
// else {
//     // $row =mysqli_fetch_assoc($result);
//     // while ($row =mysqli_fetch_assoc($result)) {
//     //    echo "{$row['category_id']}";
//     //     echo "{$row['category_name']}";
    

//     }
// }

$message = '';

if (isset($_POST['submit'])) {
    // print_r($_POST);
    // exit;
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $author_name = trim($_POST['author_name'] ?? '');
    $category_id = (int) ($_POST['category_id'] ?? 0);
    // $category_name = trim($_POST['category_name']);
    $status = $_POST['status_selection'];
    $admin_id = 1; // temporary, it must be an id that exists in the admin table

    if ($title === '' || $excerpt === '' || $content === '' || $author_name === '') {
        $message = 'please fill in the fields.';
    } elseif (mb_strlen($title) > 255 || mb_strlen($author_name) > 255) {
        $message = 'Title and author name must too long.';
    } elseif ($category_id < 1) {
        $message = 'Please choose a category';
    } elseif ($status !== 'draft' && $status !== 'published') {
        $message = 'Invalid status.';
    } else {
        
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

        //saving the post 
        if ($message === '') {
            $stmt = mysqli_prepare($conn, 
                "INSERT INTO posts (category_id, admin_id, title, excerpt, content, author_name, status, published_on, media)
                VALUES(?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
            if (!$stmt) {
                die('Prepare failed: ' . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, 'iissssss', 
                $category_id, $admin_id, $title, $excerpt, $content, $author_name, $status, $media);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: post_categories.php"); //route
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
                <!-- <h3>The Post title</h3> -->
                <input type="text" name="title" placeholder="Give the post title here!" required><br>
                <!-- <h3>Excerpt</h3> -->
                <textarea name="excerpt" placeholder="write the excerpt for the post here!" required></textarea><br>
                <!-- <h3>Post content</h3> -->
                <textarea name="content" placeholder="write the post content here!" required></textarea> <br>
                <input type="text" name="author_name" placeholder="Author name" required><br>
                <input type="file" name="image"> <br> <!--selecting file/ image for the post-->
                <select id="category" name="category_id">
                    <?php
                     while ($row =mysqli_fetch_assoc($result))
                        { ?>
                    <!-- <option value="select category">--select category--<option> -->
                    <option value="<?php echo "{$row['category_id']}";?>"><?php echo "{$row['category_name']}";?></option>  <!--option selection fetched from the database-->
                    <!-- <option value="Automation">Automation</option>
                    <option value="Robotics">Robotics</option> -->
                    <?php }  ?> 
                </select> <br>
                
                <select name="status_selection">
                <!-- <h3>Status</h3> -->
                    <!-- <option value="select status">--select status--</option> -->
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
                <!-- <input type="text" name="admin_username"> -->
                <button  name="submit">Save</button>
                <button type="button" value="cancel">Cancel</button>
            </form>
        </div>

        <div>

        </div>
    
    </body>
</html>