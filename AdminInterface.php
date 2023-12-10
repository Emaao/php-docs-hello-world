<?php
session_start();
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
    header("Location: roomReser.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Room Interface</title>
    <link rel="stylesheet" href="body.css">
</head>

<body>
    <div>
        <h1>Admin Room Interface</h1>
        <p>Rooms reserved by users:</p>

        <!-- PHP code to fetch and display reserved rooms -->
        <?php
        include 'db.php'; // Include the database connection file

        // Fetch and display reserved rooms from the Azure Function
        $function_url = 'YOUR_AZURE_FUNCTION_URL'; // Replace with the actual URL of your Azure Function

        $response = file_get_contents($function_url);
        $reserved_rooms = json_decode($response, true);

        foreach ($reserved_rooms as $room) {
            echo '<div>';
            echo '<p>Room Number: ' . $room['RoomNumber'] . '</p>';
            echo '<p>Reserved by User</p>';
            echo '</div>';
        }
        ?>
    </div>
</body>

</html>
