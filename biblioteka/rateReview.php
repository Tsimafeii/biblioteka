<?php
include("session.php");
require("config.php");

if (isset($_GET['type']) && isset($_GET['review_id']) && isset($_GET['book_id'])) {
    $type = $_GET['type'];
    $review_id = intval($_GET['review_id']);
    $user_id = $_SESSION['id'];
    $book_id = intval($_GET['book_id']);

    $check_query = "SELECT * FROM review_ratings WHERE review_id = $review_id AND user_id = $user_id";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $update_query = "UPDATE review_ratings SET type = '$type' WHERE review_id = $review_id AND user_id = $user_id";
        mysqli_query($conn, $update_query);
    } else {
        $insert_query = "INSERT INTO review_ratings (review_id, user_id, type) VALUES ($review_id, $user_id, '$type')";
        mysqli_query($conn, $insert_query);
    }

    header("Location: details.php?id=$book_id");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
