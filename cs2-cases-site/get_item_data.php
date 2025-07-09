<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['item_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$itemId = $input['item_id'];

if (!isset($_SESSION['inventory'])) {
    $_SESSION['inventory'] = [];
}

// Ищем предмет в инвентаре
foreach ($_SESSION['inventory'] as $item) {
    if ($item['id'] === $itemId) {
        // Получаем дополнительные данные из конфигурации
        $baseItem = findItemByName($item['name'], $items, $rarities);
        if ($baseItem) {
            $item['price'] = $baseItem['price'];
        }
        
        echo json_encode([
            'success' => true,
            'item' => $item
        ]);
        exit;
    }
}

echo json_encode([
    'success' => false,
    'message' => 'Предмет не найден'
]);
?>