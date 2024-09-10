<?php
include("session.php");
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['book_id']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') {
        $book_id = intval($_POST['book_id']);

        // Usunięcie powiązanych danych (lajki, recenzje, kategorie, tagi)
        $deleteLikesQuery = "DELETE FROM users_likes WHERE book_id = $book_id";
        mysqli_query($conn, $deleteLikesQuery);

        $deleteReviewsQuery = "DELETE FROM reviews WHERE book_id = $book_id";
        mysqli_query($conn, $deleteReviewsQuery);

        $deleteCategoriesQuery = "DELETE FROM book_categories WHERE book_id = $book_id";
        mysqli_query($conn, $deleteCategoriesQuery);

        $deleteTagsQuery = "DELETE FROM book_tags WHERE book_id = $book_id";
        mysqli_query($conn, $deleteTagsQuery);

        // Usunięcie książki
        $deleteBookQuery = "DELETE FROM books WHERE id = $book_id";
        if (mysqli_query($conn, $deleteBookQuery)) {
            echo "Success";
        } else {
            echo "Error deleting book: " . mysqli_error($conn);
        }
    } else {
        echo "Error: Invalid request or insufficient permissions.";
    }
} else {
    echo "Error: Invalid request method.";
}

mysqli_close($conn);
?>
