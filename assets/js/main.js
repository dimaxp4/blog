// Глобальные переменные
let currentBalance = 0;
let currentWinningItem = null;
let isAnimationRunning = false;

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    updateBalance();
    setupEventListeners();
    generateRandomDrops();
});

// Обновление баланса
function updateBalance() {
    fetch('get_balance.php')
        .then(response => response.json())
        .then(data => {
            currentBalance = data.balance;
            document.getElementById('balance').textContent = formatNumber(currentBalance);
            updateCaseButtons();
        })
        .catch(error => console.error('Error:', error));
}

// Форматирование чисел
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

// Настройка обработчиков событий
function setupEventListeners() {
    // Закрытие модального окна при клике вне его
    window.onclick = function(event) {
        const modal = document.getElementById('caseModal');
        if (event.target === modal) {
            closeModal();
        }
    };
    
    // Закрытие модального окна клавишей Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });
}

// Обновление состояния кнопок открытия кейсов
function updateCaseButtons() {
    const buttons = document.querySelectorAll('.open-case-btn');
    buttons.forEach(button => {
        const casePrice = parseInt(button.onclick.toString().match(/\d+/g)[1]);
        if (currentBalance < casePrice) {
            button.disabled = true;
            button.textContent = 'Недостаточно монет';
        } else {
            button.disabled = false;
            button.textContent = 'Открыть кейс';
        }
    });
}

// Основная функция открытия кейса
function openCase(caseId, price) {
    if (isAnimationRunning) return;
    
    if (currentBalance < price) {
        showNotification('Недостаточно монет!', 'error');
        return;
    }

    isAnimationRunning = true;
    
    // Открываем модальное окно
    const modal = document.getElementById('caseModal');
    const caseName = document.querySelector(`[data-case-id="${caseId}"] .case-name`).textContent;
    document.getElementById('modalCaseName').textContent = `Открытие ${caseName}`;
    
    modal.style.display = 'block';
    
    // Скрываем результат и показываем анимацию
    document.getElementById('resultContainer').style.display = 'none';
    document.getElementById('caseAnimation').style.display = 'block';
    
    // Запускаем анимацию рулетки
    startRouletteAnimation(caseId, price);
}

// Анимация рулетки
function startRouletteAnimation(caseId, price) {
    const rouletteItems = document.getElementById('rouletteItems');
    
    // Получаем случайный предмет с сервера
    fetch('open_case.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            case_id: caseId,
            price: price
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentWinningItem = data.item;
            currentBalance = data.new_balance;
            
            // Генерируем предметы для рулетки
            generateRouletteItems(data.item);
            
            // Запускаем анимацию
            setTimeout(() => {
                animateRoulette();
            }, 500);
        } else {
            showNotification(data.message, 'error');
            closeModal();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Произошла ошибка!', 'error');
        closeModal();
    });
}

// Генерация предметов для рулетки
function generateRouletteItems(winningItem) {
    const rouletteItems = document.getElementById('rouletteItems');
    rouletteItems.innerHTML = '';
    
    // Генерируем случайные предметы
    const items = [];
    const rarities = ['consumer', 'industrial', 'mil_spec', 'restricted', 'classified', 'covert', 'special'];
    const colors = {
        'consumer': '#b0c3d9',
        'industrial': '#5e98d9',
        'mil_spec': '#4b69ff',
        'restricted': '#8847ff',
        'classified': '#d32ce6',
        'covert': '#eb4b4b',
        'special': '#ffd700'
    };
    
    // Добавляем 50 случайных предметов
    for (let i = 0; i < 50; i++) {
        const randomRarity = rarities[Math.floor(Math.random() * rarities.length)];
        items.push({
            name: `Случайный предмет ${i + 1}`,
            rarity: randomRarity,
            color: colors[randomRarity]
        });
    }
    
    // Вставляем выигрышный предмет в позицию 25 (центр)
    items[25] = winningItem;
    
    // Создаем элементы рулетки
    items.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.className = 'roulette-item';
        itemElement.style.backgroundColor = item.color;
        itemElement.style.color = item.rarity === 'consumer' || item.rarity === 'special' ? '#000' : '#fff';
        
        itemElement.innerHTML = `
            <div class="item-icon">
                <i class="fas fa-${getItemIcon(item.type || 'Weapon')}"></i>
            </div>
            <div class="item-name">${item.name.length > 15 ? item.name.substring(0, 15) + '...' : item.name}</div>
        `;
        
        rouletteItems.appendChild(itemElement);
    });
}

// Получение иконки для типа предмета
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

