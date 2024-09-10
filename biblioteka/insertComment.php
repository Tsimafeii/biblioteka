<?php
include("session.php");
require("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST['book_id'];
    $nick = mysqli_real_escape_string($conn, $_POST['nick']);
    $rating = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $parent_comment_id = isset($_POST['parent_comment_id']) ? intval($_POST['parent_comment_id']) : NULL;

    $user_id = $_SESSION['id'];
    $insert_query = "INSERT INTO comments (nick, info, rating, book_id, user_id, parent_comment_id, date) VALUES ('$nick', '$comment', '$rating', '$book_id', '$user_id', $parent_comment_id, NOW())";

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
