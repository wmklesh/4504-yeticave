<?php

date_default_timezone_set(‘Europe/Moscow’);

require __DIR__ . '/functions.php';
require __DIR__ . '/data.php';

$connection = mysqli_connect('localhost', 'root', 'root', 'yeticave');
mysqli_set_charset($connection, 'utf8');

if (! $connection) {
    print('Ошибка: Невозможно подключиться к MySQL  ' . mysqli_connect_error());
    die();
}

$lotList = getLotList($connection, 9);
$lotListContent = '';
foreach ($lotList as $lot) {
    $lotListContent .= includeTemplate('lot', $lot);
}

$pageContent = includeTemplate('index', ['lotListContent' => $lotListContent]);

$categoryList = getCatList($connection);
$catListContent = '';
foreach ($categoryList as $category) {
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
