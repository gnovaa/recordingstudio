<?php
function handle_crud_operation($conn, $table) {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? 0;

    switch ($action) {
        case 'create':
            $title = $conn->real_escape_string($_POST['title']);
            $release_date = $conn->real_escape_string($_POST['release_date'] ?? null);
            $genre_id = $conn->real_escape_string($_POST['genre_id'] ?? null);
            $studio_id = $conn->real_escape_string($_POST['studio_id'] ?? null);
            $artist_id = $conn->real_escape_string($_POST['artist_id'] ?? null);
            $client_id = $conn->real_escape_string($_POST['client_id'] ?? null);

            $query = "INSERT INTO albums (title, release_date, genre_id, studio_id, artist_id, client_id) 
                      VALUES ('$title', " . 
                      ($release_date ? "'$release_date'" : "NULL") . ", " . 
                      ($genre_id ? "$genre_id" : "NULL") . ", " . 
                      ($studio_id ? "$studio_id" : "NULL") . ", " . 
                      ($artist_id ? "$artist_id" : "NULL") . ", " . 
                      ($client_id ? "$client_id" : "NULL") . ")";
            break;

        case 'update':
            $title = $conn->real_escape_string($_POST['title']);
            $release_date = $conn->real_escape_string($_POST['release_date'] ?? null);
            $genre_id = $conn->real_escape_string($_POST['genre_id'] ?? null);
            $studio_id = $conn->real_escape_string($_POST['studio_id'] ?? null);
            $artist_id = $conn->real_escape_string($_POST['artist_id'] ?? null);
            $client_id = $conn->real_escape_string($_POST['client_id'] ?? null);

            $query = "UPDATE albums SET 
                      title = '$title', 
                      release_date = " . ($release_date ? "'$release_date'" : "NULL") . ", 
                      genre_id = " . ($genre_id ? "$genre_id" : "NULL") . ", 
                      studio_id = " . ($studio_id ? "$studio_id" : "NULL") . ", 
                      artist_id = " . ($artist_id ? "$artist_id" : "NULL") . ", 
                      client_id = " . ($client_id ? "$client_id" : "NULL") . "
                      WHERE album_id = $id";
            break;

        case 'delete':
            // Удаляем связанные треки (если нужно каскадное удаление)
            $conn->query("DELETE FROM tracks WHERE album_id = $id");
            $query = "DELETE FROM albums WHERE album_id = $id";
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