<?php
// Конфигурация предметов CS2

// Типы редкости с шансами выпадения (в процентах)
$rarities = [
    'consumer' => ['name' => 'Consumer Grade', 'color' => '#b0c3d9', 'chance' => 79.92],
    'industrial' => ['name' => 'Industrial Grade', 'color' => '#5e98d9', 'chance' => 15.98],
    'mil_spec' => ['name' => 'Mil-Spec Grade', 'color' => '#4b69ff', 'chance' => 3.2],
    'restricted' => ['name' => 'Restricted', 'color' => '#8847ff', 'chance' => 0.64],
    'classified' => ['name' => 'Classified', 'color' => '#d32ce6', 'chance' => 0.32],
    'covert' => ['name' => 'Covert', 'color' => '#eb4b4b', 'chance' => 0.64],
    'special' => ['name' => 'Special Items', 'color' => '#ffd700', 'chance' => 0.26]
];

// Предметы по типам с ценами
$items = [
    'consumer' => [
        ['name' => 'P2000 | Granite Marbleized', 'type' => 'Pistol', 'price' => 15],
        ['name' => 'Tec-9 | Army Mesh', 'type' => 'Pistol', 'price' => 12],
        ['name' => 'MAC-10 | Silver', 'type' => 'SMG', 'price' => 18],
        ['name' => 'MP9 | Storm', 'type' => 'SMG', 'price' => 20],
        ['name' => 'UMP-45 | Urban DDPAT', 'type' => 'SMG', 'price' => 25],
        ['name' => 'P90 | Storm', 'type' => 'SMG', 'price' => 30],
        ['name' => 'Galil AR | Hunting Blind', 'type' => 'Rifle', 'price' => 35],
        ['name' => 'FAMAS | Colony', 'type' => 'Rifle', 'price' => 40],
        ['name' => 'M4A4 | Urban DDPAT', 'type' => 'Rifle', 'price' => 45],
        ['name' => 'AK-47 | Safari Mesh', 'type' => 'Rifle', 'price' => 50]
    ],
    'industrial' => [
        ['name' => 'P250 | Steel Disruption', 'type' => 'Pistol', 'price' => 80],
        ['name' => 'Five-SeveN | Forest Night', 'type' => 'Pistol', 'price' => 85],
        ['name' => 'MP7 | Forest DDPAT', 'type' => 'SMG', 'price' => 90],
        ['name' => 'MP5-SD | Phosphor', 'type' => 'SMG', 'price' => 95],
        ['name' => 'XM1014 | Blue Steel', 'type' => 'Shotgun', 'price' => 100],
        ['name' => 'G3SG1 | Green Apple', 'type' => 'Sniper', 'price' => 110],
        ['name' => 'SCAR-20 | Green Marine', 'type' => 'Sniper', 'price' => 120]
    ],
    'mil_spec' => [
        ['name' => 'Glock-18 | Blue Fissure', 'type' => 'Pistol', 'price' => 350],
        ['name' => 'P250 | Hive', 'type' => 'Pistol', 'price' => 380],
        ['name' => 'MP9 | Deadly Poison', 'type' => 'SMG', 'price' => 420],
        ['name' => 'UMP-45 | Corporal', 'type' => 'SMG', 'price' => 450],
        ['name' => 'M4A1-S | Bright Water', 'type' => 'Rifle', 'price' => 500],
        ['name' => 'AK-47 | Blue Laminate', 'type' => 'Rifle', 'price' => 580]
    ],
    'restricted' => [
        ['name' => 'Desert Eagle | Cobalt Disruption', 'type' => 'Pistol', 'price' => 1200],
        ['name' => 'P90 | Cold Blooded', 'type' => 'SMG', 'price' => 1350],
        ['name' => 'AWP | Electric Hive', 'type' => 'Sniper', 'price' => 1800],
        ['name' => 'M4A4 | X-Ray', 'type' => 'Rifle', 'price' => 1500]
    ],
    'classified' => [
        ['name' => 'AK-47 | Case Hardened', 'type' => 'Rifle', 'price' => 4500],
        ['name' => 'M4A1-S | Atomic Alloy', 'type' => 'Rifle', 'price' => 4200],
        ['name' => 'AWP | Lightning Strike', 'type' => 'Sniper', 'price' => 5800]
    ],
    'covert' => [
        ['name' => 'AK-47 | Fire Serpent', 'type' => 'Rifle', 'price' => 15000],
        ['name' => 'M4A4 | Howl', 'type' => 'Rifle', 'price' => 18000],
        ['name' => 'AWP | Dragon Lore', 'type' => 'Sniper', 'price' => 25000]
    ],
    'special' => [
        ['name' => '★ Karambit | Fade', 'type' => 'Knife', 'price' => 45000],
        ['name' => '★ Butterfly Knife | Crimson Web', 'type' => 'Knife', 'price' => 35000],
        ['name' => '★ M9 Bayonet | Doppler', 'type' => 'Knife', 'price' => 40000],
        ['name' => '★ Gut Knife | Tiger Tooth', 'type' => 'Knife', 'price' => 28000]
    ]
];

