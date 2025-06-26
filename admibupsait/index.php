<?php
require_once 'includes/db_connect.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель - Главная</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="logo">Admin Panel</div>
            <nav class="admin-nav">
                <ul>
                    <li class="active"><a href="index.php">Главная</a></li>
                    <li><a href="data.php">Представления</a></li>
                    <li><a href="manage.php">Управление таблицами</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h1>Добро пожаловать, Администратор</h1>
            </header>

            <section class="welcome-section">
                <div class="welcome-card">
                    <h2>Управление музыкальными студиями</h2>
                    <p>Панель администратора для управления базой данных студии звукозаписи.</p>
                    <p>Используйте меню слева для навигации по разделам.</p>
                </div>

                <div class="stats-cards">
                    <div class="stat-card">
                        <h3>Студии</h3>
                        <p class="stat-value">
                            <?php 
                            $result = $conn->query("SELECT COUNT(*) FROM studios");
                            echo $result->fetch_row()[0];
                            ?>
                        </p>
                    </div>
                    <div class="stat-card">
                        <h3>Альбомы</h3>
                        <p class="stat-value">
                            <?php 
                            $result = $conn->query("SELECT COUNT(*) FROM albums");
                            echo $result->fetch_row()[0];
                            ?>
                        </p>
                    </div>
                    <div class="stat-card">
                        <h3>Артисты</h3>
                        <p class="stat-value">
                            <?php 
                            $result = $conn->query("SELECT COUNT(*) FROM artists");
                            echo $result->fetch_row()[0];
                            ?>
                        </p>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>