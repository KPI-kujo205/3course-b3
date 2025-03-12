<?php
require_once 'db_connect.php';

// Get sorting parameters
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Validate sort column to prevent SQL injection
$allowed_columns = ['id', 'subject', 'ticket_number', 'grade', 'teacher', 'name', 'surname', 'group'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'id';
}

// Create the SQL query with sorting
$sql = "
    SELECT sg.*, s.name, s.surname, s.group 
    FROM student_grades sg 
    JOIN students s ON sg.student_id = s.id 
    ORDER BY " . $sort_column . " " . ($sort_order === 'DESC' ? 'DESC' : 'ASC');

$grades = $conn->query($sql)->fetchAll();

// Function to create sort links
function getSortLink($column, $current_sort, $current_order) {
    $new_order = ($current_sort === $column && $current_order === 'ASC') ? 'DESC' : 'ASC';
    return "?sort=" . $column . "&order=" . $new_order;
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Список оцінок студентів</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        th a { color: #000; text-decoration: none; }
        th a:hover { text-decoration: underline; }
        .actions { white-space: nowrap; }
        .actions a { margin-right: 10px; }
        .nav-top {
            background-color: #333;
            padding: 10px 20px;
            margin: -20px -20px 20px -20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-top ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            gap: 20px;
        }
        .nav-top ul li {
            display: inline;
        }
        .nav-top ul li a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
        }
        .nav-top ul li a:hover {
            background-color: #555;
        }
        .nav-top ul li a.active {
            background-color: #4CAF50;
        }
        .nav-actions {
            display: flex;
            gap: 10px;
        }
        .nav-button {
            padding: 5px 15px;
            border-radius: 3px;
            color: white;
            text-decoration: none;
            font-size: 14px;
        }
        .nav-button.primary {
            background-color: #4CAF50;
        }
        .nav-button.secondary {
            background-color: #2196F3;
        }
        .nav-button:hover {
            opacity: 0.9;
        }
        .content {
            margin-top: 20px;
        }
        .edit { color: #2196F3; }
        .delete { color: #f44336; }
    </style>
</head>
<body>
    <nav class="nav-top">
        <ul>
            <li><a href="index.php" class="active">Оцінки</a></li>
            <li><a href="students.php">Студенти</a></li>
        </ul>
        <div class="nav-actions">
            <a href="add.php" class="nav-button primary">+ Додати оцінку</a>
            <a href="add_student.php" class="nav-button secondary">+ Додати студента</a>
        </div>
    </nav>

    <div class="content">
        <h2>Список оцінок студентів</h2>
        
        <table>
            <tr>
                <th><a href="<?= getSortLink('surname', $sort_column, $sort_order) ?>">Прізвище</a></th>
                <th><a href="<?= getSortLink('name', $sort_column, $sort_order) ?>">Ім'я</a></th>
                <th><a href="<?= getSortLink('group', $sort_column, $sort_order) ?>">Група</a></th>
                <th><a href="<?= getSortLink('subject', $sort_column, $sort_order) ?>">Предмет</a></th>
                <th><a href="<?= getSortLink('ticket_number', $sort_column, $sort_order) ?>">Номер квитка</a></th>
                <th><a href="<?= getSortLink('grade', $sort_column, $sort_order) ?>">Оцінка</a></th>
                <th><a href="<?= getSortLink('teacher', $sort_column, $sort_order) ?>">Викладач</a></th>
                <th>Дії</th>
            </tr>
            <?php foreach ($grades as $grade): ?>
            <tr>
                <td><?= htmlspecialchars($grade['surname']) ?></td>
                <td><?= htmlspecialchars($grade['name']) ?></td>
                <td><?= htmlspecialchars($grade['group']) ?></td>
                <td><?= htmlspecialchars($grade['subject']) ?></td>
                <td><?= $grade['ticket_number'] ?></td>
                <td><?= $grade['grade'] ?></td>
                <td><?= htmlspecialchars($grade['teacher']) ?></td>
                <td class="actions">
                    <a href="edit.php?id=<?= $grade['id'] ?>" class="edit">Редагувати</a>
                    <a href="delete.php?id=<?= $grade['id'] ?>" class="delete">Видалити</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
