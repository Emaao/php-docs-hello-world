<?php
include 'db.php';
error_log("oneroom.php script started"); // Log to the server error log

// Get RoomNumber from the query parameters
$roomNumber = $_GET['RoomNumber'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    try {
        // Log the enqueued message
        $enqueuedMessage = json_encode(['RoomNumber' => $roomNumber]);
        error_log("Enqueued message: " . $enqueuedMessage);

        // Call serverless function with roomNumber
        callServerlessFunction($roomNumber);

        // Return a success response
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Room reservation request received successfully']);
        exit();
    } catch (Exception $e) {
        // Log the error
        error_log("Error processing reservation: " . $e->getMessage());
        // Return an error response if needed
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

        <!-- Display the room image from Blob Storage -->
        <img src="<?php echo $room['imagePath'] . '?si=imanee&spr=https&sv=2022-11-02&sr=c&sig=zZGbqUZMIy3SuTjwwfVIkt996nMuPTppsZXGJp5VD0Q%3D'; ?>" alt="Room Image">

        <!-- Reserve button and form -->
        <form id="reserveForm" method="post">
            <?php
            if ($room['Availability'] == 1 && $_SESSION['isAdmin'] != 1) {
                echo '<button type="button" onclick="reserveRoom(' . $room['RoomNumber'] . ')">Reserve Room</button>';
            } else {
                echo '<button type="button" disabled>Room Reserved</button>';
            }
            ?>
        </form>

    <!-- JavaScript code to handle room reservation -->
    <script>
        // JavaScript function to handle room reservation
        function reserveRoom(RoomNumber) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        // Log the serverless function response
                        console.log("Serverless function response:", this.responseText);
                    } else {
                        // Log an error if the request is not successful
                        console.error("Error updating availability");
                    }
                }
            };
            xhttp.open("POST", "/oneroom.php?RoomNumber=" + RoomNumber, true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("reserve=true");
        }

        // JavaScript function to call the serverless function
        function callServerlessFunction(RoomNumber) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    // Log the serverless function response
                    console.log("Serverless function response:", this.responseText);
                }
            };
            xhttp.open("POST", "https://securitee.azurewebsites.net/api/srvFunction?code=ma9q8GqIgDniQSR31BqVCUtQqkaF_JyaD7KxON7enzwJAzFuANgDxQ==", true);
            xhttp.setRequestHeader("Content-type", "application/json");
            xhttp.send(JSON.stringify({ roomNumber: RoomNumber }));
        }
    </script>

</body>
</html>
