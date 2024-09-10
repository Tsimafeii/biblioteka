<?php
include("session.php");
include("menu.php");
require("config.php");

$user_id = $_SESSION['id'];

$liked_books_query = "
    SELECT b.id, b.title, b.img
    FROM books b
    INNER JOIN users_likes ul ON b.id = ul.book_id
    WHERE ul.user_id = $user_id";
$liked_books_result = mysqli_query($conn, $liked_books_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Liked Books</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="css/styles.css"> <!-- Dodajemy nasz arkusz stylów -->
</head>
<body>
    <div class="container mt-5">
        <h2 class="title">My Liked Books</h2>
        <div class="columns is-multiline">
            <?php
            if ($liked_books_result && mysqli_num_rows($liked_books_result) > 0) {
                while ($book = mysqli_fetch_assoc($liked_books_result)) {
                    ?>
                    <div class="column is-one-quarter">
                        <div class="card">
                            <div class="card-image">
                                <figure class="image is-4by3">
                                    <img src="<?php echo $book['img']; ?>" alt="<?php echo $book['title']; ?>">
                                </figure>
                            </div>
                            <div class="card-content">
                                <p class="title is-4"><?php echo $book['title']; ?></p>
                                <a href="details.php?id=<?php echo $book['id']; ?>" class="button btn-primary">Details</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <p>No liked books found.</p>
                <?php
            }
            ?>
        </div>
        <a href="index.php" class="button btn-primary mt-3">Back to Main page</a>
    </div>
</body>
</html>
