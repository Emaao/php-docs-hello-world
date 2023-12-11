<?php
include 'db.php';
error_log("oneroom.php script started"); // Log to the server error log

// Get RoomNumber from the query parameters
$roomNumber = $_GET['RoomNumber'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    try {
        // Log to check if the reservation button is clicked
        error_log("Reserve button clicked for RoomNumber: $roomNumber");

        // For simplicity, let's just log a success message
        error_log("Room reserved successfully");

        // You can add the room reservation logic here

        // Respond with a success message
        echo json_encode(['message' => 'Room reserved successfully']);
        exit();
    } catch (Exception $e) {
        // Log any errors
        error_log("Error reserving room: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Internal Server Error']);
        exit();
    }
}

// Fetch room details from the database
$sql = "SELECT * FROM Salles WHERE RoomNumber = :roomNumber";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':roomNumber', $roomNumber);
$stmt->execute();
$room = $stmt->fetch(PDO::FETCH_ASSOC);

// Output HTML content
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details</title>
    <link rel="stylesheet" href="body.css">
</head>
<body>
    <div>
        <h1>Room Details</h1>
        <p>Room Number: <?php echo $room['RoomNumber']; ?></p>
        <p>Availability: <span id="availability"><?php echo $room['Availability']; ?></span></p>

        <!-- Reserve button and form -->
        <form id="reserveForm" method="post">
            <button type="button" onclick="reserveRoom(<?php echo $room['RoomNumber']; ?>)">Reserve Room</button>
        </form>

    <!-- JavaScript code to handle room reservation -->
    <script>
        // JavaScript function to handle room reservation
        function reserveRoom(RoomNumber) {
            // Log to check if the reserveRoom function is called
            console.log("Reserve Room function called for RoomNumber:", RoomNumber);

            // Make an AJAX request to simulate the room reservation
            // In a real scenario, you'd send the reservation to your serverless function or backend
            // For now, we're logging a success message
            console.log("Room reserved successfully");

            // Update the button text and disable the button
            document.querySelector("button").innerText = "Room Reserved";
            document.querySelector("button").disabled = true;

            // Update the availability in the page (for demonstration purposes)
            document.getElementById("availability").innerText = "0";
        }
    </script>

</body>
</html>