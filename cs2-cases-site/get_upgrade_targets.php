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

if (!$input || !isset($input['from_rarity'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$fromRarity = $input['from_rarity'];

// Определяем возможные редкости для апгрейда
$rarityLevels = ['consumer' => 1, 'industrial' => 2, 'mil_spec' => 3, 'restricted' => 4, 'classified' => 5, 'covert' => 6, 'special' => 7];
$fromLevel = $rarityLevels[$fromRarity] ?? 0;

$targetItems = [];

// Получаем предметы на 1-2 уровня выше
foreach ($rarities as $rarity => $data) {
    $toLevel = $rarityLevels[$rarity] ?? 0;
    
    if ($toLevel > $fromLevel && $toLevel <= $fromLevel + 2) {
        $rarityItems = getItemsByRarity($rarity, $items, $rarities);
        $targetItems = array_merge($targetItems, $rarityItems);
    }
}

echo json_encode([
    'success' => true,
    'items' => $targetItems
]);
?>