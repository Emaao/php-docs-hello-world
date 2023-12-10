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

        <!-- PHP code to fetch and display reserved rooms from the Azure Storage Queue -->
        <?php
        require 'vendor/autoload.php'; // Include the Azure Storage Queue SDK

        use MicrosoftAzure\Storage\Queue\QueueRestProxy;
        use MicrosoftAzure\Storage\Common\ServiceException;

        // Azure Storage Queue settings
        $connectionString = 'DefaultEndpointsProtocol=https;AccountName=reservac;AccountKey=FFth1+WCTmbeujiIjwW6VnnPM8QowQ9UvJMcbI8Xn8X7oQ1yytzbOU2H+Qwvb4ipJgp5MrEN4mJc+AStF7w/XQ==;EndpointSuffix=core.windows.net'; // Replace with your Azure Storage connection string
        $queueName = 'queue';

        try {
            // Create a connection to the Azure Storage Queue
            $queueClient = QueueRestProxy::createQueueService($connectionString);

            // Peek at the messages in the queue
            $messages = $queueClient->peekMessages($queueName);

            foreach ($messages->getQueueMessages() as $message) {
                $messageText = $message->getMessageText();
                $reservation = json_decode($messageText, true);

                // Display the reserved room details
                echo '<div>';
                echo '<p>Room Number: ' . $reservation['RoomNumber'] . '</p>';
                echo '<p>Reserved by User</p>';
                echo '</div>';
            }
        } catch (ServiceException $e) {
            // Log and handle the exception if needed
            error_log("Error fetching messages from Azure Storage Queue: " . $e->getMessage());
            echo 'Error fetching reservations.';
        }
        ?>
    </div>
</body>

</html>

<?php
include 'db.php'; // Include the database connection file

// Check if the user is an admin
if ($_SESSION['isAdmin'] != 1) {
    // Redirect non-admin users to the roomReser.php
    header("Location: roomReser.php");
    exit();
}

$sql = "SELECT * FROM Salles";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- ... (existing head content) ... -->
</head>
<body>
    <div>
        <h1>Welcome to Admin Interface</h1>
        <p>Rooms awaiting action:</p>

        <!-- PHP code to fetch and display rooms -->
        <?php
        foreach ($messages->getQueueMessages() as $message) {
            $messageText = $message->getMessageText();
            $reservation = json_decode($messageText, true);
        
            // Display the reserved room details
            echo '<div>';
            echo '<p>Room Number: ' . $reservation['RoomNumber'] . '</p>';
            echo '<p>ID Reservation: ' . $reservation['idResa'] . '</p>';
            
            // Add buttons for actions (affecte and ignore)
            echo '<button onclick="affectRoom(' . $reservation['RoomNumber'] . ',' . $reservation['idResa'] . ')">Affecte</button>';
            echo '<button onclick="ignoreRoom(' . $reservation['RoomNumber'] . ',' . $reservation['idResa'] . ')">Ignore</button>';
            
            echo '</div>';
        }
        
        ?>
    </div>

    <!-- JavaScript function to handle room reservation -->
    <script>
        // JavaScript function to handle room affecting
        // JavaScript function to handle room affecting
        function affectRoom(RoomNumber, idReservation) {
            // Make an AJAX request to update availability
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        // Update the page or show a message if needed
                        console.log("Room affected successfully.");
                        // Reload the page to update the displayed rooms
                        location.reload();
                    } else {
                        // Log an error if the request is not successful
                        console.error("Error affecting room");
                    }
                }
            };
            xhttp.open("POST", "affectRoom.php", true); // Update the URL accordingly
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("RoomNumber=" + RoomNumber + "&idResa=" + idReservation);
        }
        // JavaScript function to handle ignoring a room
        function ignoreRoom(RoomNumber, idReservation) {
            // Implement ignore logic here (if needed)
            console.log("Room ignored");
        }
    </script>
</body>
</html>

