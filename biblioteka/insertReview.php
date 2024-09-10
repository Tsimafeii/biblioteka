<?php
include("session.php");
require("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = intval($_POST['book_id']);
    $user_id = $_SESSION['id'];
    $rating = intval($_POST['rating']);
    $review = mysqli_real_escape_string($conn, $_POST['comment']);

    // Sprawdzenie, czy wszystkie dane są prawidłowe
    if (empty($review)) {
        echo "Error: Review content cannot be empty.";
        exit();
    }

    if ($rating < 1 || $rating > 10) {
        echo "Error: Rating must be between 1 and 10.";
        exit();
    }

    // Wstawienie recenzji do bazy danych
    $insert_query = "INSERT INTO reviews (user_id, book_id, content, rating, created_at) VALUES ('$user_id', '$book_id', '$review', '$rating', NOW())";

    if (mysqli_query($conn, $insert_query)) {
        header("Location: details.php?id=$book_id");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: index.php");
    exit();
}
?>
