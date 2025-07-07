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

// Предметы по типам
$items = [
    'consumer' => [
        ['name' => 'P2000 | Granite Marbleized', 'type' => 'Pistol'],
        ['name' => 'Tec-9 | Army Mesh', 'type' => 'Pistol'],
        ['name' => 'MAC-10 | Silver', 'type' => 'SMG'],
        ['name' => 'MP9 | Storm', 'type' => 'SMG'],
        ['name' => 'UMP-45 | Urban DDPAT', 'type' => 'SMG'],
        ['name' => 'P90 | Storm', 'type' => 'SMG'],
        ['name' => 'Galil AR | Hunting Blind', 'type' => 'Rifle'],
        ['name' => 'FAMAS | Colony', 'type' => 'Rifle'],
        ['name' => 'M4A4 | Urban DDPAT', 'type' => 'Rifle'],
        ['name' => 'AK-47 | Safari Mesh', 'type' => 'Rifle']
    ],
    'industrial' => [
        ['name' => 'P250 | Steel Disruption', 'type' => 'Pistol'],
        ['name' => 'Five-SeveN | Forest Night', 'type' => 'Pistol'],
        ['name' => 'MP7 | Forest DDPAT', 'type' => 'SMG'],
        ['name' => 'MP5-SD | Phosphor', 'type' => 'SMG'],
        ['name' => 'XM1014 | Blue Steel', 'type' => 'Shotgun'],
        ['name' => 'G3SG1 | Green Apple', 'type' => 'Sniper'],
        ['name' => 'SCAR-20 | Green Marine', 'type' => 'Sniper']
    ],
    'mil_spec' => [
        ['name' => 'Glock-18 | Blue Fissure', 'type' => 'Pistol'],
        ['name' => 'P250 | Hive', 'type' => 'Pistol'],
        ['name' => 'MP9 | Deadly Poison', 'type' => 'SMG'],
        ['name' => 'UMP-45 | Corporal', 'type' => 'SMG'],
        ['name' => 'M4A1-S | Bright Water', 'type' => 'Rifle'],
        ['name' => 'AK-47 | Blue Laminate', 'type' => 'Rifle']
    ],
    'restricted' => [
        ['name' => 'Desert Eagle | Cobalt Disruption', 'type' => 'Pistol'],
        ['name' => 'P90 | Cold Blooded', 'type' => 'SMG'],
        ['name' => 'AWP | Electric Hive', 'type' => 'Sniper'],
        ['name' => 'M4A4 | X-Ray', 'type' => 'Rifle']
    ],
    'classified' => [
        ['name' => 'AK-47 | Case Hardened', 'type' => 'Rifle'],
        ['name' => 'M4A1-S | Atomic Alloy', 'type' => 'Rifle'],
        ['name' => 'AWP | Lightning Strike', 'type' => 'Sniper']
    ],
    'covert' => [
        ['name' => 'AK-47 | Fire Serpent', 'type' => 'Rifle'],
        ['name' => 'M4A4 | Howl', 'type' => 'Rifle'],
        ['name' => 'AWP | Dragon Lore', 'type' => 'Sniper']
    ],
    'special' => [
        ['name' => '★ Karambit | Fade', 'type' => 'Knife'],
        ['name' => '★ Butterfly Knife | Crimson Web', 'type' => 'Knife'],
        ['name' => '★ M9 Bayonet | Doppler', 'type' => 'Knife'],
        ['name' => '★ Gut Knife | Tiger Tooth', 'type' => 'Knife']
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
?>