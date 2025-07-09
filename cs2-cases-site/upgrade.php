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
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Апгрейд - CS2 Case Opening</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .upgrade-section {
            padding: 60px 0;
        }

        .upgrade-container {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 40px;
            align-items: start;
            margin-bottom: 40px;
        }

        .upgrade-side {
            background: linear-gradient(145deg, #1a1a1a, #0f0f0f);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid #333;
        }

        .upgrade-side h3 {
            color: #ffffff;
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.3rem;
        }

        .upgrade-arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #ff6b35, #ff8c42);
            border-radius: 50%;
            color: white;
            font-size: 2rem;
            align-self: center;
        }

        .item-selector {
            min-height: 200px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .selected-item, .target-item-card {
            background: linear-gradient(135deg, #2a2a2a, #1a1a1a);
            border-radius: 15px;
            padding: 20px;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .selected-item.active {
            border-color: #ff6b35;
            background: linear-gradient(135deg, #3a2a1a, #2a1a0a);
        }

        .target-item-card:hover {
            border-color: #ff6b35;
            transform: translateY(-3px);
        }

        .empty-slot {
            border: 2px dashed #666;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 150px;
            border-radius: 15px;
        }

        .item-details {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .item-icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .item-name {
            font-weight: 600;
            color: #ffffff;
        }

        .item-type {
            color: #cccccc;
            font-size: 0.9rem;
        }

        .item-price {
            color: #ff6b35;
            font-weight: 600;
            margin-top: 5px;
        }

        .rarity-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 8px;
        }

        .inventory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            max-height: 300px;
            overflow-y: auto;
        }

        .inventory-item {
            background: linear-gradient(135deg, #2a2a2a, #1a1a1a);
            border-radius: 12px;
            padding: 15px;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .inventory-item:hover {
            border-color: #ff6b35;
            transform: translateY(-2px);
        }

        .inventory-item.selected {
            border-color: #ff6b35;
            background: linear-gradient(135deg, #3a2a1a, #2a1a0a);
        }

        .upgrade-info {
            background: linear-gradient(135deg, #0a2a1a, #0a1a0a);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid #2a5a3a;
            margin: 30px 0;
            text-align: center;
        }

        .upgrade-cost {
            font-size: 1.5rem;
            color: #ff6b35;
            font-weight: 700;
            margin: 15px 0;
            font-family: 'Orbitron', monospace;
        }

        .upgrade-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #28a745, #34ce57);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 20px;
        }

        .upgrade-btn:hover:not(:disabled) {
            background: linear-gradient(45deg, #34ce57, #28a745);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }

        .upgrade-btn:disabled {
            background: #666;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .target-items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
        }

        .rarity-section {
            margin-bottom: 30px;
        }

        .rarity-title {
            color: #ffffff;
            margin-bottom: 15px;
            font-size: 1.1rem;
            padding: 10px 0;
            border-bottom: 1px solid #333;
        }

        @media (max-width: 768px) {
            .upgrade-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .upgrade-arrow {
                transform: rotate(90deg);
                justify-self: center;
            }
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
                    <a href="upgrade.php" class="nav-link active">Апгрейд</a>
                    <a href="stats.php" class="nav-link">Статистика</a>
                    <a href="profile.php" class="nav-link">Профиль</a>
                </nav>
                <div class="balance">
                    <i class="fas fa-coins"></i>
                    <span id="balance"><?php echo number_format($_SESSION['balance']); ?></span>
                </div>
            </div>
        </header>

        <!-- Upgrade Section -->
        <section class="upgrade-section">
            <div class="section-header">
                <h2 class="section-title">Апгрейд предметов</h2>
                <p class="section-subtitle">Обменяйте свои предметы на более редкие</p>
            </div>

            <!-- Upgrade Container -->
            <div class="upgrade-container">
                <!-- From Item -->
                <div class="upgrade-side">
                    <h3>Выберите предмет для апгрейда</h3>
                    <div class="item-selector">
                        <div id="selectedItem" class="empty-slot">
                            <span>Выберите предмет из инвентаря</span>
                        </div>
                        
                        <div class="inventory-grid">
                            <?php if (empty($_SESSION['inventory'])): ?>
                                <div class="empty-slot" style="grid-column: 1 / -1;">
                                    <div>
                                        <i class="fas fa-box-open" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                                        Инвентарь пуст
                                        <br>
                                        <a href="index.php" style="color: #ff6b35; text-decoration: none;">Открыть кейсы</a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($_SESSION['inventory'] as $item): ?>
                                <div class="inventory-item" onclick="selectItem('<?php echo htmlspecialchars($item['id']); ?>')" data-item-id="<?php echo htmlspecialchars($item['id']); ?>">
                                    <div class="item-icon" style="color: <?php echo $item['color']; ?>; font-size: 2rem;">
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
                                    <div class="item-name"><?php echo htmlspecialchars(strlen($item['name']) > 15 ? substr($item['name'], 0, 15) . '...' : $item['name']); ?></div>
                                    <div class="rarity-badge" style="background-color: <?php echo $item['color']; ?>; color: <?php echo ($item['rarity'] === 'consumer' || $item['rarity'] === 'special') ? '#000' : '#fff'; ?>;">
                                        <?php echo htmlspecialchars($item['rarity_name']); ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Upgrade Arrow -->
                <div class="upgrade-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>

                <!-- To Item -->
                <div class="upgrade-side">
                    <h3>Выберите целевой предмет</h3>
                    <div class="item-selector" id="targetItems">
                        <div class="empty-slot">
                            <span>Сначала выберите предмет для апгрейда</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upgrade Info -->
            <div id="upgradeInfo" class="upgrade-info" style="display: none;">
                <h4>Информация об апгрейде</h4>
                <div class="upgrade-cost">
                    Стоимость: <span id="upgradeCostAmount">0</span> монет
                </div>
                <p>Комиссия 10% включена в стоимость</p>
                <button id="upgradeBtn" class="upgrade-btn" onclick="performUpgrade()" disabled>
                    Выполнить апгрейд
                </button>
            </div>
        </section>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        let selectedFromItem = null;
        let selectedToItem = null;
        let upgradeCost = 0;

        function selectItem(itemId) {
            // Убираем выделение с предыдущего элемента
            document.querySelectorAll('.inventory-item').forEach(item => {
                item.classList.remove('selected');
            });

            // Выделяем новый элемент
            const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
            itemElement.classList.add('selected');

            // Получаем данные предмета
            fetch('get_item_data.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({item_id: itemId})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    selectedFromItem = data.item;
                    updateSelectedItem();
                    loadTargetItems();
                } else {
                    alert('Ошибка при получении данных предмета');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка!');
            });
        }

        function updateSelectedItem() {
            const selectedItemDiv = document.getElementById('selectedItem');
            if (selectedFromItem) {
                const itemPrice = parseInt(selectedFromItem.price || 0);
                selectedItemDiv.innerHTML = `
                    <div class="item-details">
                        <div class="item-icon" style="color: ${selectedFromItem.color};">
                            <i class="fas fa-${getItemIcon(selectedFromItem.type)}"></i>
                        </div>
                        <div class="item-name">${selectedFromItem.name}</div>
                        <div class="item-type">${selectedFromItem.type}</div>
                        <div class="item-price">${itemPrice.toLocaleString()} монет</div>
                        <div class="rarity-badge" style="background-color: ${selectedFromItem.color}; color: ${(selectedFromItem.rarity === 'consumer' || selectedFromItem.rarity === 'special') ? '#000' : '#fff'};">
                            ${selectedFromItem.rarity_name}
                        </div>
                    </div>
                `;
                selectedItemDiv.className = 'selected-item active';
            }
        }

        function loadTargetItems() {
            if (!selectedFromItem) return;

            fetch('get_upgrade_targets.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({from_rarity: selectedFromItem.rarity})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayTargetItems(data.items);
                } else {
                    document.getElementById('targetItems').innerHTML = '<div class="empty-slot"><span>Нет доступных предметов для апгрейда</span></div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка при загрузке целевых предметов!');
            });
        }

        function displayTargetItems(items) {
            const targetItemsDiv = document.getElementById('targetItems');
            
            if (items.length === 0) {
                targetItemsDiv.innerHTML = '<div class="empty-slot"><span>Нет доступных предметов для апгрейда</span></div>';
                return;
            }

            // Группируем по редкости
            const groupedItems = {};
            items.forEach(item => {
                if (!groupedItems[item.rarity]) {
                    groupedItems[item.rarity] = [];
                }
                groupedItems[item.rarity].push(item);
            });

            let html = '';
            for (const [rarity, rarityItems] of Object.entries(groupedItems)) {
                html += `
                    <div class="rarity-section">
                        <div class="rarity-title">${rarityItems[0].rarity_name}</div>
                        <div class="target-items-grid">
                `;
                
                rarityItems.forEach(item => {
                    const itemPrice = parseInt(item.price || 0);
                    html += `
                        <div class="target-item-card" onclick="selectTargetItem('${item.name}')">
                            <div class="item-details">
                                <div class="item-icon" style="color: ${item.color};">
                                    <i class="fas fa-${getItemIcon(item.type)}"></i>
                                </div>
                                <div class="item-name">${item.name.length > 12 ? item.name.substring(0, 12) + '...' : item.name}</div>
                                <div class="item-type">${item.type}</div>
                                <div class="item-price">${itemPrice.toLocaleString()} монет</div>
                                <div class="rarity-badge" style="background-color: ${item.color}; color: ${(item.rarity === 'consumer' || item.rarity === 'special') ? '#000' : '#fff'};">
                                    ${item.rarity_name}
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div></div>';
            }

            targetItemsDiv.innerHTML = html;
        }

        function selectTargetItem(itemName) {
            // Убираем выделение с предыдущих элементов
            document.querySelectorAll('.target-item-card').forEach(item => {
                item.style.borderColor = 'transparent';
            });

            // Выделяем новый элемент
            event.target.closest('.target-item-card').style.borderColor = '#ff6b35';

            // Получаем данные о стоимости апгрейда
            fetch('get_upgrade_cost.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    from_item_name: selectedFromItem.name,
                    to_item_name: itemName
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    selectedToItem = itemName;
                    upgradeCost = data.cost;
                    updateUpgradeInfo();
                } else {
                    alert('Ошибка при расчете стоимости апгрейда: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка!');
            });
        }

        function updateUpgradeInfo() {
            const upgradeInfo = document.getElementById('upgradeInfo');
            const upgradeCostAmount = document.getElementById('upgradeCostAmount');
            const upgradeBtn = document.getElementById('upgradeBtn');
            const currentBalance = parseInt(document.getElementById('balance').textContent.replace(/\s/g, ''));

            upgradeCostAmount.textContent = upgradeCost.toLocaleString();
            upgradeInfo.style.display = 'block';

            if (currentBalance >= upgradeCost) {
                upgradeBtn.disabled = false;
                upgradeBtn.textContent = 'Выполнить апгрейд';
            } else {
                upgradeBtn.disabled = true;
                upgradeBtn.textContent = `Недостаточно средств (нужно ${upgradeCost.toLocaleString()})`;
            }
        }

        function performUpgrade() {
            if (!selectedFromItem || !selectedToItem) {
                alert('Выберите предметы для апгрейда');
                return;
            }

            fetch('upgrade_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    from_item_id: selectedFromItem.id,
                    to_item_name: selectedToItem
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Апгрейд успешно выполнен! Получен: ${data.new_item.name}`);
                    
                    // Обновляем баланс
                    document.getElementById('balance').textContent = data.new_balance.toLocaleString();
                    
                    // Перезагружаем страницу для обновления инвентаря
                    window.location.reload();
                } else {
                    alert('Ошибка апгрейда: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка при выполнении апгрейда!');
            });
        }

        function getItemIcon(type) {
            const icons = {
                'Pistol': 'gun',
                'Rifle': 'crosshairs',
                'SMG': 'bomb',
                'Shotgun': 'bullseye',
                'Sniper': 'crosshairs',
                'Knife': 'cut',
                'Weapon': 'gun'
            };
            return icons[type] || 'trophy';
        }
    </script>
</body>
</html>