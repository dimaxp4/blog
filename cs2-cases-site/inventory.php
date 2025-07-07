<?php
require_once 'config.php';
session_start();

// Инициализируем инвентарь, если он не установлен
if (!isset($_SESSION['inventory'])) {
    $_SESSION['inventory'] = [];
}

// Инициализируем баланс, если он не установлен
if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 10000;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Инвентарь - CS2 Case Opening</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .inventory-section {
            padding: 60px 0;
            min-height: 70vh;
        }

        .inventory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }

        .inventory-item {
            background: linear-gradient(145deg, #1a1a1a, #0f0f0f);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid #333;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .inventory-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--rarity-color);
        }

        .inventory-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            border-color: var(--rarity-color);
        }

        .item-icon {
            text-align: center;
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--rarity-color);
        }

        .item-details h4 {
            font-size: 1.2rem;
            margin-bottom: 8px;
            color: #ffffff;
            font-weight: 600;
        }

        .item-details p {
            color: #cccccc;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .item-rarity-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .item-date {
            font-size: 0.75rem;
            color: #888;
            margin-top: 10px;
        }

        .empty-inventory {
            text-align: center;
            padding: 80px 20px;
            color: #666;
        }

        .empty-inventory i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #444;
        }

        .empty-inventory h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .filter-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-btn {
            padding: 10px 20px;
            background: transparent;
            border: 2px solid #333;
            color: #ccc;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .filter-btn:hover,
        .filter-btn.active {
            border-color: #ff6b35;
            color: #ff6b35;
            background: rgba(255, 107, 53, 0.1);
        }

        .inventory-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: linear-gradient(135deg, #ff6b35, #ff8c42);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            color: white;
        }

        .stat-card h3 {
            font-size: 2rem;
            margin-bottom: 5px;
            font-family: 'Orbitron', monospace;
        }

        .stat-card p {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .item-actions {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #333;
        }

        .sell-btn {
            width: 100%;
            padding: 10px 15px;
            background: linear-gradient(45deg, #28a745, #34ce57);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .sell-btn:hover {
            background: linear-gradient(45deg, #34ce57, #28a745);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
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
                     <a href="inventory.php" class="nav-link active">Инвентарь</a>
                     <a href="upgrade.php" class="nav-link">Апгрейд</a>
                     <a href="stats.php" class="nav-link">Статистика</a>
                     <a href="profile.php" class="nav-link">Профиль</a>
                 </nav>
                <div class="balance">
                    <i class="fas fa-coins"></i>
                    <span><?php echo number_format($_SESSION['balance']); ?></span>
                </div>
            </div>
        </header>

        <!-- Inventory Section -->
        <section class="inventory-section">
            <div class="section-header">
                <h2 class="section-title">Ваш инвентарь</h2>
                <p class="section-subtitle">Все ваши выигранные предметы</p>
            </div>

            <!-- Inventory Stats -->
            <?php if (!empty($_SESSION['inventory'])): ?>
            <div class="inventory-stats">
                <div class="stat-card">
                    <h3><?php echo count($_SESSION['inventory']); ?></h3>
                    <p>Всего предметов</p>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, #8847ff, #a855f7);">
                    <h3><?php 
                        $rareCounts = 0;
                        foreach ($_SESSION['inventory'] as $item) {
                            if (in_array($item['rarity'], ['classified', 'covert', 'special'])) {
                                $rareCounts++;
                            }
                        }
                        echo $rareCounts;
                    ?></h3>
                    <p>Редких предметов</p>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, #ffd700, #ffed4a);">
                    <h3><?php 
                        $specialCount = 0;
                        foreach ($_SESSION['inventory'] as $item) {
                            if ($item['rarity'] === 'special') {
                                $specialCount++;
                            }
                        }
                        echo $specialCount;
                    ?></h3>
                    <p>Ножей</p>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="filter-bar">
                <button class="filter-btn active" onclick="filterItems('all')">Все</button>
                <button class="filter-btn" onclick="filterItems('special')">Ножи</button>
                <button class="filter-btn" onclick="filterItems('covert')">Covert</button>
                <button class="filter-btn" onclick="filterItems('classified')">Classified</button>
                <button class="filter-btn" onclick="filterItems('restricted')">Restricted</button>
                <button class="filter-btn" onclick="filterItems('mil_spec')">Mil-Spec</button>
            </div>
            <?php endif; ?>

            <!-- Inventory Grid -->
            <?php if (empty($_SESSION['inventory'])): ?>
                <div class="empty-inventory">
                    <i class="fas fa-box-open"></i>
                    <h3>Ваш инвентарь пуст</h3>
                    <p>Откройте несколько кейсов, чтобы получить предметы!</p>
                    <a href="index.php" class="btn-primary" style="display: inline-block; margin-top: 20px; text-decoration: none;">
                        Открыть кейсы
                    </a>
                </div>
            <?php else: ?>
                <div class="inventory-grid">
                    <?php 
                    // Сортируем по редкости (самые редкие сначала)
                    $rarityOrder = ['special', 'covert', 'classified', 'restricted', 'mil_spec', 'industrial', 'consumer'];
                    usort($_SESSION['inventory'], function($a, $b) use ($rarityOrder) {
                        $aIndex = array_search($a['rarity'], $rarityOrder);
                        $bIndex = array_search($b['rarity'], $rarityOrder);
                        return $aIndex - $bIndex;
                    });
                    
                    foreach ($_SESSION['inventory'] as $item): 
                    ?>
                    <div class="inventory-item" data-rarity="<?php echo $item['rarity']; ?>" style="--rarity-color: <?php echo $item['color']; ?>">
                        <div class="item-icon">
                            <i class="fas fa-<?php 
                                $icons = [
                                    'Pistol' => 'gun',
                                    'Rifle' => 'crosshairs', 
                                    'SMG' => 'bomb',
                                    'Shotgun' => 'bullseye',
                                    'Sniper' => 'crosshairs',
                                    'Knife' => 'cut'
                                ];
                                echo $icons[$item['type']] ?? 'trophy';
                            ?>"></i>
                        </div>
                        <div class="item-details">
                            <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                            <p><?php echo htmlspecialchars($item['type']); ?></p>
                            <div class="item-rarity-badge" style="background-color: <?php echo $item['color']; ?>; color: <?php echo ($item['rarity'] === 'consumer' || $item['rarity'] === 'special') ? '#000' : '#fff'; ?>;">
                                <?php echo htmlspecialchars($item['rarity_name']); ?>
                            </div>
                                                    <div class="item-date">
                            Получено: <?php echo date('d.m.Y H:i', strtotime($item['obtained_at'])); ?>
                        </div>
                        <div class="item-actions">
                            <?php 
                            $baseItem = findItemByName($item['name'], $items, $rarities);
                            $sellPrice = $baseItem ? intval($baseItem['price'] * 0.8) : 0;
                            ?>
                            <button class="sell-btn" onclick="sellItem('<?php echo htmlspecialchars($item['id']); ?>', <?php echo $sellPrice; ?>)">
                                <i class="fas fa-dollar-sign"></i>
                                Продать за <?php echo number_format($sellPrice); ?>
                            </button>
                        </div>
                    </div>
                </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <script>
        function filterItems(rarity) {
            const items = document.querySelectorAll('.inventory-item');
            const buttons = document.querySelectorAll('.filter-btn');
            
            // Обновляем активную кнопку
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Фильтруем предметы
            items.forEach(item => {
                if (rarity === 'all' || item.dataset.rarity === rarity) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function sellItem(itemId, sellPrice) {
            if (confirm(`Вы уверены, что хотите продать этот предмет за ${sellPrice.toLocaleString()} монет?`)) {
                fetch('sell_item.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({item_id: itemId})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Предмет продан за ${data.sell_price.toLocaleString()} монет!`);
                        
                        // Обновляем баланс в header
                        const balanceElement = document.querySelector('.balance span');
                        if (balanceElement) {
                            balanceElement.textContent = data.new_balance.toLocaleString();
                        }
                        
                        // Перезагружаем страницу для обновления инвентаря
                        window.location.reload();
                    } else {
                        alert('Ошибка при продаже: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Произошла ошибка при продаже предмета!');
                });
            }
        }
    </script>
</body>
</html>