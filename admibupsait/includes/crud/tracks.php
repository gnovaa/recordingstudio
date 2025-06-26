<?php
function handle_crud_operation($conn, $table) {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? 0;

    switch ($action) {
        case 'create':
            $title = $conn->real_escape_string($_POST['title']);
            $duration = $conn->real_escape_string($_POST['duration'] ?? null);
            $album_id = $conn->real_escape_string($_POST['album_id']);
            $recording_date = $conn->real_escape_string($_POST['recording_date'] ?? null);

            $query = "INSERT INTO tracks (title, duration, album_id, recording_date) 
                      VALUES ('$title', " . 
                      ($duration ? "'$duration'" : "NULL") . ", 
                      $album_id, " . 
                      ($recording_date ? "'$recording_date'" : "NULL") . ")";
            break;

        case 'update':
            $title = $conn->real_escape_string($_POST['title']);
            $duration = $conn->real_escape_string($_POST['duration'] ?? null);
            $album_id = $conn->real_escape_string($_POST['album_id']);
            $recording_date = $conn->real_escape_string($_POST['recording_date'] ?? null);

            $query = "UPDATE tracks SET 
                      title = '$title', 
                      duration = " . ($duration ? "'$duration'" : "NULL") . ", 
                      album_id = $album_id, 
                      recording_date = " . ($recording_date ? "'$recording_date'" : "NULL") . "
                      WHERE track_id = $id";
            break;

        case 'delete':
            $query = "DELETE FROM tracks WHERE track_id = $id";
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