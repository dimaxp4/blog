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

if (!$input || !isset($input['from_item_id']) || !isset($input['to_item_name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$fromItemId = $input['from_item_id'];
$toItemName = $input['to_item_name'];

// Инициализируем баланс и инвентарь
if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 10000;
}

if (!isset($_SESSION['inventory'])) {
    $_SESSION['inventory'] = [];
}

// Ищем исходный предмет в инвентаре
$fromItemIndex = -1;
$fromItem = null;

foreach ($_SESSION['inventory'] as $index => $item) {
    if ($item['id'] === $fromItemId) {
        $fromItemIndex = $index;
        $fromItem = $item;
        break;
    }
}

if ($fromItemIndex === -1 || $fromItem === null) {
    echo json_encode([
        'success' => false,
        'message' => 'Исходный предмет не найден в инвентаре'
    ]);
    exit;
}

// Находим целевой предмет
$toItem = findItemByName($toItemName, $items, $rarities);
if (!$toItem) {
    echo json_encode([
        'success' => false,
        'message' => 'Целевой предмет не найден'
    ]);
    exit;
}

// Проверяем возможность апгрейда
if (!canUpgrade($fromItem['rarity'], $toItem['rarity'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Апгрейд невозможен. Можно апгрейдить только на 1-2 уровня редкости выше.'
    ]);
    exit;
}

// Получаем полную информацию об исходном предмете
$fromItemFull = findItemByName($fromItem['name'], $items, $rarities);
if (!$fromItemFull) {
    echo json_encode([
        'success' => false,
        'message' => 'Не удалось определить характеристики исходного предмета'
    ]);
    exit;
}

// Рассчитываем стоимость апгрейда
$upgradeCost = getUpgradeCost($fromItemFull, $toItem);

if ($_SESSION['balance'] < $upgradeCost) {
    echo json_encode([
        'success' => false,
        'message' => 'Недостаточно средств для апгрейда',
        'required' => $upgradeCost,
        'current' => $_SESSION['balance']
    ]);
    exit;
}

// Выполняем апгрейд
// Удаляем исходный предмет
array_splice($_SESSION['inventory'], $fromItemIndex, 1);

// Списываем стоимость апгрейда
$_SESSION['balance'] -= $upgradeCost;

// Добавляем новый предмет
$newItem = [
    'id' => uniqid(),
    'name' => $toItem['name'],
    'type' => $toItem['type'],
    'rarity' => $toItem['rarity'],
    'rarity_name' => $toItem['rarity_name'],
    'color' => $toItem['color'],
    'price' => $toItem['price'],
    'obtained_at' => date('Y-m-d H:i:s'),
    'source' => 'upgrade'
];

$_SESSION['inventory'][] = $newItem;

// Сохраняем статистику апгрейда
if (!isset($_SESSION['stats'])) {
    $_SESSION['stats'] = [
        'total_upgrades' => 0,
        'upgrade_cost_spent' => 0,
        'upgrades_history' => []
    ];
}

$_SESSION['stats']['total_upgrades']++;
$_SESSION['stats']['upgrade_cost_spent'] += $upgradeCost;
$_SESSION['stats']['upgrades_history'][] = [
    'from_item' => $fromItem,
    'to_item' => $newItem,
    'cost' => $upgradeCost,
    'upgraded_at' => date('Y-m-d H:i:s')
];

// Ограничиваем историю апгрейдов (сохраняем только последние 50)
if (count($_SESSION['stats']['upgrades_history']) > 50) {
    $_SESSION['stats']['upgrades_history'] = array_slice($_SESSION['stats']['upgrades_history'], -50);
}

echo json_encode([
    'success' => true,
    'message' => 'Апгрейд успешно выполнен!',
    'new_item' => $newItem,
    'cost' => $upgradeCost,
    'new_balance' => $_SESSION['balance'],
    'inventory_count' => count($_SESSION['inventory'])
]);
?>