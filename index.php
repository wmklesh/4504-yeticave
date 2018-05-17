<?php

require __DIR__ . '/core/bootstrap.php';
require __DIR__ . '/data.php';

$lotList = getLotList(9);
$lotListContent = '';
foreach ($lotList as $lot) {
    $lotListContent .= includeTemplate('lot-item', $lot);
}

$pageContent = includeTemplate('index', ['lotListContent' => $lotListContent]);

$categoryList = getCatList();
$catListContent = '';
foreach ($categoryList as $category) {
    $catListContent .= includeTemplate('nav-item', ['name' => $category['name']]);
}

$layoutContent = includeTemplate('layout', [
    'content' => $pageContent,
    'catListContent' => $catListContent,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'user_avatar' => $user_avatar,
    'title' => 'Yeticave - Главная страница'
]);

echo $layoutContent;
