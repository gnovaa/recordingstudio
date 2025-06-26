<?php
// Включение сессии и проверка авторизации
session_start();

// Если пользователь не авторизован - перенаправляем на страницу входа
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_login'])) {
    header('Location: auth.html');
    exit();
}

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Подключение к БД
    $dbHost = 'localhost';
    $dbName = 'recordstudios';
    $dbUser = 'root';
    $dbPass = '';
    
    try {
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Подготовка и выполнение запроса
        $stmt = $pdo->prepare("INSERT INTO lkcabinet (name_user, email_user, service_user, comment_user) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['service'],
            $_POST['comment']
        ]);
        
        $success = "Заявка успешно отправлена! Наш менеджер свяжется с вами в ближайшее время.";
    } catch (PDOException $e) {
        $error = "Ошибка при отправке заявки: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="shortcut icon" href="images/music.png"/>
</head>
<body>

    <div class="welcome-section">
        <h1>Добро пожаловать, <?php echo htmlspecialchars($_SESSION['user_login']); ?>!</h1>
        <a href="logout.php" class="logout-btn">Выйти</a>
    </div>

    <p>Заполните форму записи на услуги - наш менеджер свяжется с вами в ближайшее время!</p>

    <?php if (isset($success)): ?>
        <div><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div>
            <label for="name">Ваше имя:</label><br>
            <input type="text" id="name" name="name" required>
        </div>

        <div>
            <label for="email">Ваша почта:</label><br>
            <input type="email" id="email" name="email" required>
        </div>

        <div>
            <label for="service">Услуга:</label><br>
            <select id="service" name="service" required>
                <option value="">-- Выберите услугу --</option>
                <option value="запись вокала">Запись вокала</option>
                <option value="сведение трека(микширование)">Сведение трека (микширование)</option>
                <option value="мастеринг треков">Мастеринг треков</option>
                <option value="профессиональная аранжировка">Профессиональная аранжировка</option>
            </select>
        </div>

        <div>
            <label for="comment">Комментарий:</label><br>
            <textarea id="comment" name="comment" rows="4"></textarea>
        </div>

        <div>
            <button type="submit">Отправить</button>
        </div>
    </form>
</body>
</html>