// Доступные кейсы
$cases = [
    [
        'id' => 1,
        'name' => 'Chroma Case',
        'image' => 'chroma_case.png',
        'price' => 250
    ],
    [
        'id' => 2,
        'name' => 'Phoenix Case',
        'image' => 'phoenix_case.png',
        'price' => 200
    ],
    [
        'id' => 3,
        'name' => 'Gamma Case',
        'image' => 'gamma_case.png',
        'price' => 300
    ],
    [
        'id' => 4,
        'name' => 'Revolution Case',
        'image' => 'revolution_case.png',
        'price' => 350
    ]
];

// Функция для получения случайного предмета
function getRandomItem($rarities, $items) {
    $rand = mt_rand(1, 10000) / 100;
    $cumulative = 0;
    
    foreach ($rarities as $rarity => $data) {
        $cumulative += $data['chance'];
        if ($rand <= $cumulative) {
            $itemsInRarity = $items[$rarity];
            $randomItem = $itemsInRarity[array_rand($itemsInRarity)];
            $randomItem['rarity'] = $rarity;
            $randomItem['rarity_name'] = $data['name'];
            $randomItem['color'] = $data['color'];
            return $randomItem;
        }
    }
    
    // Fallback на consumer grade
    $item = $items['consumer'][array_rand($items['consumer'])];
    $item['rarity'] = 'consumer';
    $item['rarity_name'] = $rarities['consumer']['name'];
    $item['color'] = $rarities['consumer']['color'];
    return $item;
}

// Функция для поиска предмета по имени
function findItemByName($itemName, $items, $rarities) {
    foreach ($items as $rarity => $rarityItems) {
        foreach ($rarityItems as $item) {
            if ($item['name'] === $itemName) {
                $item['rarity'] = $rarity;
                $item['rarity_name'] = $rarities[$rarity]['name'];
                $item['color'] = $rarities[$rarity]['color'];
                return $item;
            }
        }
    }
    return null;
}

// Функция для получения всех предметов определенной редкости
function getItemsByRarity($rarity, $items, $rarities) {
    if (!isset($items[$rarity])) {
        return [];
    }
    
    $result = [];
    foreach ($items[$rarity] as $item) {
        $item['rarity'] = $rarity;
        $item['rarity_name'] = $rarities[$rarity]['name'];
        $item['color'] = $rarities[$rarity]['color'];
        $result[] = $item;
    }
    return $result;
}

// Функция для определения возможности апгрейда
function canUpgrade($fromRarity, $toRarity) {
    $rarityLevels = ['consumer' => 1, 'industrial' => 2, 'mil_spec' => 3, 'restricted' => 4, 'classified' => 5, 'covert' => 6, 'special' => 7];
    
    $fromLevel = $rarityLevels[$fromRarity] ?? 0;
    $toLevel = $rarityLevels[$toRarity] ?? 0;
    
    return $toLevel > $fromLevel && $toLevel <= $fromLevel + 2;
}

// Функция для расчета стоимости апгрейда
function getUpgradeCost($fromItem, $toItem) {
    $baseCost = $toItem['price'] - $fromItem['price'];
    $fee = $baseCost * 0.1; // 10% комиссия
    return max(0, $baseCost + $fee);
}
?>