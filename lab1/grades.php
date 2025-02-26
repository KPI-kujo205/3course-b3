<?php
// Створення зʼєднання з сервером
$link = mysqli_connect("localhost", "admin", "admin");
$db = "MySiteDB";

// Вибір бази даних
mysqli_select_db($link, $db);

// Запит для отримання користувачів та їх оцінок
$query = "
SELECT students.id, students.name, students.surname, students.`group`, student_grades.subject, student_grades.ticket_number, student_grades.grade, student_grades.teacher
FROM students
LEFT JOIN student_grades ON students.id = student_grades.student_id
";
$result = mysqli_query($link, $query);

echo "<a href=\"/lab1/main.php\">На головну</a>";

echo "<div></div>";

if ($result) {
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Surname</th>
                <th>Group</th>
                <th>Subject</th>
                <th>Ticket Number</th>
                <th>Grade</th>
                <th>Teacher</th>
            </tr>";

    // Виведення даних у таблицю
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['surname']}</td>
                <td>{$row['group']}</td>
                <td>{$row['subject']}</td>
                <td>{$row['ticket_number']}</td>
                <td>{$row['grade']}</td>
                <td>{$row['teacher']}</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "Помилка при виконанні запиту: " . mysqli_error($link);
}

// Закриття з'єднання
mysqli_close($link);
?>
