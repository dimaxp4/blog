<?php
require_once 'config.php';
session_start();

// Инициализируем данные пользователя
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

// Рассчитываем общую статистику
$totalValue = 0;
foreach ($_SESSION['inventory'] as $item) {
    $baseItem = findItemByName($item['name'], $items, $rarities);
    if ($baseItem) {
        $totalValue += $baseItem['price'];
    }
}

// Подсчитываем предметы по редкости
$rarityStats = [];
foreach ($rarities as $rarity => $data) {
    $rarityStats[$rarity] = 0;
}

foreach ($_SESSION['inventory'] as $item) {
    if (isset($rarityStats[$item['rarity']])) {
        $rarityStats[$item['rarity']]++;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль - CS2 Case Opening</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-section {
            padding: 60px 0;
        }

        .profile-header {
            text-align: center;
            background: linear-gradient(135deg, #ff6b35, #ff8c42);
            padding: 40px;
            border-radius: 20px;
            margin-bottom: 40px;
            color: white;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 3rem;
        }

        .profile-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: linear-gradient(145deg, #1a1a1a, #0f0f0f);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid #333;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: #ff6b35;
        }

        .stat-card h3 {
            color: #ff6b35;
            font-size: 2rem;
            margin-bottom: 10px;
            font-family: 'Orbitron', monospace;
        }

        .stat-card p {
            color: #cccccc;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .stat-card .stat-icon {
            font-size: 2.5rem;
            color: #ff6b35;
            margin-bottom: 15px;
        }

        .rarity-breakdown {
            background: linear-gradient(145deg, #1a1a1a, #0f0f0f);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid #333;
            margin-bottom: 40px;
        }

        .rarity-breakdown h3 {
            color: #ffffff;
            margin-bottom: 25px;
            font-size: 1.5rem;
        }

        .rarity-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #333;
        }

        .rarity-item:last-child {
            border-bottom: none;
        }

        .rarity-name {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .rarity-color {
            width: 20px;
            height: 20px;
            border-radius: 50%;
        }

        .rarity-count {
            font-weight: 600;
            color: #ff6b35;
            font-size: 1.2rem;
        }

        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .achievement {
            background: linear-gradient(135deg, #2a2a2a, #1a1a1a);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .achievement.unlocked {
            border-color: #ffd700;
            background: linear-gradient(135deg, #3a3a00, #2a2a00);
        }

        .achievement-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #666;
        }

        .achievement.unlocked .achievement-icon {
            color: #ffd700;
        }

        .achievement h4 {
            color: #cccccc;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .achievement.unlocked h4 {
            color: #ffd700;
        }

        .achievement p {
            color: #888;
            font-size: 0.8rem;
        }

        .user-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 12px 25px;
            background: linear-gradient(45deg, #ff6b35, #ff8c42);
            border: none;
            border-radius: 12px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
        }

        .reset-btn {
            background: linear-gradient(45deg, #dc3545, #e74c3c);
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
                    <a href="stats.php" class="nav-link">Статистика</a>
                    <a href="profile.php" class="nav-link active">Профиль</a>
                </nav>
                <div class="balance">
                    <i class="fas fa-coins"></i>
                    <span><?php echo number_format($_SESSION['balance']); ?></span>
                </div>
            </div>
        </header>

        <!-- Profile Section -->
        <section class="profile-section">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h1>Игрок CS2</h1>
                <p>Дата регистрации: Сегодня</p>
                <div class="user-actions">
                    <a href="index.php" class="action-btn">
                        <i class="fas fa-box"></i>
                        Открыть кейсы
                    </a>
                    <a href="inventory.php" class="action-btn">
                        <i class="fas fa-backpack"></i>
                        Инвентарь
                    </a>
                    <a href="upgrade.php" class="action-btn">
                        <i class="fas fa-arrow-up"></i>
                        Апгрейд
                    </a>
                    <button class="action-btn reset-btn" onclick="resetProgress()">
                        <i class="fas fa-redo"></i>
                        Сбросить прогресс
                    </button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="profile-stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h3><?php echo number_format($_SESSION['balance']); ?></h3>
                    <p>Текущий баланс</p>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <h3><?php echo count($_SESSION['inventory']); ?></h3>
                    <p>Предметов в инвентаре</p>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-gem"></i>
                    </div>
                    <h3><?php echo number_format($totalValue); ?></h3>
                    <p>Общая стоимость инвентаря</p>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3><?php echo $_SESSION['stats']['total_sold'] ?? 0; ?></h3>
                    <p>Предметов продано</p>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3><?php echo number_format($_SESSION['stats']['total_earned'] ?? 0); ?></h3>
                    <p>Заработано с продаж</p>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <h3><?php echo $_SESSION['stats']['total_upgrades'] ?? 0; ?></h3>
                    <p>Апгрейдов выполнено</p>
                </div>
            </div>

            <!-- Rarity Breakdown -->
            <div class="rarity-breakdown">
                <h3>Предметы по редкости</h3>
                <?php foreach ($rarities as $rarity => $data): ?>
                <div class="rarity-item">
                    <div class="rarity-name">
                        <div class="rarity-color" style="background-color: <?php echo $data['color']; ?>"></div>
                        <span><?php echo $data['name']; ?></span>
                    </div>
                    <div class="rarity-count"><?php echo $rarityStats[$rarity]; ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Achievements -->
            <div class="section-header">
                <h2 class="section-title">Достижения</h2>
                <p class="section-subtitle">Ваши успехи в открытии кейсов</p>
            </div>

            <div class="achievements-grid">
                <div class="achievement <?php echo count($_SESSION['inventory']) >= 1 ? 'unlocked' : ''; ?>">
                    <div class="achievement-icon">
                        <i class="fas fa-baby"></i>
                    </div>
                    <h4>Первый предмет</h4>
                    <p>Получите первый предмет</p>
                </div>

                <div class="achievement <?php echo count($_SESSION['inventory']) >= 10 ? 'unlocked' : ''; ?>">
                    <div class="achievement-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <h4>Коллекционер</h4>
                    <p>Соберите 10 предметов</p>
                </div>

                <div class="achievement <?php echo $rarityStats['special'] >= 1 ? 'unlocked' : ''; ?>">
                    <div class="achievement-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h4>Счастливчик</h4>
                    <p>Получите нож</p>
                </div>

                <div class="achievement <?php echo ($_SESSION['stats']['total_earned'] ?? 0) >= 1000 ? 'unlocked' : ''; ?>">
                    <div class="achievement-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h4>Торговец</h4>
                    <p>Заработайте 1000 монет</p>
                </div>

                <div class="achievement <?php echo ($_SESSION['stats']['total_upgrades'] ?? 0) >= 5 ? 'unlocked' : ''; ?>">
                    <div class="achievement-icon">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <h4>Апгрейдер</h4>
                    <p>Выполните 5 апгрейдов</p>
                </div>

                <div class="achievement <?php echo $totalValue >= 10000 ? 'unlocked' : ''; ?>">
                    <div class="achievement-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h4>Магнат</h4>
                    <p>Инвентарь стоимостью 10,000 монет</p>
                </div>
            </div>
        </section>
    </div>

    <script>
        function resetProgress() {
            if (confirm('Вы уверены, что хотите сбросить весь прогресс? Это действие необратимо!')) {
                fetch('reset_progress.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Прогресс успешно сброшен!');
                        window.location.reload();
                    } else {
                        alert('Ошибка при сбросе прогресса: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Произошла ошибка!');
                });
            }
        }
    </script>
</body>
</html>