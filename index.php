<?php

date_default_timezone_set(‘Europe/Moscow’);

require __DIR__ . '/functions.php';
require __DIR__ . '/data.php';

$lotListContent = '';
foreach ($lotList as $lot) {
    $lotListContent .= includeTemplate('lot', $lot);
}
$pageContent = includeTemplate('index', ['lotListContent' => $lotListContent]);

$catListContent = '';
foreach ($categoryList as $category) {
    $catListContent .= includeTemplate('nav', ['name' => $category]);
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
