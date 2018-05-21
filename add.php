<?php

require __DIR__ . '/core/bootstrap.php';

if ($_POST) {
    $lot = $_POST['lot'];
    $resultAddLot = addLot($lot, $_FILES['photo']);

    if (is_numeric($resultAddLot)) {
        header('Location: lot.php?id=' . $resultAddLot);
    } else {
        $errors = $resultAddLot;
    }
}

$categoryList = getCatList();
$pageContent = includeTemplate('add', [
    'name' => $lot['name'] ?? '',
    'category' => $lot['category'] ?? '',
    'message' => $lot['message'] ?? '',
    'rate' => $lot['rate'] ?? '',
    'step' => $lot['step'] ?? '',
    'date' => $lot['date'] ?? '',
    'errors' => $errors ?? [],
    'categoryList' => $categoryList

]);

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
    'title' => 'Yeticave - Добавление лота'
]);

echo $layoutContent;
