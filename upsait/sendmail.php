<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из формы
    $name = strip_tags(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = strip_tags(trim($_POST["subject"]));
    $message = strip_tags(trim($_POST["message"]));
    
    // Проверяем данные
    if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Пожалуйста, заполните форму корректно.";
        exit;
    }
    
    // Указываем email, на который будут приходить письма
    $recipient = "isip_g.v.tolstov@mpt.ru";
    
    // Формируем содержимое письма
    $email_content = "Имя: $name\n";
    $email_content .= "Email: $email\n\n";
    $email_content .= "Тема: $subject\n\n";
    $email_content .= "Сообщение:\n$message\n";
    
    // Формируем заголовки письма
    $email_headers = "From: $name <$email>";
    
    // Отправляем письмо
    if (mail($recipient, $subject, $email_content, $email_headers)) {
        http_response_code(200);
        echo "Спасибо! Ваше сообщение отправлено.";
    } else {
        http_response_code(500);
        echo "Ой! Что-то пошло не так, и мы не смогли отправить ваше сообщение.";
    }
} else {
    http_response_code(403);
    echo "Возникла проблема с отправкой, пожалуйста, попробуйте еще раз.";
}
?>