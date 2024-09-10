<?php
require('config.php');
include('session.php');

$booksPerPage = 8;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $booksPerPage;

$searchQuery = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? intval($_GET['category']) : 0;
$tagFilter = isset($_GET['tag']) ? intval($_GET['tag']) : 0;
$authorFilter = isset($_GET['author']) ? mysqli_real_escape_string($conn, $_GET['author']) : '';

// Pobieranie dostępnych kategorii
$categories_query = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $categories_query);

$sql = "SELECT b.*, AVG(r.rating) AS avg_rating
        FROM books b
        LEFT JOIN reviews r ON b.id = r.book_id
        WHERE b.title LIKE '%$searchQuery%'";

if ($categoryFilter > 0) {
    $sql .= " AND EXISTS (SELECT 1 FROM book_categories bc WHERE bc.book_id = b.id AND bc.category_id = $categoryFilter)";
}

if ($tagFilter > 0) {
    $sql .= " AND EXISTS (SELECT 1 FROM book_tags bt WHERE bt.book_id = b.id AND bt.tag_id = $tagFilter)";
}

if ($authorFilter !== '') {
    $sql .= " AND b.author LIKE '%$authorFilter%'";
}

$sql .= " GROUP BY b.id
          ORDER BY avg_rating DESC
          LIMIT $booksPerPage OFFSET $offset";

$result = mysqli_query($conn, $sql);

$totalBooks = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM books"));
$totalPages = ceil($totalBooks / $booksPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <style>
        /* Dodaj swoje style tutaj */
    </style>
</head>
<body>
    <?php include("menu.php"); ?>
    <div class="container mt-5">
        <form method="get">
            <div class="field is-grouped">
                <div class="control is-expanded">
                    <input class="input" type="text" name="search" placeholder="Search books..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                </div>
                <div class="control">
                    <div class="select">
                        <select name="category">
                            <option value="0">All Categories</option>
                            <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                                <option value="<?php echo $category['id']; ?>" <?php if ($categoryFilter == $category['id']) echo 'selected'; ?>>
                                    <?php echo $category['name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="control">
                    <button class="button is-link" type="submit">Search</button>
                </div>
            </div>
        </form>

        <div class="columns is-multiline">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="column is-one-quarter">
                    <div class="card">
                        <div class="card-image">
                            <figure class="image is-4by3">
                                <img src="<?php echo $row['img']; ?>" alt="<?php echo $row['title']; ?>">
                            </figure>
                        </div>
                        <div class="card-content">
                            <p class="title is-4"><?php echo $row['title']; ?></p>
                            <p class="subtitle is-6">by <?php echo $row['author']; ?></p>
                            <p class="subtitle is-6">Rating: <?php echo number_format($row['avg_rating'], 2); ?></p>
                            <a href="details.php?id=<?php echo $row['id']; ?>" class="button is-link">Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <nav class="pagination is-centered">
            <ul class="pagination-list">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li><a class="pagination-link <?php if ($i == $page) echo 'is-current'; ?>" href="index.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
</body>
</html>
