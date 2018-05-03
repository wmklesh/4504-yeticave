<?php

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
    'title' => 'Yeticave - Главная страница'
]);

echo $layoutContent;
