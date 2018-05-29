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
    'isAuth' => empty(getCurrentUser()) ? false : true,
    'userName' => getCurrentUser()['name'] ?? null,
    'userAvatar' => getCurrentUser()['avatar'] ?? null,
    'title' => 'Yeticave - Результаты поиска «' . $search . '»'
]);

echo $layoutContent;
