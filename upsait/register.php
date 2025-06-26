<?php
// Устанавливаем кодировку
header('Content-Type: text/html; charset=utf-8');

// Проверяем, что запрос пришел методом POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Неверный метод запроса');
}

// Получаем данные из формы
$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';

// Проверяем, что все поля заполнены
if (empty($login) || empty($password)) {
    die('Все поля должны быть заполнены');
}

// Подключение к базе данных
$dbHost = 'localhost';
$dbName = 'recordstudios';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Ошибка подключения к базе данных: ' . $e->getMessage());
}

// Проверяем, не занят ли логин
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE login_user = :login");
    $stmt->bindParam(':login', $login);
    $stmt->execute();
    
    if ($stmt->fetch()) {
        die('Этот логин уже занят');
    }
} catch (PDOException $e) {
    die('Ошибка при проверке логина: ' . $e->getMessage());
}

// Регистрируем нового пользователя
try {
    $stmt = $pdo->prepare("INSERT INTO users (login_user, password_user) VALUES (:login, :password)");
    $stmt->bindParam(':login', $login);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    
    // Перенаправляем на страницу авторизации
    header('Location: auth.html?registration=success');
    exit();
} catch (PDOException $e) {
    die('Ошибка при регистрации: ' . $e->getMessage());
}