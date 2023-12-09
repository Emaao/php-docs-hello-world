<?php
include 'db.php'; // Include the database connection file

$sql = "SELECT * FROM Salles";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Reservation System</title>
    <link rel="stylesheet" href="body.css">
</head>

<body>
    <div>
        <h1>Welcome to Room Reservation System</h1>
        <p>Choose a room to reserve:</p>

        <!-- PHP code to fetch and display rooms -->
        <?php
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo '<div>';
            echo '<p>Room Number: ' . $row['RoomNumber'] . '</p>';
            echo '<p>Availability: ' . $row['Availability'] . '</p>';

            // Add a link to view room details
            echo '<a href="oneroom.php?RoomNumber=' . $row['RoomNumber'] . '">View Details</a>';

            echo '</div>';
        }
        ?>
    </div>
</body>

</html>
