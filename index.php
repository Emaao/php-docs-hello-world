<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation de Salles - Connexion</title>
    <link rel="stylesheet" href="styleAuthenti.css">
</head>

<body>

    <?php
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        include 'db.php'; // Include the database connection file

        $username = $_POST['username'];
        $password = $_POST['password'];

        try {
            // Check if the user exists and is an admin
            $sql = "SELECT * FROM _user WHERE NameUser = :username AND pass = :password AND isAdmin = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['isAdmin'] = 1;
                header("Location: AdminInterface.php");
                exit();
            } else {
                // Invalid credentials
                header("Location: roomReser.php");
                exit();
            }
        } catch (PDOException $e) {
            // Log the error
            error_log("Error checking admin credentials: " . $e->getMessage());
            // Return an error response if needed
            http_response_code(500);
            exit();
        }
    } else {
        // Invalid request method
        header("Location: roomReser.php");
        exit();
    }
    ?>

    <form action="process_login.php" method="post">
        <h2>Connexion</h2>
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Se Connecter</button>
    </form>

</body>

</html>

