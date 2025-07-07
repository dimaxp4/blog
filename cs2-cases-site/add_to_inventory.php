<?php
session_start();

// Устанавливаем заголовки для JSON ответа
header('Content-Type: application/json');

// Проверяем метод запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Получаем данные из запроса
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['name']) || !isset($input['type']) || !isset($input['rarity'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Инициализируем инвентарь, если он не установлен
if (!isset($_SESSION['inventory'])) {
    $_SESSION['inventory'] = [];
}

// Добавляем предмет в инвентарь
$item = [
    'id' => $input['id'] ?? uniqid(),
    'name' => $input['name'],
    'type' => $input['type'],
    'rarity' => $input['rarity'],
    'rarity_name' => $input['rarity_name'],
    'color' => $input['color'],
    'obtained_at' => $input['obtained_at'] ?? date('Y-m-d H:i:s'),
    'case_id' => $input['case_id'] ?? null
];

$_SESSION['inventory'][] = $item;

// Возвращаем успешный результат
echo json_encode([
    'success' => true,
    'message' => 'Предмет добавлен в инвентарь',
    'inventory_count' => count($_SESSION['inventory'])
]);
?>