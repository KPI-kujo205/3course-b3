<?php
// Створення зʼєднання з сервером
$link = mysqli_connect("localhost", "admin", "admin");

if ($link) {
    echo "Зʼєднання з сервером встановлено", "<br>";
} else {
    echo "Немає зʼєднання з сервером";
}

$db = "MySiteDB";
$query = "CREATE DATABASE IF NOT EXISTS $db";

// Потім реалізація запиту на створення. Важлива послідовність аргументів функції: зʼєднання з сервером, SQL-запит.
$create_db = mysqli_query($link, $query);

if ($create_db) {
    echo "База даних $db успішно створена";
} else {
    echo "База не створена";
}



$usersQueryStr = "
CREATE TABLE IF NOT EXISTS MySiteDB.students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    surname VARCHAR(255) NOT NULL,
    `group` VARCHAR(50) NOT NULL
);
";

$userGradesStr = "
CREATE TABLE IF NOT EXISTS MySiteDB.student_grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(255) NOT NULL,
    ticket_number INT NOT NULL,
    grade INT NOT NULL,
    teacher VARCHAR(255) NOT NULL,
    student_id INT NOT NULL,

    FOREIGN KEY (student_id) REFERENCES students(id)
);
";



$users_query = mysqli_query($link, $usersQueryStr);
$grades_query = mysqli_query($link, $userGradesStr);


if ($users_query) {
    echo "\nТаблиця користувачів $db успішно створена";
} else {
    echo "\nТаблиця користувачів не створена";
}

if ($grades_query) {
    echo "\nТаблиця оцінок $db успішно створена";
} else {
    echo "\nТаблиці оцінок не створенa";
}
?>
