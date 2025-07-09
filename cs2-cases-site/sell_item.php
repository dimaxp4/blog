<?php
session_start();
require_once 'config.php';

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

if (!$input || !isset($input['item_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$itemId = $input['item_id'];

// Инициализируем баланс и инвентарь
if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 10000;
}

if (!isset($_SESSION['inventory'])) {
    $_SESSION['inventory'] = [];
}

// Ищем предмет в инвентаре
$itemIndex = -1;
$itemToSell = null;

foreach ($_SESSION['inventory'] as $index => $item) {
    if ($item['id'] === $itemId) {
        $itemIndex = $index;
        $itemToSell = $item;
        break;
    }
}

if ($itemIndex === -1 || $itemToSell === null) {
    echo json_encode([
        'success' => false,
        'message' => 'Предмет не найден в инвентаре'
    ]);
    exit;
}

// Получаем цену предмета из конфигурации
$baseItem = findItemByName($itemToSell['name'], $items, $rarities);
if (!$baseItem) {
    echo json_encode([
        'success' => false,
        'message' => 'Не удалось определить цену предмета'
    ]);
    exit;
}

// Рассчитываем цену продажи (80% от рыночной стоимости)
$sellPrice = intval($baseItem['price'] * 0.8);

// Удаляем предмет из инвентаря
array_splice($_SESSION['inventory'], $itemIndex, 1);

// Добавляем деньги на баланс
$_SESSION['balance'] += $sellPrice;

// Сохраняем статистику продажи
if (!isset($_SESSION['stats'])) {
    $_SESSION['stats'] = [
        'total_sold' => 0,
        'total_earned' => 0,
        'items_sold' => []
    ];
}

$_SESSION['stats']['total_sold']++;
$_SESSION['stats']['total_earned'] += $sellPrice;
$_SESSION['stats']['items_sold'][] = [
    'item' => $itemToSell,
    'price' => $sellPrice,
    'sold_at' => date('Y-m-d H:i:s')
];

// Ограничиваем историю продаж (сохраняем только последние 50)
if (count($_SESSION['stats']['items_sold']) > 50) {
    $_SESSION['stats']['items_sold'] = array_slice($_SESSION['stats']['items_sold'], -50);
}

echo json_encode([
    'success' => true,
    'message' => 'Предмет успешно продан',
    'sell_price' => $sellPrice,
    'new_balance' => $_SESSION['balance'],
    'inventory_count' => count($_SESSION['inventory'])
]);
?>