<?php
require 'connect.php';

$sql = "SELECT * FROM comments ORDER BY date_time DESC"; // the query for the data
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

while ($row = mysqli_fetch_assoc($result)) {
    $name = htmlspecialchars($row["Name"]);
    $date_time = $row["Date_Time"];
    $comment = htmlspecialchars($row["Message"]);
    // $comment = $row["Message"];
    echo "<p><strong>$name</strong> ($date_time)<br>$comment</p>";
    // echo $row["Name"] . ":" . $row["Date_Time"] . $row["Message"] . "<br>";
    // the mysqli_fetch_assoc() outputs one row per loop turn as an array. when no rows left it returens false 
    //and the while stops


}

?>