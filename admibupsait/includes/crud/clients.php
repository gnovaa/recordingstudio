<?php
function handle_crud_operation($conn, $table) {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? 0;

    switch ($action) {
        case 'create':
            $name = $conn->real_escape_string($_POST['name']);
            $contact_person = $conn->real_escape_string($_POST['contact_person'] ?? null);
            $phone = $conn->real_escape_string($_POST['phone'] ?? null);
            $email = $conn->real_escape_string($_POST['email'] ?? null);
            $registration_date = $conn->real_escape_string($_POST['registration_date'] ?? null);

            $query = "INSERT INTO clients (name, contact_person, phone, email, registration_date) 
                      VALUES ('$name', " . 
                      ($contact_person ? "'$contact_person'" : "NULL") . ", " . 
                      ($phone ? "'$phone'" : "NULL") . ", " . 
                      ($email ? "'$email'" : "NULL") . ", " . 
                      ($registration_date ? "'$registration_date'" : "NULL") . ")";
            break;

        case 'update':
            $name = $conn->real_escape_string($_POST['name']);
            $contact_person = $conn->real_escape_string($_POST['contact_person'] ?? null);
            $phone = $conn->real_escape_string($_POST['phone'] ?? null);
            $email = $conn->real_escape_string($_POST['email'] ?? null);
            $registration_date = $conn->real_escape_string($_POST['registration_date'] ?? null);

            $query = "UPDATE clients SET 
                      name = '$name', 
                      contact_person = " . ($contact_person ? "'$contact_person'" : "NULL") . ", 
                      phone = " . ($phone ? "'$phone'" : "NULL") . ", 
                      email = " . ($email ? "'$email'" : "NULL") . ", 
                      registration_date = " . ($registration_date ? "'$registration_date'" : "NULL") . "
                      WHERE client_id = $id";
            break;

        case 'delete':
            // Сначала удаляем связанные записи из studio_clients
            $conn->query("DELETE FROM studio_clients WHERE client_id = $id");
            // Затем удаляем клиента
            $query = "DELETE FROM clients WHERE client_id = $id";
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