// Анимация рулетки
function animateRoulette() {
    const rouletteItems = document.getElementById('rouletteItems');
    const itemWidth = 124; // 120px ширина + 4px margin
    const totalItems = 50;
    const winningPosition = 25;
    
    // Случайное смещение для более реалистичной анимации
    const randomOffset = Math.random() * 100 - 50;
    const targetPosition = -(winningPosition * itemWidth) + (rouletteItems.parentElement.offsetWidth / 2) - (itemWidth / 2) + randomOffset;
    
    rouletteItems.style.transform = `translateX(${targetPosition}px)`;
    
    // Показываем результат после анимации
    setTimeout(() => {
        showResult();
    }, 3500);
}

// Показ результата
function showResult() {
    document.getElementById('caseAnimation').style.display = 'none';
    document.getElementById('resultContainer').style.display = 'block';
    
    // Заполняем информацию о предмете
    document.getElementById('resultItemName').textContent = currentWinningItem.name;
    document.getElementById('resultItemType').textContent = currentWinningItem.type;
    
    const rarityElement = document.getElementById('resultItemRarity');
    rarityElement.textContent = currentWinningItem.rarity_name;
    rarityElement.className = `item-rarity ${currentWinningItem.rarity}`;
    rarityElement.style.backgroundColor = currentWinningItem.color;
    rarityElement.style.color = currentWinningItem.rarity === 'consumer' || currentWinningItem.rarity === 'special' ? '#000' : '#fff';
    
    // Обновляем баланс
    document.getElementById('balance').textContent = formatNumber(currentBalance);
    updateCaseButtons();
    
    // Добавляем в список последних выпадений
    addToRecentDrops(currentWinningItem);
    
    isAnimationRunning = false;
}

// Добавление предмета в инвентарь
function addToInventory() {
    fetch('add_to_inventory.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(currentWinningItem)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Предмет добавлен в инвентарь!', 'success');
            closeModal();
            
            // Обновляем счетчик предметов в инвентаре
            const inventoryCount = document.querySelector('.stat-number');
            if (inventoryCount) {
                inventoryCount.textContent = parseInt(inventoryCount.textContent) + 1;
            }
        } else {
            showNotification('Ошибка при добавлении в инвентарь!', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Произошла ошибка!', 'error');
    });
}

// Закрытие модального окна
function closeModal() {
    const modal = document.getElementById('caseModal');
    modal.style.display = 'none';
    isAnimationRunning = false;
    
    // Сброс состояния рулетки
    const rouletteItems = document.getElementById('rouletteItems');
    rouletteItems.style.transform = 'translateX(0)';
}

// Добавление в список последних выпадений
function addToRecentDrops(item) {
    const recentDrops = document.getElementById('recentDrops');
    const dropItem = document.createElement('div');
    dropItem.className = 'drop-item';
    
    dropItem.innerHTML = `
        <div class="drop-player">Вы</div>
        <div class="drop-item-name">${item.name}</div>
        <div class="drop-rarity ${item.rarity}">${item.rarity_name}</div>
    `;
    
    // Добавляем в начало списка
    recentDrops.insertBefore(dropItem, recentDrops.firstChild);
    
    // Ограничиваем количество отображаемых элементов
    while (recentDrops.children.length > 10) {
        recentDrops.removeChild(recentDrops.lastChild);
    }
    
    // Анимация появления
    dropItem.style.opacity = '0';
    dropItem.style.transform = 'translateX(-20px)';
    setTimeout(() => {
        dropItem.style.transition = 'all 0.3s ease';
        dropItem.style.opacity = '1';
        dropItem.style.transform = 'translateX(0)';
    }, 100);
}

// Генерация случайных выпадений для демонстрации
function generateRandomDrops() {
    const players = ['Игрок1', 'ProGamer', 'CSMaster', 'Sniper123', 'AKLover', 'Headshot'];
    const items = [
        'AK-47 | Redline',
        'M4A4 | Asiimov',
        'AWP | Dragon Lore',
        '★ Karambit | Fade',
        'Glock-18 | Water Elemental',
        'P250 | Sand Dune'
    ];
    const rarities = ['covert', 'classified', 'special', 'restricted', 'mil_spec', 'consumer'];
    
    setInterval(() => {
        if (!isAnimationRunning) {
            const randomPlayer = players[Math.floor(Math.random() * players.length)];
            const randomItem = items[Math.floor(Math.random() * items.length)];
            const randomRarity = rarities[Math.floor(Math.random() * rarities.length)];
            
            addToRecentDrops({
                name: randomItem,
                rarity: randomRarity,
                rarity_name: randomRarity.charAt(0).toUpperCase() + randomRarity.slice(1)
            });
        }
    }, 5000);
}

// Показ уведомлений
function showNotification(message, type) {
    // Создаем элемент уведомления
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    // Стили для уведомления
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 3000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        ${type === 'success' ? 'background: linear-gradient(45deg, #28a745, #34ce57);' : 'background: linear-gradient(45deg, #dc3545, #e74c3c);'}
    `;
    
    document.body.appendChild(notification);
    
    // Анимация появления
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Удаление через 3 секунды
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}