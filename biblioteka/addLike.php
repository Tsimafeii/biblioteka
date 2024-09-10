<?php
include("session.php");
require("config.php");

register_shutdown_function(function(){
    if (error_get_last()) {
        var_export(error_get_last());
    }
});

$book_id = $_REQUEST["book_id"];
$user_id = $_SESSION["id"];

$sql = "SELECT * FROM users_likes WHERE book_id = $book_id AND user_id = $user_id";
$result = $conn->query($sql);

if ($result === FALSE) {
    echo "Error in the SELECT query: " . $conn->error;
} else {
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $id = $row['id'];
        $sql = "DELETE FROM users_likes WHERE id = $id";
    } else {
        $sql = "INSERT INTO users_likes (user_id, book_id) VALUES ($user_id, $book_id)";
    }

    if ($conn->query($sql) !== TRUE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    } else {
        echo "Success";
    }
}

$conn->close();
?>
