<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $stmt = $conn->prepare("INSERT INTO students (name, surname, `group`) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['surname'],
            $_POST['group']
        ]);
        echo "<div style='color: green;'>Студента успішно додано!</div>";
    } catch(PDOException $e) {
        echo "<div style='color: red;'>Помилка: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Додати студента</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 300px; padding: 5px; }
        button { padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { color: #666; text-decoration: none; margin-right: 15px; }
        .nav-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="nav-links">
        <a href="index.php">← Повернутися до списку оцінок</a>
        <a href="students.php">Список студентів</a>
    </div>

    <h2>Додати нового студента</h2>
    <form method="POST">
        <div class="form-group">
            <label for="surname">Прізвище:</label>
            <input type="text" name="surname" required>
        </div>

        <div class="form-group">
            <label for="name">Ім'я:</label>
            <input type="text" name="name" required>
        </div>

        <div class="form-group">
            <label for="group">Група:</label>
            <input type="text" name="group" required>
        </div>

        <button type="submit">Додати студента</button>
    </form>
</body>
</html> 