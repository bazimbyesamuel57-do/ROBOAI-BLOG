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


//fetching contact_us messages
$contact_result = mysqli_query($conn,
    "SELECT contact_us_id, firstname, lastname, email, subject
    FROM contact_us
    ORDER BY contact_us_id DESC");
if (!$contact_result) {
    die ('contact  query failed: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>admin feeds</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="admin.css" rel="stylesheet">
    </head>
    <body>
        <div>
            <h2>Contact Messages</h2>

            <?php if (mysqli_num_rows($contact_result) === 0) { ?>
                <p>No messages yet.</p>
            <?php } else { ?>
                <table border="1">
                    <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                    </tr>
                    <?php while ($msg = mysqli_fetch_assoc($contact_result)) { ?>
                        <tr>
                            <td><?php echo $msg['contact_us_id']; ?></td>
                            <td><?php echo htmlspecialchars($msg['firstname']); ?></td>
                            <td><?php echo htmlspecialchars($msg['lastname']); ?></td>
                            <td><?php echo htmlspecialchars($msg['email']); ?></td>
                            <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                            <td></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
        </div>

    </body>
</html>