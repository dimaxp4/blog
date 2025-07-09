<?php
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Сбрасываем все данные пользователя
$_SESSION['balance'] = 10000;
$_SESSION['inventory'] = [];
$_SESSION['stats'] = [
    'cases_opened' => 0,
    'total_spent' => 0,
    'total_sold' => 0,
    'total_earned' => 0,
    'total_upgrades' => 0,
    'upgrade_cost_spent' => 0,
    'items_sold' => [],
    'upgrades_history' => []
];

echo json_encode([
    'success' => true,
    'message' => 'Прогресс успешно сброшен'
]);
?>