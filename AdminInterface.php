<?php
session_start();

error_log("AdminInterface.php script started");

if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
    // Log an error if the admin session is not recognized
    error_log("Admin session not recognized");
    
    // Redirect to roomReser.php
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
