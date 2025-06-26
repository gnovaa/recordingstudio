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
$dbHost = 'localhost'; // Хост БД
$dbName = 'recordstudios'; // Имя БД
$dbUser = 'root'; // Пользователь БД
$dbPass = ''; // Пароль БД

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Ошибка подключения к базе данных: ' . $e->getMessage());
}

// Ищем пользователя в базе данных
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE login_user = :login AND password_user = :password");
    $stmt->bindParam(':login', $login);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    
    // Исправленная строка - используем PDO::FETCH_ASSOC для получения ассоциативного массива
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Пользователь найден, начинаем сессию
        session_start();
        $_SESSION['user_id'] = $user['id_users']; // Предполагаем, что в таблице есть поле id
        $_SESSION['user_login'] = $user['login_user'];
        
        // Перенаправляем на защищенную страницу
        header('Location: dashboard.php');
        exit();
    } else {
        die('Неверный логин или пароль');
    }
} catch (PDOException $e) {
    die('Ошибка при выполнении запроса: ' . $e->getMessage());
}