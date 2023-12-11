<?php
session_start();
include 'db.php';
error_log("AdminInterface.php script started");

if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
    // Log an error if the admin session is not recognized
    error_log("Admin session not recognized");

    // Redirect to roomReser.php or another appropriate page
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
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>
    <div>
        <h1>Admin Room Interface</h1>
        <p>Rooms reserved by users:</p>

        <!-- PHP code to fetch and display reserved rooms from the Azure Storage Queue -->
        <?php
        
        echo 'hello world';
        
        ?>
    </div>

    <!-- JavaScript code to handle room affecting -->
    <script>
        $(document).ready(function () {
            // Handle click event for the "Affect" button
            $('.affectButton').on('click', function () {
                var roomNumber = $(this).data('roomNumber');
                
                // Make an AJAX request to adminAffect.php
                $.post('adminAffect.php', { RoomNumber: roomNumber }, function (data) {
                    alert(data); // Display the response (you can replace this with actual UI update logic)
                });
            });
        });
    </script>
</body>

</html>
