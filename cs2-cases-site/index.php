<?php
require_once 'config.php';

// Начинаем сессию для отслеживания баланса пользователя
session_start();

// Инициализируем баланс, если он не установлен
if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 10000; // Начальный баланс 10000 монет
}

// Инициализируем инвентарь
if (!isset($_SESSION['inventory'])) {
    $_SESSION['inventory'] = [];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS2 Case Opening</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    <a href="index.php" class="nav-link active">Кейсы</a>
                    <a href="inventory.php" class="nav-link">Инвентарь</a>
                    <a href="stats.php" class="nav-link">Статистика</a>
                </nav>
                <div class="balance">
                    <i class="fas fa-coins"></i>
                    <span id="balance"><?php echo number_format($_SESSION['balance']); ?></span>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1 class="hero-title">CS2 CASE OPENING</h1>
                <p class="hero-subtitle">Откройте свой путь к легендарным скинам</p>
                <div class="hero-stats">
                    <div class="stat">
                        <span class="stat-number"><?php echo count($_SESSION['inventory']); ?></span>
                        <span class="stat-label">Предметов в инвентаре</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number"><?php echo number_format($_SESSION['balance']); ?></span>
                        <span class="stat-label">Монет</span>
                    </div>
                </div>
            </div>
            <div class="hero-background"></div>
        </section>

        <!-- Cases Grid -->
        <section class="cases-section">
            <div class="section-header">
                <h2 class="section-title">Доступные кейсы</h2>
                <p class="section-subtitle">Выберите кейс и попробуйте свою удачу</p>
            </div>
            
            <div class="cases-grid">
                <?php foreach ($cases as $case): ?>
                <div class="case-card" data-case-id="<?php echo $case['id']; ?>">
                    <div class="case-image">
                        <img src="assets/images/<?php echo $case['image']; ?>" alt="<?php echo $case['name']; ?>" 
                             onerror="this.src='assets/images/default_case.png'">
                        <div class="case-overlay">
                            <i class="fas fa-unlock-alt"></i>
                        </div>
                    </div>
                    <div class="case-info">
                        <h3 class="case-name"><?php echo $case['name']; ?></h3>
                        <div class="case-price">
                            <i class="fas fa-coins"></i>
                            <span><?php echo number_format($case['price']); ?></span>
                        </div>
                    </div>
                    <button class="open-case-btn" 
                            onclick="openCase(<?php echo $case['id']; ?>, <?php echo $case['price']; ?>)"
                            <?php echo $_SESSION['balance'] < $case['price'] ? 'disabled' : ''; ?>>
                        <?php echo $_SESSION['balance'] < $case['price'] ? 'Недостаточно монет' : 'Открыть кейс'; ?>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Recent Drops -->
        <section class="recent-drops">
            <div class="section-header">
                <h2 class="section-title">Последние выпадения</h2>
            </div>
            <div class="drops-container" id="recentDrops">
                <!-- Здесь будут отображаться последние выпадения -->
                <div class="drop-item">
                    <div class="drop-player">Игрок1</div>
                    <div class="drop-item-name">AK-47 | Redline</div>
                    <div class="drop-rarity covert">Covert</div>
                </div>
                <div class="drop-item">
                    <div class="drop-player">Игрок2</div>
                    <div class="drop-item-name">★ Karambit | Fade</div>
                    <div class="drop-rarity special">Special</div>
                </div>
            </div>
        </section>
    </div>

    <!-- Case Opening Modal -->
    <div id="caseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalCaseName">Открытие кейса</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="case-opening-animation" id="caseAnimation">
                    <div class="roulette-container">
                        <div class="roulette-items" id="rouletteItems"></div>
                        <div class="roulette-pointer"></div>
                    </div>
                </div>
                <div class="result-container" id="resultContainer" style="display: none;">
                    <div class="result-item">
                        <div class="item-image">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="item-info">
                            <h4 id="resultItemName"></h4>
                            <p id="resultItemType"></p>
                            <div class="item-rarity" id="resultItemRarity"></div>
                        </div>
                    </div>
                    <button class="btn-primary" onclick="addToInventory()">Добавить в инвентарь</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>