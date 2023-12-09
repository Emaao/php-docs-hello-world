<?php
include 'rooms.php';

// Get RoomNumber from the query parameters
$roomNumber = $_GET['RoomNumber'];

// Fetch room details from the database
$sql = "SELECT * FROM Salles WHERE RoomNumber = :roomNumber";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':roomNumber', $roomNumber);
$stmt->execute();
$room = $stmt->fetch(PDO::FETCH_ASSOC);

// Display room details
echo '<div>';
echo '<p>Room Number: ' . $room['RoomNumber'] . '</p>';
echo '<p>Availability: ' . $room['Availability'] . '</p>';
// Add other details as needed

// Display the room image (assuming 'Image' is the column name for the image URL in your database)
echo '<img src="' . $room['imagePath'] . '" alt="Room Image">';

echo '</div>';
?>
