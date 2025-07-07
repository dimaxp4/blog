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

if (!$input || !isset($input['case_id']) || !isset($input['price'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$caseId = (int)$input['case_id'];
$price = (int)$input['price'];

// Инициализируем баланс, если он не установлен
if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 10000;
}

// Проверяем достаточность средств
if ($_SESSION['balance'] < $price) {
    echo json_encode([
        'success' => false, 
        'message' => 'Недостаточно средств'
    ]);
    exit;
}

// Проверяем существование кейса
$caseExists = false;
foreach ($cases as $case) {
    if ($case['id'] === $caseId && $case['price'] === $price) {
        $caseExists = true;
        break;
    }
}

if (!$caseExists) {
    echo json_encode([
        'success' => false, 
        'message' => 'Кейс не найден'
    ]);
    exit;
}

// Списываем средства
$_SESSION['balance'] -= $price;

// Генерируем случайный предмет
$wonItem = getRandomItem($rarities, $items);

// Добавляем уникальный ID и дату получения
$wonItem['id'] = uniqid();
$wonItem['obtained_at'] = date('Y-m-d H:i:s');
$wonItem['case_id'] = $caseId;

// Возвращаем результат
echo json_encode([
    'success' => true,
    'item' => $wonItem,
    'new_balance' => $_SESSION['balance'],
    'message' => 'Кейс успешно открыт!'
]);
?>