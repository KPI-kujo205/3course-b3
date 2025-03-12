<?php
require_once 'db_connect.php';

// Get sorting parameters
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'surname';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Validate sort column to prevent SQL injection
$allowed_columns = ['id', 'name', 'surname', 'group'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'surname';
}

// Create the SQL query with sorting
$sql = "SELECT * FROM students ORDER BY " . $sort_column . " " . ($sort_order === 'DESC' ? 'DESC' : 'ASC');
$students = $conn->query($sql)->fetchAll();

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
    <title>Список студентів</title>
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
    </style>
</head>
<body>
    <nav class="nav-top">
        <ul>
            <li><a href="index.php">Оцінки</a></li>
            <li><a href="students.php" class="active">Студенти</a></li>
        </ul>
        <div class="nav-actions">
            <a href="add_student.php" class="nav-button primary">+ Додати студента</a>
            <a href="add.php" class="nav-button secondary">+ Додати оцінку</a>
        </div>
    </nav>

    <div class="content">

        <h2>Список студентів</h2>
        
        <table>
            <tr>
                <th><a href="<?= getSortLink('surname', $sort_column, $sort_order) ?>">Прізвище</a></th>
                <th><a href="<?= getSortLink('name', $sort_column, $sort_order) ?>">Ім'я</a></th>
                <th><a href="<?= getSortLink('group', $sort_column, $sort_order) ?>">Група</a></th>
            </tr>
            <?php foreach ($students as $student): ?>
            <tr>
                <td><?= htmlspecialchars($student['surname']) ?></td>
                <td><?= htmlspecialchars($student['name']) ?></td>
                <td><?= htmlspecialchars($student['group']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html> 