<?php
// Include the database connection file
include 'db.php';

// Check if the POST parameters are set
if (isset($_POST['RoomNumber']) && isset($_POST['idResa'])) {
    // Sanitize and get the values
    $roomNumber = $_POST['RoomNumber'];
    $idResa = $_POST['idResa'];

    try {
        // Update the availability in the database
        $sqlUpdate = "UPDATE Salles SET Availability = 0 WHERE RoomNumber = :roomNumber";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':roomNumber', $roomNumber);
        $stmtUpdate->execute();

        // Log the affected room
        error_log("Room affected - RoomNumber: $roomNumber, idResa: $idResa");

        // Return a success response if needed
        http_response_code(200);
        echo "Room affected successfully.";
    } catch (PDOException $e) {
        // Log the error
        error_log("Error affecting room: " . $e->getMessage());
        // Return an error response if needed
        http_response_code(500);
        echo "Error affecting room.";
    }
} else {
    // Return a bad request response if parameters are not set
    http_response_code(400);
    echo "Bad request - RoomNumber and idResa parameters are required.";
}
?>
