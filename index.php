<?php

date_default_timezone_set(‘Europe/Moscow’);

require __DIR__ . '/functions.php';
require __DIR__ . '/data.php';

$link = mysqli_connect('localhost', 'root', 'root', 'yeticave');
mysqli_set_charset($link, 'utf8');

if (! $link) {
    print('Ошибка: Невозможно подключиться к MySQL  ' . mysqli_connect_error());
}

$sql = 'SELECT l.name, l.price, img, end_time, c.name category_name, IFNULL(b.count_bet, 0) count_bet
        FROM lot l
          JOIN category c ON c.id = l.category_id
          LEFT JOIN
            (SELECT b.lot_id, COUNT(b.id) count_bet FROM bet b GROUP BY b.lot_id) b
          ON b.lot_id = l.id
        WHERE l.end_time > NOW()
        ORDER BY l.add_time DESC
        LIMIT 9';
$lotList = mysqli_query($link, $sql);

$lotListContent = '';
while ($lot = mysqli_fetch_array($lotList)) {
    $lotListContent .= includeTemplate('lot', $lot);
}
$pageContent = includeTemplate('index', ['lotListContent' => $lotListContent]);

$sql = 'SELECT * FROM category';
$categoryList = mysqli_query($link, $sql);

$catListContent = '';
while ($category = mysqli_fetch_array($categoryList)) {
    $catListContent .= includeTemplate('nav', ['name' => $category['name']]);
}

$layoutContent = includeTemplate('layout', [
    'content' => $pageContent,
    'lotListContent' => $lotListContent,
    'catListContent' => $catListContent,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'user_avatar' => $user_avatar,
    'title' => 'Yeticave - Главная страница'
]);

echo $layoutContent;
