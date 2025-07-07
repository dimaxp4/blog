<?php
require_once 'config.php';
session_start();

// Инициализируем данные
if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 10000;
}

if (!isset($_SESSION['inventory'])) {
    $_SESSION['inventory'] = [];
}

if (!isset($_SESSION['stats'])) {
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
}

// Рассчитываем дополнительную статистику
$totalValue = 0;
$rarityStats = [];

foreach ($rarities as $rarity => $data) {
    $rarityStats[$rarity] = 0;
}

foreach ($_SESSION['inventory'] as $item) {
    $baseItem = findItemByName($item['name'], $items, $rarities);
    if ($baseItem) {
        $totalValue += $baseItem['price'];
    }
    
    if (isset($rarityStats[$item['rarity']])) {
        $rarityStats[$item['rarity']]++;
    }
}

// Статистика по типам предметов
$typeStats = [];
foreach ($_SESSION['inventory'] as $item) {
    if (!isset($typeStats[$item['type']])) {
        $typeStats[$item['type']] = 0;
    }
    $typeStats[$item['type']]++;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статистика - CS2 Case Opening</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .stats-section {
            padding: 60px 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .stats-card {
            background: linear-gradient(145deg, #1a1a1a, #0f0f0f);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid #333;
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            border-color: #ff6b35;
        }

        .stats-card h3 {
            color: #ff6b35;
            margin-bottom: 25px;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stats-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #333;
        }

        .stats-item:last-child {
            border-bottom: none;
        }

        .stats-label {
            color: #cccccc;
            font-size: 0.95rem;
        }

        .stats-value {
            color: #ff6b35;
            font-weight: 600;
            font-size: 1.1rem;
            font-family: 'Orbitron', monospace;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #333;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 8px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #ff6b35, #ff8c42);
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        .history-section {
            background: linear-gradient(145deg, #1a1a1a, #0f0f0f);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid #333;
            margin-bottom: 30px;
        }

        .history-tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            border-bottom: 1px solid #333;
        }

        .history-tab {
            padding: 12px 20px;
            background: transparent;
            border: none;
            color: #cccccc;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .history-tab.active {
            color: #ff6b35;
            border-bottom-color: #ff6b35;
        }

        .history-content {
            display: none;
        }

        .history-content.active {
            display: block;
        }

        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #333;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-details {
            flex: 1;
        }

        .history-item-name {
            color: #ffffff;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .history-item-info {
            color: #888;
            font-size: 0.9rem;
        }

        .history-value {
            color: #28a745;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .chart-container {
            height: 200px;
            display: flex;
            align-items: end;
            gap: 10px;
            padding: 20px 0;
            border-top: 1px solid #333;
            margin-top: 20px;
        }

        .chart-bar {
            flex: 1;
            background: linear-gradient(to top, #ff6b35, #ff8c42);
            border-radius: 4px 4px 0 0;
            position: relative;
            min-height: 10px;
        }

        .chart-label {
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.8rem;
            color: #888;
            white-space: nowrap;
        }

        .chart-value {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.8rem;
            color: #ff6b35;
            font-weight: 600;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #444;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-crosshairs"></i>
                    <span>CS2 CASES</span>
                </div>
                <nav class="nav">
                    <a href="index.php" class="nav-link">Кейсы</a>
                    <a href="inventory.php" class="nav-link">Инвентарь</a>
                    <a href="upgrade.php" class="nav-link">Апгрейд</a>
                    <a href="stats.php" class="nav-link active">Статистика</a>
                    <a href="profile.php" class="nav-link">Профиль</a>
                </nav>
                <div class="balance">
                    <i class="fas fa-coins"></i>
                    <span><?php echo number_format($_SESSION['balance']); ?></span>
                </div>
            </div>
        </header>

        <!-- Stats Section -->
        <section class="stats-section">
            <div class="section-header">
                <h2 class="section-title">Подробная статистика</h2>
                <p class="section-subtitle">Анализ вашей активности в открытии кейсов</p>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <!-- General Stats -->
                <div class="stats-card">
                    <h3>
                        <i class="fas fa-chart-line"></i>
                        Общая статистика
                    </h3>
                    <div class="stats-item">
                        <span class="stats-label">Текущий баланс</span>
                        <span class="stats-value"><?php echo number_format($_SESSION['balance']); ?></span>
                    </div>
                    <div class="stats-item">
                        <span class="stats-label">Предметов в инвентаре</span>
                        <span class="stats-value"><?php echo count($_SESSION['inventory']); ?></span>
                    </div>
                    <div class="stats-item">
                        <span class="stats-label">Общая стоимость инвентаря</span>
                        <span class="stats-value"><?php echo number_format($totalValue); ?></span>
                    </div>
                    <div class="stats-item">
                        <span class="stats-label">Прибыль/Убыток</span>
                        <span class="stats-value" style="color: <?php echo ($totalValue + $_SESSION['stats']['total_earned'] - $_SESSION['stats']['total_spent'] - $_SESSION['stats']['upgrade_cost_spent']) >= 0 ? '#28a745' : '#dc3545'; ?>">
                            <?php echo number_format($totalValue + $_SESSION['stats']['total_earned'] - $_SESSION['stats']['total_spent'] - $_SESSION['stats']['upgrade_cost_spent']); ?>
                        </span>
                    </div>
                </div>

                <!-- Trading Stats -->
                <div class="stats-card">
                    <h3>
                        <i class="fas fa-exchange-alt"></i>
                        Торговая статистика
                    </h3>
                    <div class="stats-item">
                        <span class="stats-label">Предметов продано</span>
                        <span class="stats-value"><?php echo $_SESSION['stats']['total_sold'] ?? 0; ?></span>
                    </div>
                    <div class="stats-item">
                        <span class="stats-label">Заработано с продаж</span>
                        <span class="stats-value"><?php echo number_format($_SESSION['stats']['total_earned'] ?? 0); ?></span>
                    </div>
                    <div class="stats-item">
                        <span class="stats-label">Апгрейдов выполнено</span>
                        <span class="stats-value"><?php echo $_SESSION['stats']['total_upgrades'] ?? 0; ?></span>
                    </div>
                    <div class="stats-item">
                        <span class="stats-label">Потрачено на апгрейды</span>
                        <span class="stats-value"><?php echo number_format($_SESSION['stats']['upgrade_cost_spent'] ?? 0); ?></span>
                    </div>
                </div>

                <!-- Rarity Distribution -->
                <div class="stats-card">
                    <h3>
                        <i class="fas fa-gem"></i>
                        Распределение по редкости
                    </h3>
                    <?php $maxCount = max(array_values($rarityStats)); ?>
                    <?php foreach ($rarities as $rarity => $data): ?>
                    <div class="stats-item">
                        <span class="stats-label" style="color: <?php echo $data['color']; ?>">
                            <?php echo $data['name']; ?>
                        </span>
                        <span class="stats-value"><?php echo $rarityStats[$rarity]; ?></span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $maxCount > 0 ? ($rarityStats[$rarity] / $maxCount * 100) : 0; ?>%; background-color: <?php echo $data['color']; ?>;"></div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Type Distribution -->
                <div class="stats-card">
                    <h3>
                        <i class="fas fa-crosshairs"></i>
                        Распределение по типам
                    </h3>
                    <?php if (empty($typeStats)): ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <p>Пока нет предметов</p>
                        </div>
                    <?php else: ?>
                        <?php $maxTypeCount = max(array_values($typeStats)); ?>
                        <?php foreach ($typeStats as $type => $count): ?>
                        <div class="stats-item">
                            <span class="stats-label"><?php echo $type; ?></span>
                            <span class="stats-value"><?php echo $count; ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo ($count / $maxTypeCount * 100); ?>%;"></div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- History Section -->
            <div class="history-section">
                <h3>
                    <i class="fas fa-history"></i>
                    История активности
                </h3>
                
                <div class="history-tabs">
                    <button class="history-tab active" onclick="showHistory('sales')">Продажи</button>
                    <button class="history-tab" onclick="showHistory('upgrades')">Апгрейды</button>
                </div>

                <!-- Sales History -->
                <div id="sales" class="history-content active">
                    <?php if (empty($_SESSION['stats']['items_sold'])): ?>
                        <div class="empty-state">
                            <i class="fas fa-shopping-cart"></i>
                            <p>Пока нет продаж</p>
                        </div>
                    <?php else: ?>
                        <?php foreach (array_reverse($_SESSION['stats']['items_sold']) as $sale): ?>
                        <div class="history-item">
                            <div class="history-details">
                                <div class="history-item-name"><?php echo htmlspecialchars($sale['item']['name']); ?></div>
                                <div class="history-item-info">
                                    <?php echo $sale['item']['type']; ?> • 
                                    <?php echo $sale['item']['rarity_name']; ?> • 
                                    <?php echo date('d.m.Y H:i', strtotime($sale['sold_at'])); ?>
                                </div>
                            </div>
                            <div class="history-value">+<?php echo number_format($sale['price']); ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Upgrades History -->
                <div id="upgrades" class="history-content">
                    <?php if (empty($_SESSION['stats']['upgrades_history'])): ?>
                        <div class="empty-state">
                            <i class="fas fa-arrow-up"></i>
                            <p>Пока нет апгрейдов</p>
                        </div>
                    <?php else: ?>
                        <?php foreach (array_reverse($_SESSION['stats']['upgrades_history']) as $upgrade): ?>
                        <div class="history-item">
                            <div class="history-details">
                                <div class="history-item-name">
                                    <?php echo htmlspecialchars($upgrade['from_item']['name']); ?> 
                                    → 
                                    <?php echo htmlspecialchars($upgrade['to_item']['name']); ?>
                                </div>
                                <div class="history-item-info">
                                    <?php echo $upgrade['from_item']['rarity_name']; ?> → <?php echo $upgrade['to_item']['rarity_name']; ?> • 
                                    <?php echo date('d.m.Y H:i', strtotime($upgrade['upgraded_at'])); ?>
                                </div>
                            </div>
                            <div class="history-value" style="color: #dc3545;">-<?php echo number_format($upgrade['cost']); ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>

    <script>
        function showHistory(type) {
            // Убираем активные классы
            document.querySelectorAll('.history-tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.history-content').forEach(content => content.classList.remove('active'));
            
            // Добавляем активные классы
            event.target.classList.add('active');
            document.getElementById(type).classList.add('active');
        }
    </script>
</body>
</html>