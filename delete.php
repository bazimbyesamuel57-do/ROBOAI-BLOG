<?php
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('invalid request.');
}
$post_id = (int) ($_POST['post_id'] ?? 0);

//reading the media path first
$stmt = mysqli_prepare($conn, 'SELECT media FROM posts WHERE post_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $post_id);
mysqli_stmt_execute($stmt);
$post = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$post) {
    die('post not found.');
}

//deleting the row
$d_stmt = mysqli_prepare($conn, 'DELETE FROM posts WHERE post_id = ?');
mysqli_stmt_bind_param($d_stmt, 'i', $post_id);

if (mysqli_stmt_execute($d_stmt)) {
    // now the deletion of the file
    if ($post['media'] !== '') {
        $file = 'images/' . basename($post['media']);
        if (is_file($file)) {
            unlink($file);
        }
    }
    header('Location: admin_posts.php?deleted=1');
    exit;
} else {
    die('delete failed: ' . mysqli_stmt_error($d_stmt));
}
?>