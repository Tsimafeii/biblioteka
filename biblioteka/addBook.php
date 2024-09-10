<?php
include "config.php";
include "session.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $year = $_POST["year"];
    $author = $_POST["author"];
    $language = $_POST["language"];
    $categories = $_POST["categories"];
    $tags = $_POST["tags"];

    if (isset($_FILES['img'])) {
        $file = $_FILES['img'];
        $fileName = $_FILES['img']['name'];
        $fileTmpName = $_FILES['img']['tmp_name'];
        $fileDestination = 'uploads/' . $fileName;
        move_uploaded_file($fileTmpName, $fileDestination);

        $insertBookQuery = "INSERT INTO books (title, description, img, year, author, language) VALUES ('$title', '$description', '$fileDestination', '$year', '$author', '$language')";

        if (mysqli_query($conn, $insertBookQuery)) {
            $bookId = mysqli_insert_id($conn);

            foreach ($categories as $categoryId) {
                $insertCategoryQuery = "INSERT INTO book_categories (book_id, category_id) VALUES ('$bookId', '$categoryId')";
                mysqli_query($conn, $insertCategoryQuery);
            }

            foreach ($tags as $tagId) {
                $insertTagQuery = "INSERT INTO book_tags (book_id, tag_id) VALUES ('$bookId', '$tagId')";
                mysqli_query($conn, $insertTagQuery);
            }

            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Book</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
            <style>
        /* Stylizacja dla obrazków */
        .img-container img {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .img-container img:hover {
            transform: scale(1.05);
        }

        /* Lista grupowa */
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
        .btn {
            margin: 12px 0;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-primary {
            background-color: #007BFF;
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            color: #fff;
        }

        .btn-primary:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        /* Układ kolumnowy */
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

        /* Tabela */
        .table {
            width: 100%;
            margin-bottom: 1.5rem;
            color: #333;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .table th,
        .table td {
            padding: 1rem;
            vertical-align: middle;
            background-color: #fff;
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .table thead th {
            background-color: #007BFF;
            color: white;
            border-bottom: none;
        }

        .table tbody tr {
            transition: background-color 0.2s ease-in-out;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
        }

        /* Historia przeglądania */
        .history-table img {
            width: 50px;
            height: auto;
            border-radius: 6px;
        }

        /* Zmiana wyglądu strony */
        body {
            background-color: #fafafa;
            font-family: 'Roboto', sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .container {
            background-color: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            margin: 30px auto;
        }

        .title {
            font-weight: 700;
            color: #222;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007BFF;
            color: white;
            border-radius: 30px;
            padding: 10px 20px;
            font-weight: 600;
        }

            </style>
</head>
<body>
    <?php include("menu.php"); ?>
    <div class="container mt-5">
        <h1 class="title">Add New Book</h1>
        <form action="addBook.php" method="post" enctype="multipart/form-data">
            <div class="field">
                <label class="label">Title</label>
                <div class="control">
                    <input class="input" type="text" name="title" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Description</label>
                <div class="control">
                    <textarea class="textarea" name="description" required></textarea>
                </div>
            </div>
            <div class="field">
                <label class="label">Year</label>
                <div class="control">
                    <input class="input" type="number" name="year" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Author</label>
                <div class="control">
                    <input class="input" type="text" name="author" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Language</label>
                <div class="control">
                    <div class="select">
                        <select name="language" required>
                            <option value="English">English</option>
                            <option value="Polish">Polish</option>
                            <option value="Spanish">Spanish</option>
                            <option value="German">German</option>
                            <option value="French">French</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="field">
                <label class="label">Categories</label>
                <div class="control">
                    <div class="select is-multiple">
                        <select name="categories[]" multiple required>
                            <?php
                            $categories_query = "SELECT * FROM categories";
                            $categories_result = mysqli_query($conn, $categories_query);

                            if ($categories_result && mysqli_num_rows($categories_result) > 0) {
                                while ($category = mysqli_fetch_assoc($categories_result)) {
                                    echo "<option value='{$category['id']}'>{$category['name']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="field">
                <label class="label">Tags</label>
                <div class="control">
                    <div class="select is-multiple">
                        <select name="tags[]" multiple required>
                            <?php
                            $tags_query = "SELECT * FROM tags";
                            $tags_result = mysqli_query($conn, $tags_query);

                            if ($tags_result && mysqli_num_rows($tags_result) > 0) {
                                while ($tag = mysqli_fetch_assoc($tags_result)) {
                                    echo "<option value='{$tag['id']}'>{$tag['name']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="field">
                <label class="label">Image</label>
                <div class="control">
                    <input class="input" type="file" name="img" accept="image/*" required>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button class="button is-link" type="submit">Add Book</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
