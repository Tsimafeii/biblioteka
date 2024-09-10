<?php
include("session.php");
include("config.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Sprawdzenie, czy książka jest już w historii przeglądania dla tego użytkownika
    $user_id = $_SESSION['id'];
    $check_history_query = "SELECT * FROM history WHERE book_id = $id AND user_id = $user_id";
    $check_history_result = mysqli_query($conn, $check_history_query);

    if (mysqli_num_rows($check_history_result) == 0) {
        // Jeśli książka nie jest w historii przeglądania, dodaj ją
        $insert_history_query = "INSERT INTO history (book_id, user_id, view_date) VALUES ($id, $user_id, NOW())";
        mysqli_query($conn, $insert_history_query);
    }

    // Sprawdzenie, czy książka została polubiona przez użytkownika
    $like_check_query = "SELECT * FROM users_likes WHERE book_id = $id AND user_id = $user_id";
    $like_check_result = mysqli_query($conn, $like_check_query);
    $is_liked = mysqli_num_rows($like_check_result) > 0;

    // Pobieranie szczegółów książki z bazy danych
    $query = "SELECT * FROM books WHERE id = $id";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error: " . mysqli_error($conn));
    }

    $row = mysqli_fetch_assoc($result);
    $image_path = "" . $row['img'];

} else {
    die("Error: No book ID provided.");
}

// Pobieranie kategorii książki
$current_book_categories = [];
$categories_query = "SELECT name FROM categories c JOIN book_categories bc ON c.id = bc.category_id WHERE bc.book_id = $id";
$categories_result = mysqli_query($conn, $categories_query);
while ($category = mysqli_fetch_assoc($categories_result)) {
    $current_book_categories[] = $category['name'];
}

// Pobieranie średniej oceny
$average_query = "SELECT AVG(rating) AS avg_rating FROM reviews WHERE book_id = $id";
$average_result = mysqli_query($conn, $average_query);
$avg_rating = 0;
if ($average_result && mysqli_num_rows($average_result) > 0) {
    $average_row = mysqli_fetch_assoc($average_result);
    $avg_rating = $average_row['avg_rating'];
}

// Pobieranie oceny użytkownika
$user_rating_query = "SELECT rating FROM reviews WHERE book_id = $id AND user_id = $user_id";
$user_rating_result = mysqli_query($conn, $user_rating_query);
$user_rating = 0;
if ($user_rating_result && mysqli_num_rows($user_rating_result) > 0) {
    $user_rating_row = mysqli_fetch_assoc($user_rating_result);
    $user_rating = $user_rating_row['rating'];
}

