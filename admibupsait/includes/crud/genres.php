<?php
function handle_crud_operation($conn, $table) {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? 0;

    switch ($action) {
        case 'create':
            $name = $conn->real_escape_string($_POST['name']);
            $description = $conn->real_escape_string($_POST['description'] ?? null);

            $query = "INSERT INTO genres (name, description) 
                      VALUES ('$name', " . 
                      ($description ? "'$description'" : "NULL") . ")";
            break;

        case 'update':
            $name = $conn->real_escape_string($_POST['name']);
            $description = $conn->real_escape_string($_POST['description'] ?? null);

            $query = "UPDATE genres SET 
                      name = '$name', 
                      description = " . ($description ? "'$description'" : "NULL") . "
                      WHERE genre_id = $id";
            break;

        case 'delete':
            // Проверяем, есть ли связанные артисты или альбомы
            $has_artists = $conn->query("SELECT COUNT(*) FROM artists WHERE genre_id = $id")->fetch_row()[0];
            $has_albums = $conn->query("SELECT COUNT(*) FROM albums WHERE genre_id = $id")->fetch_row()[0];
            
            if ($has_artists > 0 || $has_albums > 0) {
                die("Нельзя удалить жанр, так как есть связанные артисты или альбомы");
            }
            
            $query = "DELETE FROM genres WHERE genre_id = $id";
            break;

        default:
            return;
    }

    if ($conn->query($query)) {
        header("Location: manage.php?table=$table");
        exit();
    } else {
        die("Ошибка выполнения запроса: " . $conn->error);
    }
}
?>