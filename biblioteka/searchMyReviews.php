<?php
include("session.php");
include("menu.php");
require("config.php");

$login = $_SESSION['login'];

$reviews_query = "SELECT r.*, b.title AS book_title FROM reviews r JOIN books b ON r.book_id = b.id WHERE r.user_id = {$_SESSION['id']}";
$reviews_result = mysqli_query($conn, $reviews_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Reviews</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="css/styles.css"> <!-- Dodajemy nasz arkusz stylów -->
</head>
<body>
    <div class="container mt-5">
        <h2 class="title">My Reviews</h2>
        <ul class="list-group mt-3">
            <?php
            if ($reviews_result && mysqli_num_rows($reviews_result) > 0) {
                while ($review = mysqli_fetch_assoc($reviews_result)) {
                    ?>
                    <li class="list-group-item">
                        <p><strong>Book:</strong> <a href="details.php?id=<?php echo $review['book_id']; ?>"><?php echo $review['book_title']; ?></a></p>
                        <p><strong>Rating:</strong> <?php echo $review['rating']; ?>/10</p>
                        <p><strong>Review:</strong> <?php echo nl2br($review['content']); ?></p> <!-- Użyj nl2br, jeśli chcesz zachować formatowanie nowej linii -->
                        <p><em>Date:</em> <?php echo $review['created_at']; ?></p>
                    </li>
                    <?php
                }
            } else {
                ?>
                <li class="list-group-item">No reviews found.</li>
                <?php
            }
            ?>
        </ul>
        <a href="index.php" class="button btn-primary mt-3">Back to Main page</a>
    </div>
</body>
</html>
