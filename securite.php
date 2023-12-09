<?php
// Assuming you have a function to authenticate the user
// TODO: Implement user authentication function

// Check if the user is an admin
if ($isAdmin) {
    // Connect to your Azure Storage Queue
    $connectionString = "DefaultEndpointsProtocol=https;AccountName=reservac;AccountKey=FFth1+WCTmbeujiIjwW6VnnPM8QowQ9UvJMcbI8Xn8X7oQ1yytzbOU2H+Qwvb4ipJgp5MrEN4mJc+AStF7w/XQ==;EndpointSuffix=core.windows.net";
    $queueName = "queue";
    
    try {
        // Create a queue client
        $queueClient = new WindowsAzure\Common\Services\Queue\QueueRestProxy($connectionString);

        // Retrieve messages from the queue
        $options = new WindowsAzure\Common\Services\Queue\Models\QueueServiceOptions();
        $result = $queueClient->listMessages($queueName, $options);

        // Display room details from the messages
        foreach ($result->getQueueMessages() as $message) {
            $messageText = $message->getMessageText();

            // TODO: Parse the message and display room details
            // Example: $roomNumber = json_decode($messageText)->roomNumber;
            // TODO: Display room details and buttons
            echo "<p>Room Number: $roomNumber</p>";
            echo '<button onclick="affectRoom(\'' . $roomNumber . '\')">Affect Room</button>';
            echo '<button onclick="ignoreRoom(\'' . $roomNumber . '\')">Ignore Room</button>';
        }
    } catch (ServiceException $e) {
        echo "Error fetching messages from the queue: " . $e->getMessage();
    }
} else {
    // Redirect to login page or unauthorized access page
    header("Location: login.html");
    exit();
}
?>
