<?php
include 'rooms.php';

// Get RoomNumber from the query parameters
$roomNumber = $_GET['RoomNumber'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    try {
        // Fetch idReservation from the database based on idUser and RoomNumber
        $sqlFetchIdReservation = "SELECT idResa FROM Resa WHERE idUser = :idUser AND idRoom = :roomNumber";
        $stmtFetchIdReservation = $conn->prepare($sqlFetchIdReservation);
        $stmtFetchIdReservation->bindParam(':idUser', $_SESSION['userId']); // Assuming you have a session variable for userId
        $stmtFetchIdReservation->bindParam(':roomNumber', $roomNumber);
        $stmtFetchIdReservation->execute();
        $reservation = $stmtFetchIdReservation->fetch(PDO::FETCH_ASSOC);

        // Check if idReservation is found
        if (!$reservation) {
            // Handle the case where no reservation is found
            http_response_code(404);
            echo json_encode(['error' => 'Reservation not found']);
            exit();
        }

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

        // Return updated availability and idReservation as a JSON response
        header('Content-Type: application/json');
        echo json_encode(['Availability' => $room['Availability'], 'idResa' => $reservation['idResa']]);

        // URL to which the POST request will be sent
        $url = 'https://securitee.azurewebsites.net/api/srvFunction?code=ma9q8GqIgDniQSR31BqVCUtQqkaF_JyaD7KxON7enzwJAzFuANgDxQ==';

        // Data to be sent in the POST request
        $data = array(
            'RoomNumber' => $roomNumber,
            'idResa' => $reservation['idResa'] // Send the fetched idResa value
        );

        // ... (rest of the cURL code remains unchanged)
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
        <img src="<?php echo $room['imagePath'] . '?si=imanee&spr=https&sv=2022-11-02&sr=c&sig=zZGbqUZMIy3SuTjwwfVIkt996nMuPTppsZXGJp5VD0Q%3D'; ?>" alt="Room Image">

        <!-- Reserve button and form -->
        <form id="reserveForm">
            <button type="button" onclick="reserveRoom(<?php echo $room['RoomNumber']; ?>)">
                Reserve Room
            </button>
        </form>
    </div>

    <!-- JavaScript function to handle room reservation -->
    <script>
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

                        // Get idReservation from the response
                        var response = JSON.parse(this.responseText);
                        var idReservation = response.idResa;

                        // Call serverless function with roomNumber and idReservation
                        callServerlessFunction(RoomNumber, idReservation);
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

        // JavaScript function to call the serverless function
        function callServerlessFunction(RoomNumber, idReservation) {
            // Make an AJAX request to the serverless function
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    // Log the serverless function response
                    console.log("Serverless function response:", this.responseText);
                }
            };
            xhttp.open("POST", "https://securitee.azurewebsites.net/api/srvFunction?code=ma9q8GqIgDniQSR31BqVCUtQqkaF_JyaD7KxON7enzwJAzFuANgDxQ==", true); // Replace with the actual URL of your serverless function
            xhttp.setRequestHeader("Content-type", "application/json");
            xhttp.send(JSON.stringify({ roomNumber: RoomNumber, idReservation: idReservation }));
        }
    </script>

</body>

</html>
