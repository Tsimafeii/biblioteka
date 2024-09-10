<?php
include("session.php");
include("menu.php");
require("config.php");

// Fetch top authors with most likes
$authors_query = "
    SELECT b.author, COUNT(ul.id) AS likes_count
    FROM books b
    INNER JOIN users_likes ul ON b.id = ul.book_id
    GROUP BY b.author
    ORDER BY likes_count DESC
    LIMIT 5";
$authors_result = mysqli_query($conn, $authors_query);

// Fetch top users with most reviews
$users_query = "
    SELECT u.login, COUNT(r.id) AS reviews_count
    FROM users u
    INNER JOIN reviews r ON u.id = r.user_id
    GROUP BY u.login
    ORDER BY reviews_count DESC
    LIMIT 5";
$users_result = mysqli_query($conn, $users_query);

// Fetch top tags
$tags_query = "
    SELECT t.name, COUNT(bt.book_id) AS tag_count
    FROM tags t
    INNER JOIN book_tags bt ON t.id = bt.tag_id
    GROUP BY t.name
    ORDER BY tag_count DESC
    LIMIT 5";
$tags_result = mysqli_query($conn, $tags_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Statistics</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">

</head>
<body>
    <div class="container mt-5">
        <h2 class="title">Statistics</h2>

        <div class="columns">
            <div class="column is-one-third">
                <h3 class="title is-4">Top Authors by Likes</h3>
                <table class="table is-striped">
                    <thead>
                        <tr>
                            <th>Author</th>
                            <th>Likes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($author = mysqli_fetch_assoc($authors_result)): ?>
                            <tr>
                                <td><?php echo $author['author']; ?></td>
                                <td><?php echo $author['likes_count']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="column is-one-third">
                <h3 class="title is-4">Top Users by Reviews</h3>
                <table class="table is-striped">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Reviews</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                            <tr>
                                <td><?php echo $user['login']; ?></td>
                                <td><?php echo $user['reviews_count']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="column is-one-third">
                <h3 class="title is-4">Top Tags</h3>
                <table class="table is-striped">
                    <thead>
                        <tr>
                            <th>Tag</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($tag = mysqli_fetch_assoc($tags_result)): ?>
                            <tr>
                                <td><?php echo $tag['name']; ?></td>
                                <td><?php echo $tag['tag_count']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <a href="index.php" class="button is-primary mt-3">Back to Main page</a>
    </div>
</body>
</html>
