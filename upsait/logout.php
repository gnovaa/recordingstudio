<?php
session_start();

// Уничтожаем все данные сессии
$_SESSION = array();
session_destroy();

// Перенаправляем на страницу авторизации
header('Location: auth.html');
exit();