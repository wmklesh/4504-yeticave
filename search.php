<?php

require __DIR__ . '/core/bootstrap.php';

$search = trim($_GET['search']) ?? null;
if (empty($search)) {
    header('Location: index.php');
    exit;
}

$lotList = getSearchLotList($search,9);
$lotListContent = '';
foreach ($lotList as $lot) {
    $lotListContent .= includeTemplate('lot-item', $lot);
}

$pageContent = includeTemplate('search', [
    'search' => $search,
    'lotListContent' => $lotListContent
]);

$categoryList = getCatList();
$catListContent = '';
foreach ($categoryList as $category) {
    $catListContent .= includeTemplate('nav-item', ['name' => $category['name']]);
}

$layoutContent = includeTemplate('layout', [
    'content' => $pageContent,
    'catListContent' => $catListContent,
    'isAuth' => empty($_SESSION['user']) ? false : true,
    'userName' => $_SESSION['user']['name'] ?? null,
    'userAvatar' => $_SESSION['user']['avatar'] ?? null,
    'title' => 'Yeticave - Главная страница'
]);

echo $layoutContent;
