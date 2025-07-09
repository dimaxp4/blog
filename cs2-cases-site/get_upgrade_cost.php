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

if (!$input || !isset($input['from_item_name']) || !isset($input['to_item_name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$fromItemName = $input['from_item_name'];
$toItemName = $input['to_item_name'];

// Получаем данные о предметах
$fromItem = findItemByName($fromItemName, $items, $rarities);
$toItem = findItemByName($toItemName, $items, $rarities);

if (!$fromItem || !$toItem) {
    echo json_encode([
        'success' => false,
        'message' => 'Один или оба предмета не найдены'
    ]);
    exit;
}

// Проверяем возможность апгрейда
if (!canUpgrade($fromItem['rarity'], $toItem['rarity'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Апгрейд между этими предметами невозможен'
    ]);
    exit;
}

// Рассчитываем стоимость
$cost = getUpgradeCost($fromItem, $toItem);

echo json_encode([
    'success' => true,
    'cost' => intval($cost),
    'from_item' => $fromItem,
    'to_item' => $toItem
]);
?>