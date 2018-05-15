<?php

date_default_timezone_set(‘Europe/Moscow’);
ini_set('error_reporting', E_ALL & ~E_NOTICE);

require __DIR__ . '/functions.php';
require __DIR__ . '/mysql_helper.php';
require __DIR__ . '/data.php';

$lotList = getLotList(9);
$lotListContent = '';
foreach ($lotList as $lot) {
    $lotListContent .= includeTemplate('lot', $lot);
}

$pageContent = includeTemplate('index', ['lotListContent' => $lotListContent]);

$categoryList = getCatList();
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
