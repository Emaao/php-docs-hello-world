<?php
include 'rooms.php';

// Get RoomNumber from the query parameters
$roomNumber = $_GET['RoomNumber'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    try {
        // Update the availability in the database
        $sqlUpdate = "UPDATE Salles SET Availability = 0 WHERE RoomNumber = :roomNumber";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':roomNumber', $roomNumber);
        $stmtUpdate->execute();

        // Fetch updated room details from the database
        $sql = "SELECT * FROM Salles WHERE RoomNumber = :roomNumber";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':roomNumber', $roomNumber);
        $stmt->execute();
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
        
        //Generate SAS for the image URl
        $sasToken = generateSasToken($room['imagePath']);
        
        // Return updated availability as a JSON response
        header('Content-Type: application/json');
        echo json_encode(['availability' => $room['Availability']]);
        exit();
    } catch (PDOException $e) {
        // Log the error
        error_log("Error updating availability: " . $e->getMessage());
        // Return an error response if needed
        http_response_code(500);
        exit();
    }
}

// Fetch room details from the database
$sql = "SELECT * FROM Salles WHERE RoomNumber = :roomNumber";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':roomNumber', $roomNumber);
$stmt->execute();
$room = $stmt->fetch(PDO::FETCH_ASSOC);
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
        <img src="<?php echo $room['imagePath'] . $sasToken; ?>" alt="Room Image">

        <!-- Reserve button and form -->
        <form id="reserveForm">
            <button type="button" onclick="reserveRoom(<?php echo $room['RoomNumber']; ?>)">
                Reserve Room
            </button>
        </form>
    </div>

    <!-- JavaScript function to handle room reservation -->
    <script>
        //function to generate sas token for blob 
        function generateSasToken($blobUrl)
    {
        $accountName = 'reservac'; // Replace with your storage account name
        $accountKey = 'FFth1+WCTmbeujiIjwW6VnnPM8QowQ9UvJMcbI8Xn8X7oQ1yytzbOU2H+Qwvb4ipJgp5MrEN4mJc+AStF7w/XQ==';   // Replace with your storage account key
        $containerName = 'blobcontainer';     // Replace with your container name

        $expiry = time() + 3600;  // Set the expiry time (1 hour from now)
        $stringToSign = utf8_encode(urlencode($blobUrl) . "\n" . $expiry);
        $signature = base64_encode(hash_hmac('sha256', $stringToSign, base64_decode($accountKey), true));

        $sasToken = sprintf(
            '?sv=%s&se=%s&sr=%s&sp=%s&sig=%s',
            '2019-12-12',       // Storage service version
            $expiry,            // Expiry time
            'b',                // Signed resource: blob
            'r',                // Signed permissions: read
            rawurlencode($signature)
        );

        return $sasToken;
    }
        // JavaScript function to handle room reservation
        function reserveRoom(RoomNumber) {
            // Make an AJAX request to update availability
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        // Update the button text and disable the button
                        document.querySelector("button").innerText = "Room Reserved";
                        document.querySelector("button").disabled = true;

                        // Update the availability in the page
                        document.getElementById("availability").innerText = "0";
                    } else {
                        // Log an error if the request is not successful
                        console.error("Error updating availability");
                    }
                }
            };
            xhttp.open("POST", "oneroom.php?RoomNumber=" + RoomNumber, true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("reserve=true");
        }

    </script>
</body>

</html>
