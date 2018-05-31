<?php

require __DIR__ . '/core/bootstrap.php';
require __DIR__ . '/getwinner.php';

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
    'isAuth' => empty(getCurrentUser()) ? false : true,
    'userName' => getCurrentUser()['name'] ?? null,
    'userAvatar' => getCurrentUser()['avatar'] ?? null,
    'title' => 'Yeticave - Главная страница'
]);

echo $layoutContent;
