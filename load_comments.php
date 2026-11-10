<?php
require 'connect.php';
header('Content-Type: application/json');

$per_load = 6; // the number of comments per load, written once

// 1. read post_id and offset from $_GET -> cast both to int
$post_id = (int) ($_GET['post_id'] ?? 0);
$offset  = max(0, (int) ($_GET['offset'] ?? 0));

if ($post_id < 1) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid post.']);
    exit;
}

// 2. prepare, bind 'ii', execute, get the result
$c_stmt = mysqli_prepare($conn,
    "SELECT Name, Date_Time, Message
     FROM comments
     WHERE post_id = ?
     ORDER BY Date_Time DESC, Comments_id DESC
     LIMIT $per_load OFFSET ?");
if (!$c_stmt) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Query failed.']);
    exit;
}
mysqli_stmt_bind_param($c_stmt, 'ii', $post_id, $offset);
mysqli_stmt_execute($c_stmt);
$c_result = mysqli_stmt_get_result($c_stmt);

// 3. build the array
$comments = [];
while ($row = mysqli_fetch_assoc($c_result)) {
    $comments[] = [
        'name'    => $row['Name'],
        'date'    => $row['Date_Time'],
        'comment' => $row['Message'],
    ];
}

// 4. count the total comments for this post
$t_stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM comments WHERE post_id = ?");
mysqli_stmt_bind_param($t_stmt, 'i', $post_id);
mysqli_stmt_execute($t_stmt);
$total = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($t_stmt))['total'];
$has_more = ($offset + count($comments)) < $total;

// 5. send it back
echo json_encode(['ok' => true, 'comments' => $comments, 'has_more' => $has_more]);