<?php
require('config.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = mysqli_real_escape_string($conn, $_POST['login']);
    $password = md5($_POST['pass']);

    $sql = "SELECT * FROM users WHERE login='$login' AND pass='$password'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['login'] = $row['login'];
        $_SESSION['id'] = $row['id'];
        $_SESSION['role'] = $row['role'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid login or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
<div class="container mt-5">
    <?php if (isset($error)): ?>
        <div class="notification is-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <form method="post">
        <div class="field">
            <label class="label">Login</label>
            <div class="control">
                <input class="input" type="text" name="login" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Password</label>
            <div class="control">
                <input class="input" type="password" name="pass" required>
            </div>
        </div>
        <div class="field">
            <div class="control">
                <button class="button is-link" type="submit">Login</button>
            </div>
        </div>
    </form>
</div>
</body>
</html>
