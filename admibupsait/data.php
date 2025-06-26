<?php
require_once 'includes/db_connect.php';

// Получаем список всех представлений
$views = [
    'zapros1' => 'Студии в Лос-Анджелесе',
    'zapros2' => 'Альбомы Electric Lady Studios',
    'zapros3' => 'Количество альбомов по студиям',
    'zapros4' => 'Артисты и альбомы по студиям',
    'zapros5' => 'Клиенты студий',
    'zapros6' => 'Студии и жанры',
    'zapros7' => 'Артисты с последними релизами',
    'zapros8' => 'Студии с более чем 1 альбомом',
    'zapros9' => 'Записи 2014 года по студиям',
    'zapros10' => 'Студии с более чем 10 альбомами'
];

// Получаем выбранное представление
$selected_view = isset($_GET['view']) ? $_GET['view'] : 'zapros1';
$view_title = $views[$selected_view] ?? $views['zapros1'];

// Получаем данные из выбранного представления
$result = $conn->query("SELECT * FROM $selected_view");
$columns = [];
$rows = [];

if ($result) {
    // Получаем названия колонок
    while ($field = $result->fetch_field()) {
        $columns[] = $field->name;
    }
    
    // Получаем данные
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель - Данные</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/table.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="logo">Admin Panel</div>
            <nav class="admin-nav">
                <ul>
                    <li><a href="index.php">Главная</a></li>
                    <li class="active"><a href="data.php">Представления</a></li>
                    <li><a href="manage.php">Управление таблицами</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h1><?= htmlspecialchars($view_title) ?></h1>
                <div class="view-selector">
                    <form method="get" action="data.php">
                        <select name="view" onchange="this.form.submit()">
                            <?php foreach ($views as $view => $title): ?>
                                <option value="<?= $view ?>" <?= $selected_view == $view ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($title) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </header>

            <section class="data-section">
                <?php if (!empty($rows)): ?>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <?php foreach ($columns as $column): ?>
                                        <th><?= htmlspecialchars($column) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <?php foreach ($row as $value): ?>
                                            <td><?= htmlspecialchars($value) ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>Нет данных для отображения.</p>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>