<?php
session_start();

// Устанавливаем заголовки для JSON ответа
header('Content-Type: application/json');

// Инициализируем баланс, если он не установлен
if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 10000; // Начальный баланс 10000 монет
}

// Инициализируем инвентарь, если он не установлен
if (!isset($_SESSION['inventory'])) {
    $_SESSION['inventory'] = [];
}

// Возвращаем данные пользователя
echo json_encode([
    'success' => true,
    'balance' => $_SESSION['balance'],
    'inventory_count' => count($_SESSION['inventory'])
]);
?>