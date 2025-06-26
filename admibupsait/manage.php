<?php
require_once 'includes/db_connect.php';

// Определяем доступные таблицы
$tables = [
    'studios' => 'Студии',
    'clients' => 'Клиенты',
    'artists' => 'Артисты',
    'genres' => 'Жанры',
    'albums' => 'Альбомы',
    'tracks' => 'Треки',
    'studio_clients' => 'Клиенты студий'
];

// Получаем выбранную таблицу
$selected_table = isset($_GET['table']) && array_key_exists($_GET['table'], $tables) 
    ? $_GET['table'] 
    : 'studios';

// Обработка CRUD операций
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once "includes/crud/{$selected_table}.php";
    handle_crud_operation($conn, $selected_table);
}

// Получаем данные из выбранной таблицы
$result = $conn->query("SELECT * FROM $selected_table");
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

// Получаем внешние ключи для выбранной таблицы
$foreign_keys = [];
$fk_query = "
    SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = ? 
    AND REFERENCED_TABLE_NAME IS NOT NULL
";

$stmt = $conn->prepare($fk_query);
$stmt->bind_param("s", $selected_table);
$stmt->execute();
$fk_result = $stmt->get_result();

while ($fk = $fk_result->fetch_assoc()) {
    $foreign_keys[$fk['COLUMN_NAME']] = [
        'table' => $fk['REFERENCED_TABLE_NAME'],
        'column' => $fk['REFERENCED_COLUMN_NAME']
    ];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление данными</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/table.css">
    <link rel="stylesheet" href="css/form.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="logo">Admin Panel</div>
            <nav class="admin-nav">
                <ul>
                    <li><a href="index.php">Главная</a></li>
                    <li><a href="data.php">Представления</a></li>
                    <li class="active"><a href="manage.php">Управление данными</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-content">
            <header class="admin-header">
                <h1>Управление таблицами: <?= htmlspecialchars($tables[$selected_table]) ?></h1>
                <div class="header-actions">
                    <div class="table-selector">
                        <form method="get" action="manage.php">
                            <select name="table" onchange="this.form.submit()">
                                <?php foreach ($tables as $table => $title): ?>
                                    <option value="<?= $table ?>" <?= $selected_table == $table ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($title) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                </div>
            </header>

            <section class="management-section">
                <!-- Форма добавления/редактирования -->
                <div class="crud-form-container" id="crudFormContainer" style="display: <?= isset($_GET['edit']) ? 'block' : 'none' ?>;">
                    <div class="form-header">
                        <h2><?= isset($_GET['edit']) ? 'Редактирование' : 'Добавление' ?> записи</h2>
                        <button class="btn btn-secondary" onclick="hideForm()">Закрыть</button>
                    </div>
                    <form id="crudForm" method="post">
                        <?php if (isset($_GET['edit'])): ?>
                            <input type="hidden" name="action" value="update">
                            <?php if ($selected_table === 'studio_clients'): ?>
                                <input type="hidden" name="studio_id" value="<?= $rows[$_GET['edit']]['studio_id'] ?>">
                                <input type="hidden" name="client_id" value="<?= $rows[$_GET['edit']]['client_id'] ?>">
                            <?php else: ?>
                                <input type="hidden" name="id" value="<?= $rows[$_GET['edit']][$columns[0]] ?>">
                            <?php endif; ?>
                        <?php else: ?>
                            <input type="hidden" name="action" value="create">
                        <?php endif; ?>

                        <?php if ($selected_table === 'studio_clients'): ?>
                            <!-- Специальная форма для studio_clients -->
                            <div class="form-group">
                                <label for="studio_id">Студия:</label>
                                <select name="studio_id" id="studio_id" required <?= isset($_GET['edit']) ? 'disabled' : '' ?>>
                                    <option value="">Выберите студию</option>
                                    <?php 
                                    $studios = $conn->query("SELECT * FROM studios");
                                    while ($studio = $studios->fetch_assoc()): 
                                        $selected = isset($_GET['edit']) && $rows[$_GET['edit']]['studio_id'] == $studio['studio_id'] ? 'selected' : '';
                                    ?>
                                        <option value="<?= $studio['studio_id'] ?>" <?= $selected ?>>
                                            <?= htmlspecialchars($studio['name']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="client_id">Клиент:</label>
                                <select name="client_id" id="client_id" required <?= isset($_GET['edit']) ? 'disabled' : '' ?>>
                                    <option value="">Выберите клиента</option>
                                    <?php 
                                    $clients = $conn->query("SELECT * FROM clients");
                                    while ($client = $clients->fetch_assoc()): 
                                        $selected = isset($_GET['edit']) && $rows[$_GET['edit']]['client_id'] == $client['client_id'] ? 'selected' : '';
                                    ?>
                                        <option value="<?= $client['client_id'] ?>" <?= $selected ?>>
                                            <?= htmlspecialchars($client['name']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="contract_date">Дата контракта:</label>
                                <input type="date" name="contract_date" id="contract_date" 
                                       value="<?= isset($_GET['edit']) ? htmlspecialchars($rows[$_GET['edit']]['contract_date']) : '' ?>">
                            </div>

                            <?php if (isset($_GET['edit'])): ?>
                                <input type="hidden" name="new_studio_id" value="<?= $rows[$_GET['edit']]['studio_id'] ?>">
                                <input type="hidden" name="new_client_id" value="<?= $rows[$_GET['edit']]['client_id'] ?>">
                            <?php endif; ?>

                        <?php else: ?>
                            <!-- Стандартная форма для других таблиц -->
                            <?php foreach ($columns as $column): 
                                if ($column === $columns[0] && !isset($_GET['edit'])) continue;
                                
                                $value = isset($_GET['edit']) && isset($rows[$_GET['edit']][$column]) 
                                    ? htmlspecialchars($rows[$_GET['edit']][$column]) 
                                    : '';
                            ?>
                                <div class="form-group">
                                    <label for="<?= $column ?>"><?= ucfirst(str_replace('_', ' ', $column)) ?>:</label>
                                    
                                    <?php if (array_key_exists($column, $foreign_keys)): 
                                        $fk_table = $foreign_keys[$column]['table'];
                                        $fk_data = $conn->query("SELECT * FROM $fk_table");
                                    ?>
                                        <select name="<?= $column ?>" id="<?= $column ?>" required>
                                            <option value="">Выберите...</option>
                                            <?php while ($fk_row = $fk_data->fetch_assoc()): 
                                                $fk_value = $fk_row[$foreign_keys[$column]['column']];
                                                $fk_display = $fk_row['name'] ?? $fk_value;
                                            ?>
                                                <option value="<?= $fk_value ?>" <?= $value == $fk_value ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($fk_display) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    <?php elseif (strpos($column, 'date') !== false): ?>
                                        <input type="date" name="<?= $column ?>" id="<?= $column ?>" value="<?= $value ?>" required>
                                    <?php elseif ($column === 'duration'): ?>
                                        <input type="time" name="<?= $column ?>" id="<?= $column ?>" value="<?= $value ?>" step="1" required>
                                    <?php elseif (strpos($column, 'email') !== false): ?>
                                        <input type="email" name="<?= $column ?>" id="<?= $column ?>" value="<?= $value ?>" required>
                                    <?php elseif (strpos($column, 'phone') !== false): ?>
                                        <input type="tel" name="<?= $column ?>" id="<?= $column ?>" value="<?= $value ?>" required>
                                    <?php else: ?>
                                        <input type="text" name="<?= $column ?>" id="<?= $column ?>" value="<?= $value ?>" required>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <div class="form-actions">
                            <button type="submit" class="btn">Сохранить</button>
                            <a href="manage.php?table=<?= $selected_table ?>" class="btn btn-secondary">Отмена</a>
                        </div>
                    </form>
                </div>

                <!-- Кнопка добавления записи и таблица данных -->
                <div class="data-container">
                    <button id="addRecordBtn" class="btn" style="margin-bottom: 15px;">Добавить запись</button>
                    
                    <?php if (!empty($rows)): ?>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <?php foreach ($columns as $column): ?>
                                            <th><?= ucfirst(str_replace('_', ' ', $column)) ?></th>
                                        <?php endforeach; ?>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $index => $row): ?>
                                        <tr>
                                            <?php foreach ($row as $key => $value): ?>
                                                <td>
                                                    <?php if (array_key_exists($key, $foreign_keys)): 
                                                        $fk_table = $foreign_keys[$key]['table'];
                                                        $fk_column = $foreign_keys[$key]['column'];
                                                        $fk_row = $conn->query("SELECT * FROM $fk_table WHERE $fk_column = " . $conn->real_escape_string($value))->fetch_assoc();
                                                        echo htmlspecialchars($fk_row['name'] ?? $value);
                                                    ?>
                                                    <?php else: ?>
                                                        <?= htmlspecialchars($value) ?>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                            <td class="actions">
                                                <a href="manage.php?table=<?= $selected_table ?>&edit=<?= $index ?>" class="btn-icon edit">✏️</a>
                                                <form method="post" action="manage.php?table=<?= $selected_table ?>" style="display:inline;">
                                                    <input type="hidden" name="action" value="delete">
                                                    <?php if ($selected_table === 'studio_clients'): ?>
                                                        <input type="hidden" name="studio_id" value="<?= $row['studio_id'] ?>">
                                                        <input type="hidden" name="client_id" value="<?= $row['client_id'] ?>">
                                                    <?php else: ?>
                                                        <input type="hidden" name="id" value="<?= $row[$columns[0]] ?>">
                                                    <?php endif; ?>
                                                    <button type="submit" class="btn-icon delete" onclick="return confirm('Удалить запись?')">🗑️</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>Нет данных для отображения.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
    <script>
        // Показать форму добавления записи
        document.getElementById('addRecordBtn').addEventListener('click', function() {
            const formContainer = document.getElementById('crudFormContainer');
            formContainer.style.display = 'block';
            document.getElementById('crudForm').reset();
            
            // Очищаем параметры редактирования в URL
            if (window.location.search.includes('edit=')) {
                const url = new URL(window.location);
                url.searchParams.delete('edit');
                window.history.pushState({}, '', url);
            }
        });

        // Скрыть форму
        function hideForm() {
            document.getElementById('crudFormContainer').style.display = 'none';
            
            // Очищаем параметры редактирования в URL
            if (window.location.search.includes('edit=')) {
                const url = new URL(window.location);
                url.searchParams.delete('edit');
                window.history.pushState({}, '', url);
            }
        }

        // Автоматически открываем форму при редактировании
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.search.includes('edit=')) {
                document.getElementById('crudFormContainer').style.display = 'block';
            }
        });
    </script>
</body>
</html>