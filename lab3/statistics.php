<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статистика сайту</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .stats-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .search-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
        }
        .stat-item {
            margin: 10px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        form {
            margin: 20px 0;
        }
        input[type="text"], input[type="number"] {
            padding: 8px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
<?php
// Підключення до бази даних
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mysitedb";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Помилка підключення: " . $e->getMessage();
    exit;
}

// Розрахунок статистики
echo "<div class='stats-container'>";
echo "<h2>Статистика сайту</h2>";

// 1. Загальна кількість записів у таблиці студентів
$stmt = $conn->query("SELECT COUNT(*) as count FROM students");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<div class='stat-item'>Загальна кількість студентів: " . $result['count'] . "</div>";

// 2. Загальна кількість записів у таблиці оцінок
$stmt = $conn->query("SELECT COUNT(*) as count FROM student_grades");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<div class='stat-item'>Загальна кількість оцінок: " . $result['count'] . "</div>";

// 3. Записи за останній місяць в обох таблицях
$stmt = $conn->query("SELECT COUNT(*) as count FROM students WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<div class='stat-item'>Нових студентів за останній місяць: " . $result['count'] . "</div>";

$stmt = $conn->query("SELECT COUNT(*) as count FROM student_grades WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<div class='stat-item'>Нових оцінок за останній місяць: " . $result['count'] . "</div>";

// 4. Останній запис з таблиці оцінок
$stmt = $conn->query("SELECT sg.*, s.name, s.surname FROM student_grades sg 
                      JOIN students s ON sg.student_id = s.id 
                      ORDER BY sg.created_at DESC LIMIT 1");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
if ($result) {
    echo "<div class='stat-item'>Остання оцінка: Студент " . htmlspecialchars($result['name']) . " " . 
         htmlspecialchars($result['surname']) . " отримав оцінку " . $result['grade'] . 
         " з предмету " . htmlspecialchars($result['subject']) . "</div>";
}

// 5. Студент з найбільшою кількістю оцінок
$stmt = $conn->query("SELECT s.name, s.surname, COUNT(sg.id) as grade_count 
                      FROM students s 
                      LEFT JOIN student_grades sg ON s.id = sg.student_id 
                      GROUP BY s.id 
                      ORDER BY grade_count DESC 
                      LIMIT 1");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
if ($result) {
    echo "<div class='stat-item'>Студент з найбільшою кількістю оцінок: " . 
         htmlspecialchars($result['name']) . " " . htmlspecialchars($result['surname']) . 
         " (" . $result['grade_count'] . " оцінок)</div>";
}
echo "</div>";

// Функціонал пошуку
echo "<div class='search-container'>";
echo "<h2>Пошук</h2>";
?>

<form method="GET">
    <input type="text" name="keyword" placeholder="Ключове слово для пошуку" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
    <input type="number" name="grade_from" placeholder="Оцінка від" value="<?php echo isset($_GET['grade_from']) ? htmlspecialchars($_GET['grade_from']) : ''; ?>">
    <input type="number" name="grade_to" placeholder="Оцінка до" value="<?php echo isset($_GET['grade_to']) ? htmlspecialchars($_GET['grade_to']) : ''; ?>">
    <input type="submit" value="Шукати">
</form>

<?php
if (isset($_GET['keyword']) || (isset($_GET['grade_from']) && isset($_GET['grade_to']))) {
    $conditions = [];
    $params = [];
    
    if (!empty($_GET['keyword'])) {
        $keyword = '%' . $_GET['keyword'] . '%';
        $conditions[] = "(s.name LIKE :keyword OR s.surname LIKE :keyword OR sg.subject LIKE :keyword OR sg.teacher LIKE :keyword)";
        $params[':keyword'] = $keyword;
    }
    
    if (!empty($_GET['grade_from']) && !empty($_GET['grade_to'])) {
        $conditions[] = "sg.grade BETWEEN :grade_from AND :grade_to";
        $params[':grade_from'] = $_GET['grade_from'];
        $params[':grade_to'] = $_GET['grade_to'];
    }
    
    if (!empty($conditions)) {
        $sql = "SELECT s.name, s.surname, s.group, sg.subject, sg.grade, sg.teacher, sg.created_at 
                FROM students s 
                JOIN student_grades sg ON s.id = sg.student_id 
                WHERE " . implode(" AND ", $conditions) . 
                " ORDER BY sg.created_at DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($results) {
            echo "<table>";
            echo "<tr><th>Ім'я</th><th>Прізвище</th><th>Група</th><th>Предмет</th><th>Оцінка</th><th>Викладач</th><th>Дата</th></tr>";
            foreach ($results as $row) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['surname']) . "</td>";
                echo "<td>" . htmlspecialchars($row['group']) . "</td>";
                echo "<td>" . htmlspecialchars($row['subject']) . "</td>";
                echo "<td>" . $row['grade'] . "</td>";
                echo "<td>" . htmlspecialchars($row['teacher']) . "</td>";
                echo "<td>" . $row['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Результатів не знайдено.</p>";
        }
    }
}
echo "</div>";
?>
</body>
</html> 