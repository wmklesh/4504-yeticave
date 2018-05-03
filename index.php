<?php

date_default_timezone_set(‘Europe/Moscow’);

require __DIR__ . '/functions.php';
require __DIR__ . '/data.php';

$lotsContent = '';
foreach ($lotList as $lot) {
    $lotsContent .= includeTemplate('lot', $lot);
}
$pageContent = includeTemplate('index', ['content' => $lotsContent]);
$layoutContent = includeTemplate('layout', [
    'content' => $pageContent,
    'categoryList' => $categoryList,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'user_avatar' => $user_avatar,
    'title' => 'Yeticave - Главная страница'
]);

echo $layoutContent;
