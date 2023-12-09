<?php
include 'db.php';

// Get RoomNumber from the query parameters
$roomNumber = $_GET['RoomNumber'];

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
        <p>Availability: <?php echo $room['Availability']; ?></p>

        <!-- Display the room image from Blob Storage -->
        <img src="<?php echo $room['imagePath']; ?>" alt="Room Image">


        <!-- Reserve button -->
        <?php
            if ($room['Availability'] == 1) {
                echo '<button onclick="reserveRoom(' . $room['RoomNumber'] . ')">Reserve Room</button>';
            } else {
                echo '<button disabled>Room Reserved</button>';
            }
        ?>
    </div>

    <!-- JavaScript function to handle room reservation -->
    <script>
        // JavaScript function to handle room reservation
        function reserveRoom(RoomNumber) {
            // Make an AJAX request to update availability
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    // Update the button text and disable the button
                    document.querySelector("button").innerText = "Room Reserved";
                    document.querySelector("button").disabled = true;
                }
            };
            xhttp.open("POST", "oneroom.php?RoomNumber=" + RoomNumber, true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("reserve=true");
        }

    </script>
</body>

</html>
