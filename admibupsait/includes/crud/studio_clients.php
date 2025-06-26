<?php
function handle_crud_operation($conn, $table) {
    $action = $_POST['action'] ?? '';
    $studio_id = $_POST['studio_id'] ?? 0;
    $client_id = $_POST['client_id'] ?? 0;

    switch ($action) {
        case 'create':
            $contract_date = $conn->real_escape_string($_POST['contract_date'] ?? null);

            $query = "INSERT INTO studio_clients (studio_id, client_id, contract_date) 
                      VALUES ($studio_id, $client_id, " . 
                      ($contract_date ? "'$contract_date'" : "NULL") . ")";
            break;

        case 'update':
            $new_studio_id = $conn->real_escape_string($_POST['new_studio_id'] ?? $studio_id);
            $new_client_id = $conn->real_escape_string($_POST['new_client_id'] ?? $client_id);
            $contract_date = $conn->real_escape_string($_POST['contract_date'] ?? null);

            $query = "UPDATE studio_clients SET 
                      studio_id = $new_studio_id, 
                      client_id = $new_client_id, 
                      contract_date = " . ($contract_date ? "'$contract_date'" : "NULL") . "
                      WHERE studio_id = $studio_id AND client_id = $client_id";
            break;

        case 'delete':
            $query = "DELETE FROM studio_clients 
                      WHERE studio_id = $studio_id AND client_id = $client_id";
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