// Pobieranie recenzji z dołączeniem informacji o użytkowniku (nickname)
$reviews_query = "
    SELECT r.*, u.login AS nickname
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.book_id = $id";
$reviews_result = mysqli_query($conn, $reviews_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Details</title>
    <link rel="stylesheet" href="/biblioteka/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <style>
        /* Stylizacja dla obrazków */
        .img-container img {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
            max-width: 100%;
            height: auto;
        }

        .img-container img:hover {
            transform: scale(1.05);
        }

        /* Stylizacja listy */
        .list-group-item {
            border-bottom: 1px solid #e0e0e0;
            background: none;
            padding: 0.75rem 0;
            transition: background-color 0.3s ease;
        }

        .list-group-item:hover {
            background-color: #f7f7f7;
        }

        .list-group-item strong {
            color: #444;
        }

        /* Stylizacja przycisków */
        .btn-primary {
            background-color: #007BFF;
            color: white;
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        /* Stylizacja kolumn */
        .columns {
            display: flex;
            flex-wrap: wrap;
            margin: -10px;
        }

        .column {
            padding: 10px;
        }

        .column img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
        }

        /* Sekcja komentarzy */
        .comments-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .comments-section h2 {
            color: #555;
            font-weight: 700;
        }

        /* Powiązane książki */
        .related-books .column {
            margin-bottom: 20px;
        }

        .related-books .column img {
            transition: transform 0.3s ease-in-out;
        }

        .related-books .column img:hover {
            transform: scale(1.08);
        }

        /* Stylizacja ikony serca */
        .fav-icon {
            width: 70px;
            height: 70px;
            max-width: 70px;
            max-height: 70px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include("menu.php"); ?>
    <div class="container mt-5">
        <div class="columns">
            <div class="column is-half">
                <div class="img-container">
                    <img src="<?php echo $image_path; ?>" alt="Book Image">
                </div>
            </div>
            <div class="column is-half">
                <h1 class="title"><?php echo $row['title']; ?></h1>
                <img id="fav-icon" class="fav-icon" data-book-id="<?php echo $id; ?>"
                     src="uploads/<?php echo $is_liked ? 'heart_red.jpg' : 'heart_gray.png'; ?>" alt="Add to Favorites">
                <ul class="list-group">
                    <li class="list-group-item"><strong>Description:</strong> <?php echo $row['description']; ?></li>
                    <li class="list-group-item"><strong>Year:</strong> <?php echo $row['year']; ?></li>
                    <li class="list-group-item"><strong>Category:</strong> <?php echo implode(', ', $current_book_categories); ?></li>
                    <li class="list-group-item"><strong>Author:</strong> <?php echo $row['author']; ?></li>
                    <li class="list-group-item"><strong>Language:</strong> <?php echo $row['language']; ?></li>
                    <li class="list-group-item"><strong>Your Rating:</strong> <?php echo $user_rating > 0 ? $user_rating : 'Not rated yet'; ?>/10</li>
                    <?php if ($avg_rating > 0): ?>
                        <li class="list-group-item"><strong>Average Rating:</strong> <?php echo number_format($avg_rating, 2); ?>/10</li>
                    <?php else: ?>
                        <li class="list-group-item"><strong>No ratings yet.</strong></li>
                    <?php endif; ?>
                </ul>
                <div class="mt-4">
                    <h3 class="title is-4">Add a Review</h3>
                    <form method="POST" action="insertReview.php">
                        <input type="hidden" name="book_id" value="<?php echo $id; ?>">
                        <div class="field">
                            <label class="label">Nickname: <?php echo $_SESSION['login']; ?></label>
                            <input type="hidden" class="input" name="nickname" value="<?php echo $_SESSION['login']; ?>" required>
                        </div>
                        <div class="field">
                            <label class="label" for="rating">Your Rating (1-10):</label>
                            <input type="number" class="input" id="rating" name="rating" min="1" max="10" required>
                        </div>
                        <div class="field">
                            <label class="label" for="comment">Your Review:</label>
                            <textarea class="textarea" id="comment" name="comment" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="button btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="comments-section">
            <h2 class="title is-4">Reviews</h2>
            <?php if ($reviews_result && mysqli_num_rows($reviews_result) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nickname</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($review = mysqli_fetch_assoc($reviews_result)): ?>
                            <tr>
                                <td><?php echo $review['nickname']; ?></td> <!-- Wyświetlanie nickname -->
                                <td><?php echo $review['rating']; ?>/10</td>
                                <td><?php echo nl2br($review['content']); ?></td>
                                <td><?php echo $review['created_at']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No reviews yet.</p>
            <?php endif; ?>
        </div>
    </div>
    <a href="index.php" class="button btn-primary mt-3">Back to Main page</a>
    <?php
    if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        echo '<div class="mt-4">
            <a href="updateBookForm.php?id=' . $id . '" class="button btn-primary">Update data</a>
        </div>';
    }
    if (isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        echo '<div class="mt-4">
            <button class="button btn-primary" id="deleteBookBtn" data-bs-toggle="modal">Delete book</button>
        </div>';
    }
    ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#fav-icon").on("click", function() {
                const bookId = $(this).data("book-id");
                const icon = $(this);

                $.post(
                    "addLike.php",
                    { book_id: bookId },
                    function(data) {
                        if (data.trim().includes("Success")) {
                            const currentSrc = icon.attr("src");
                            const newSrc = currentSrc.includes("uploads/heart_gray.png") ? "uploads/heart_red.jpg" : "uploads/heart_gray.png";
                            icon.attr("src", newSrc);
                        } else {
                            alert("Error adding to favorites.");
                        }
                    }
                );
            });

            $("#deleteBookBtn").on("click", function() {
                if (confirm("Are you sure you want to delete this book?")) {
                    $.post(
                        "delete.php",
                        { book_id: <?php echo $id; ?> },
                        function(data) {
                            if (data.trim().includes("Success")) {
                                alert("Book deleted successfully");
                                window.location.href = "index.php";
                            } else {
                                alert("Error deleting book");
                            }
                        }
                    );
                }
            });
        });
    </script>
</body>
</html>
