<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Statistics</title>
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
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mysitedb";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Statistics calculations
echo "<div class='stats-container'>";
echo "<h2>Site Statistics</h2>";

// 1. Total number of records in students table
$stmt = $conn->query("SELECT COUNT(*) as count FROM students");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<div class='stat-item'>Total students: " . $result['count'] . "</div>";

// 2. Total number of records in student_grades table
$stmt = $conn->query("SELECT COUNT(*) as count FROM student_grades");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<div class='stat-item'>Total grades recorded: " . $result['count'] . "</div>";

// 3. Records from last month in both tables
$stmt = $conn->query("SELECT COUNT(*) as count FROM students WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<div class='stat-item'>New students in the last month: " . $result['count'] . "</div>";

$stmt = $conn->query("SELECT COUNT(*) as count FROM student_grades WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<div class='stat-item'>New grades in the last month: " . $result['count'] . "</div>";

// 4. Last record from student_grades
$stmt = $conn->query("SELECT sg.*, s.name, s.surname FROM student_grades sg 
                      JOIN students s ON sg.student_id = s.id 
                      ORDER BY sg.created_at DESC LIMIT 1");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
if ($result) {
    echo "<div class='stat-item'>Latest grade: Student " . htmlspecialchars($result['name']) . " " . 
         htmlspecialchars($result['surname']) . " got grade " . $result['grade'] . 
         " in " . htmlspecialchars($result['subject']) . "</div>";
}

// 5. Student with most grades
$stmt = $conn->query("SELECT s.name, s.surname, COUNT(sg.id) as grade_count 
                      FROM students s 
                      LEFT JOIN student_grades sg ON s.id = sg.student_id 
                      GROUP BY s.id 
                      ORDER BY grade_count DESC 
                      LIMIT 1");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
if ($result) {
    echo "<div class='stat-item'>Student with most grades: " . 
         htmlspecialchars($result['name']) . " " . htmlspecialchars($result['surname']) . 
         " (" . $result['grade_count'] . " grades)</div>";
}
echo "</div>";

// Search functionality
echo "<div class='search-container'>";
echo "<h2>Search</h2>";
?>

<form method="GET">
    <input type="text" name="keyword" placeholder="Search keyword" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
    <input type="number" name="grade_from" placeholder="Grade from" value="<?php echo isset($_GET['grade_from']) ? htmlspecialchars($_GET['grade_from']) : ''; ?>">
    <input type="number" name="grade_to" placeholder="Grade to" value="<?php echo isset($_GET['grade_to']) ? htmlspecialchars($_GET['grade_to']) : ''; ?>">
    <input type="submit" value="Search">
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
            echo "<tr><th>Name</th><th>Surname</th><th>Group</th><th>Subject</th><th>Grade</th><th>Teacher</th><th>Date</th></tr>";
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
            echo "<p>No results found.</p>";
        }
    }
}
echo "</div>";
?>
</body>
</html> 