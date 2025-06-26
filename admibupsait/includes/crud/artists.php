<?php
function handle_crud_operation($conn, $table) {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? 0;

    switch ($action) {
        case 'create':
            $name = $conn->real_escape_string($_POST['name']);
            $real_name = $conn->real_escape_string($_POST['real_name'] ?? null);
            $birth_date = $conn->real_escape_string($_POST['birth_date'] ?? null);
            $country = $conn->real_escape_string($_POST['country'] ?? null);
            $genre_id = $conn->real_escape_string($_POST['genre_id'] ?? null);

            $query = "INSERT INTO artists (name, real_name, birth_date, country, genre_id) 
                      VALUES ('$name', " . 
                      ($real_name ? "'$real_name'" : "NULL") . ", " . 
                      ($birth_date ? "'$birth_date'" : "NULL") . ", " . 
                      ($country ? "'$country'" : "NULL") . ", " . 
                      ($genre_id ? "$genre_id" : "NULL") . ")";
            break;

        case 'update':
            $name = $conn->real_escape_string($_POST['name']);
            $real_name = $conn->real_escape_string($_POST['real_name'] ?? null);
            $birth_date = $conn->real_escape_string($_POST['birth_date'] ?? null);
            $country = $conn->real_escape_string($_POST['country'] ?? null);
            $genre_id = $conn->real_escape_string($_POST['genre_id'] ?? null);

            $query = "UPDATE artists SET 
                      name = '$name', 
                      real_name = " . ($real_name ? "'$real_name'" : "NULL") . ", 
                      birth_date = " . ($birth_date ? "'$birth_date'" : "NULL") . ", 
                      country = " . ($country ? "'$country'" : "NULL") . ", 
                      genre_id = " . ($genre_id ? "$genre_id" : "NULL") . "
                      WHERE artist_id = $id";
            break;

        case 'delete':
            // Удаляем связанные альбомы (если нужно каскадное удаление)
            // $conn->query("DELETE FROM albums WHERE artist_id = $id");
            $query = "DELETE FROM artists WHERE artist_id = $id";
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