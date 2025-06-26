<?php
function handle_crud_operation($conn, $table) {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? 0;

    switch ($action) {
        case 'create':
            $name = $conn->real_escape_string($_POST['name']);
            $address = $conn->real_escape_string($_POST['address']);
            $city = $conn->real_escape_string($_POST['city']);
            $phone = $conn->real_escape_string($_POST['phone'] ?? null);
            $email = $conn->real_escape_string($_POST['email'] ?? null);
            $founded_date = $conn->real_escape_string($_POST['founded_date'] ?? null);

            $query = "INSERT INTO studios (name, address, city, phone, email, founded_date) 
                      VALUES ('$name', '$address', '$city', " . 
                      ($phone ? "'$phone'" : "NULL") . ", " . 
                      ($email ? "'$email'" : "NULL") . ", " . 
                      ($founded_date ? "'$founded_date'" : "NULL") . ")";
            break;

        case 'update':
            $name = $conn->real_escape_string($_POST['name']);
            $address = $conn->real_escape_string($_POST['address']);
            $city = $conn->real_escape_string($_POST['city']);
            $phone = $conn->real_escape_string($_POST['phone'] ?? null);
            $email = $conn->real_escape_string($_POST['email'] ?? null);
            $founded_date = $conn->real_escape_string($_POST['founded_date'] ?? null);

            $query = "UPDATE studios SET 
                      name = '$name', 
                      address = '$address', 
                      city = '$city', 
                      phone = " . ($phone ? "'$phone'" : "NULL") . ", 
                      email = " . ($email ? "'$email'" : "NULL") . ", 
                      founded_date = " . ($founded_date ? "'$founded_date'" : "NULL") . "
                      WHERE studio_id = $id";
            break;

        case 'delete':
            $query = "DELETE FROM studios WHERE studio_id = $id";
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