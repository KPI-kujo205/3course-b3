<?php
// Створення зʼєднання з сервером
$link = mysqli_connect("localhost", "admin", "admin");
$db = "MySiteDB";

// Вибір бази даних
mysqli_select_db($link, $db);

// Запит для отримання користувачів
$query = "SELECT id, name, surname, `group` FROM students";
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
            </tr>";

    // Виведення даних у таблицю
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['surname']}</td>
                <td>{$row['group']}</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "Помилка при виконанні запиту: " . mysqli_error($link);
}

// Закриття з'єднання
mysqli_close($link);
?>
