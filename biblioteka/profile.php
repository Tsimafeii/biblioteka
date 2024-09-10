<?php
include("session.php");
include("config.php");

// Pobieranie danych użytkownika
$user_id = $_SESSION["id"];
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include("menu.php"); ?>
    <div class="container mt-5">
        <div class="columns">
            <div class="column is-half is-offset-one-quarter">
                <div class="box">
                    <h1 class="title">User Profile</h1>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Username:</strong> <?php echo $user['login']; ?></li>
                        <li class="list-group-item"><strong>Email:</strong> <?php echo $user['email']; ?></li>
                        <li class="list-group-item"><strong>Role:</strong> <?php echo $user['role']; ?></li>
                        <!-- Dodaj więcej informacji o użytkowniku w razie potrzeby -->
                    </ul>
                    <a href="index.php" class="button btn-primary mt-3">Back to Main page</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
