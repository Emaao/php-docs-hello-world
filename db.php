<?php
try {
    // Establish the connection
    $conn = new PDO("sqlsrv:server = tcp:serveursalle.database.windows.net,1433; Database = ReservationDB", "Adamimane", "adamRGimaneAZ+");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Example query
    //$sql = "SELECT * FROM Salles"; // Replace your_table_name with the actual table name

    // Execute the query
    //$result = $conn->query($sql);

    // Fetch the results
    //while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
       // print_r($row); // Adjust as needed based on your query results
    //}
} catch (PDOException $e) {
    print("Error connecting to SQL Server.");
    die(print_r($e));
}
?>
