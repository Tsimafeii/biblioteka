<?php
include "config.php";
include "session.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $book_query = "SELECT * FROM books WHERE id = $id";
    $book_result = mysqli_query($conn, $book_query);
    if ($book_result && mysqli_num_rows($book_result) > 0) {
        $book = mysqli_fetch_assoc($book_result);
    } else {
        header("Location: index.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $title = $_POST["title"];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $year = $_POST["year"];
    $author = $_POST["author"];
    $language = $_POST["language"];
    $categories = $_POST["categories"];
    $tags = $_POST["tags"];

    $updateBookQuery = "UPDATE books SET title = '$title', description = '$description', year = $year, author = '$author', language = '$language' WHERE id = $id";

    if (isset($_FILES['img']) && $_FILES['img']['size'] > 0) {
        $fileName = $_FILES['img']['name'];
        $fileTmpName = $_FILES['img']['tmp_name'];
        $fileDestination = 'uploads/' . $fileName;

        // Dodatkowe informacje diagnostyczne
        if (!is_uploaded_file($fileTmpName)) {
            echo "The file was not uploaded via HTTP POST.";
            exit();
        }

        if (!move_uploaded_file($fileTmpName, $fileDestination)) {
            echo "Error uploading image. File destination: $fileDestination";
            exit();
        }

        // Zapisz tylko nazwę pliku w bazie danych
        $updateBookQuery .= ", img = '$fileName'";
    }

    if (mysqli_query($conn, $updateBookQuery)) {
        $deleteCategoriesQuery = "DELETE FROM book_categories WHERE book_id = $id";
        mysqli_query($conn, $deleteCategoriesQuery);

        foreach ($categories as $categoryId) {
            $insertCategoryQuery = "INSERT INTO book_categories (book_id, category_id) VALUES ('$id', '$categoryId')";
            mysqli_query($conn, $insertCategoryQuery);
        }

        $deleteTagsQuery = "DELETE FROM book_tags WHERE book_id = $id";
        mysqli_query($conn, $deleteTagsQuery);

        foreach ($tags as $tagId) {
            $insertTagQuery = "INSERT INTO book_tags (book_id, tag_id) VALUES ('$id', '$tagId')";
            mysqli_query($conn, $insertTagQuery);
        }

        header("Location: details.php?id=$id");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Book</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>
<body>
    <?php include("menu.php"); ?>
    <div class="container mt-5">
        <h1 class="title">Update Book</h1>
        <form action="updateBookForm.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
            <div class="field">
                <label class="label">Title</label>
                <div class="control">
                    <input class="input" type="text" name="title" value="<?php echo $book['title']; ?>" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Description</label>
                <div class="control">
                    <textarea class="textarea" name="description" required><?php echo $book['description']; ?></textarea>
                </div>
            </div>
            <div class="field">
                <label class="label">Year</label>
                <div class="control">
                    <input class="input" type="number" name="year" value="<?php echo $book['year']; ?>" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Author</label>
                <div class="control">
                    <input class="input" type="text" name="author" value="<?php echo $book['author']; ?>" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Language</label>
                <div class="control">
                    <div class="select">
                        <select name="language" required>
                            <option value="English" <?php if ($book['language'] == 'English') echo 'selected'; ?>>English</option>
                            <option value="Polish" <?php if ($book['language'] == 'Polish') echo 'selected'; ?>>Polish</option>
                            <option value="Spanish" <?php if ($book['language'] == 'Spanish') echo 'selected'; ?>>Spanish</option>
                            <option value="German" <?php if ($book['language'] == 'German') echo 'selected'; ?>>German</option>
                            <option value="French" <?php if ($book['language'] == 'French') echo 'selected'; ?>>French</option>
                            <option value="Other" <?php if ($book['language'] == 'Other') echo 'selected'; ?>>Other</option>
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
                            $book_categories = mysqli_query($conn, "SELECT category_id FROM book_categories WHERE book_id = $id");
                            $selected_categories = [];
                            while ($cat = mysqli_fetch_assoc($book_categories)) {
                                $selected_categories[] = $cat['category_id'];
                            }

                            if ($categories_result && mysqli_num_rows($categories_result) > 0) {
                                while ($category = mysqli_fetch_assoc($categories_result)) {
                                    $selected = in_array($category['id'], $selected_categories) ? "selected" : "";
                                    echo "<option value='{$category['id']}' $selected>{$category['name']}</option>";
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
                            $book_tags = mysqli_query($conn, "SELECT tag_id FROM book_tags WHERE book_id = $id");
                            $selected_tags = [];
                            while ($tag = mysqli_fetch_assoc($book_tags)) {
                                $selected_tags[] = $tag['tag_id'];
                            }

                            if ($tags_result && mysqli_num_rows($tags_result) > 0) {
                                while ($tag = mysqli_fetch_assoc($tags_result)) {
                                    $selected = in_array($tag['id'], $selected_tags) ? "selected" : "";
                                    echo "<option value='{$tag['id']}' $selected>{$tag['name']}</option>";
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
                    <input class="input" type="file" name="img" accept="image/*">
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button class="button is-link" type="submit">Update Book</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
