<?php
require_once 'db_connect.php';

if (!isset($_GET['id'])) {
    die("ID не вказано");
}

$id = $_GET['id'];

if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    try {
        $stmt = $conn->prepare("DELETE FROM student_grades WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: index.php");
        exit();
    } catch(PDOException $e) {
        echo "<div style='color: red;'>Помилка: " . $e->getMessage() . "</div>";
    }
}

// Get grade info for confirmation
$stmt = $conn->prepare("
    SELECT sg.*, s.name, s.surname, s.group 
    FROM student_grades sg 
    JOIN students s ON sg.student_id = s.id 
    WHERE sg.id = ?
");
$stmt->execute([$id]);
$grade = $stmt->fetch();

if (!$grade) {
    die("Оцінку не знайдено");
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Видалити оцінку</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .confirmation { margin: 20px 0; }
        .buttons { margin-top: 20px; }
        .buttons a { 
            padding: 10px 20px; 
            text-decoration: none; 
            margin-right: 10px;
            display: inline-block;
        }
        .delete { background-color: #ff4444; color: white; }
        .cancel { background-color: #666; color: white; }
    </style>
</head>
<body>
    <h2>Видалити оцінку</h2>
    <div class="confirmation">
        <p>Ви впевнені, що хочете видалити наступну оцінку?</p>
        <p>
            <strong>Студент:</strong> <?= htmlspecialchars($grade['surname'] . ' ' . $grade['name']) ?><br>
            <strong>Група:</strong> <?= htmlspecialchars($grade['group']) ?><br>
            <strong>Предмет:</strong> <?= htmlspecialchars($grade['subject']) ?><br>
            <strong>Оцінка:</strong> <?= $grade['grade'] ?><br>
            <strong>Викладач:</strong> <?= htmlspecialchars($grade['teacher']) ?>
        </p>
    </div>
    <div class="buttons">
        <a href="delete.php?id=<?= $id ?>&confirm=yes" class="delete">Так, видалити</a>
        <a href="index.php" class="cancel">Скасувати</a>
    </div>
</body>
</html> 