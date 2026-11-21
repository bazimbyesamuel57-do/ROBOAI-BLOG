<?php

require 'auth.php';
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


//fetching contact_us messages
// $contact_result = mysqli_query($conn,
//     "SELECT contact_us_id, firstname, lastname, email, subject
//     FROM contact_us
//     ORDER BY contact_us_id DESC");
// if (!$contact_result) {
//     die ('contact  query failed: ' . mysqli_error($conn));
// }

$per_page = 10;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;


//number of rows existing in total
$count_stmt = mysqli_prepare($conn, 
    "SELECT COUNT(*) AS total FROM contact_us");
if (!$count_stmt) {
    die("Count failed: " . mysqli_error($conn));
}
mysqli_stmt_execute($count_stmt);
$total = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt))['total'];
$total_pages =(int) ceil($total / $per_page);

//pagination
$stmt = mysqli_prepare($conn,
        "SELECT contact_us_id, firstname, lastname, email, subject
        FROM contact_us
        ORDER BY contact_us_id DESC
        LIMIT ? OFFSET ?");
if (!$stmt) {
    die('Query failed: ' . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, 'ii', $per_page, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>admin feeds</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <link href="admin.css" rel="stylesheet">
    </head>
    <body>

        <header class="blog-header">
            <div class="logo">
                <!-- <a href="#" class="logo">ROBOAI</a> -->
                <a href="#"><img class="blog-header-img" style="height: 50px; width: auto;" src="/ROBOAI/images/logo2.index.png" alt="Roboai Logo"></a>
            </div>
            <div class="log-auth">
                <a class="log-auth-btn" href="admin_logout.php">Logout</a>
            </div>
        </header>

        <div class="contactfeed">
            <h2 style="text-align: center;">Contact Messages</h2>

            <?php if ($total === 0) { ?>
                <p style="text-align: center;">No messages yet.</p>
            <?php } elseif ($page > $total_pages) { ?>
                <p style="text-align: center;"> No more messages. <a href="admin_contactfeed.php">back to first page.</a></p>
            <?php } else { ?>
            <div class="table-responsive">
                <table border="1" class="table table-hover">
                    <tr style="background-color: aquamarine;">
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                    </tr>
                    <?php while ($msg = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $msg['contact_us_id']; ?></td>
                        <td><?php echo htmlspecialchars($msg['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($msg['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($msg['email']); ?></td>
                        <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="pageCount">
                <p style="text-align: center;">Page <?php echo $page; ?> of <?php echo $total_pages; ?></p>
                <div class="next-prev">
                    <?php if ($page > 1) { ?><a href="?page=<?php echo $page - 1; ?>">Prev</a><?php } ?>
                    <?php if ($page > 1 && $page < $total_pages) {?><span> | </span><?php } ?>
                    <?php if ($page < $total_pages) { ?><a href="?page=<?php echo $page + 1; ?>"> Next</a><?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
        
        <footer class="footer">ROBOAI Blog 2026 | Dashboard | <a href="admin_posts.php">Manage Posts</a> | <a href="admin_register.php">Register</a> | <a href="admin_logout.php">LogOut</a> |</footer>
        <script src="nocache.js"></script>
    </body>
</html>