<?php
require_once 'db_connect.php';

if (!isset($_GET['id'])) {
    die("ID не вказано");
}

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $stmt = $conn->prepare("UPDATE student_grades SET subject = ?, ticket_number = ?, grade = ?, teacher = ?, student_id = ? WHERE id = ?");
        $stmt->execute([
            $_POST['subject'],
            $_POST['ticket_number'],
            $_POST['grade'],
            $_POST['teacher'],
            $_POST['student_id'],
            $id
        ]);
        echo "<div style='color: green;'>Оцінку успішно оновлено!</div>";
    } catch(PDOException $e) {
        echo "<div style='color: red;'>Помилка: " . $e->getMessage() . "</div>";
    }
}

// Get current grade data
$stmt = $conn->prepare("SELECT * FROM student_grades WHERE id = ?");
$stmt->execute([$id]);
$grade = $stmt->fetch();

if (!$grade) {
    die("Оцінку не знайдено");
}

// Get list of students for dropdown
$students = $conn->query("SELECT * FROM students ORDER BY surname, name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Редагувати оцінку</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select { width: 300px; padding: 5px; }
        button { padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <h2>Редагувати оцінку</h2>
    <form method="POST">
        <div class="form-group">
            <label for="student_id">Студент:</label>
            <select name="student_id" required>
                <?php foreach ($students as $student): ?>
                    <option value="<?= $student['id'] ?>" <?= $student['id'] == $grade['student_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($student['surname'] . ' ' . $student['name'] . ' (' . $student['group'] . ')') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="subject">Предмет:</label>
            <input type="text" name="subject" value="<?= htmlspecialchars($grade['subject']) ?>" required>
        </div>

        <div class="form-group">
            <label for="ticket_number">Номер квитка:</label>
            <input type="number" name="ticket_number" value="<?= $grade['ticket_number'] ?>" required min="1">
        </div>

        <div class="form-group">
            <label for="grade">Оцінка:</label>
            <input type="number" name="grade" value="<?= $grade['grade'] ?>" required min="1" max="100">
        </div>

        <div class="form-group">
            <label for="teacher">Викладач:</label>
            <input type="text" name="teacher" value="<?= htmlspecialchars($grade['teacher']) ?>" required>
        </div>

        <button type="submit">Зберегти зміни</button>
    </form>
    <p><a href="index.php">Повернутися до списку</a></p>
</body>
</html> 