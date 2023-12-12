<?php
session_start();
include 'db.php';

error_log("adminAffect.php script started");

if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
    // Log an error if the admin session is not recognized
    error_log("Admin session not recognized");

    // Redirect to roomReser.php or another appropriate page
    header("Location: roomReser.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['affectButton'])) {
    try {
        // Get RoomNumber from the form submission
        $roomNumber = $_POST['RoomNumber'];

        // Update the availability in the database
        // Assuming you have a database connection in db.php
        include 'db.php';

        $sqlUpdate = "UPDATE Salles SET Availability = 0 WHERE RoomNumber = :roomNumber";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':roomNumber', $roomNumber);
        $stmtUpdate->execute();

        // Check the affected rows to ensure the update occurred
        $affectedRows = $stmtUpdate->rowCount();

        if ($affectedRows > 0) {
            echo 'Room availability updated successfully.';
        } else {
            echo 'Failed to update room availability.';
        }
    } catch (PDOException $e) {
        // Log the error
        error_log("Error updating room availability: " . $e->getMessage());
        echo 'Internal Server Error';
    }
} else {
    echo 'Invalid request.';
}
?>
