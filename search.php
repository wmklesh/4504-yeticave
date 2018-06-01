<?php

require __DIR__ . '/core/bootstrap.php';

$search = trim(getQuery()['search']) ?? null;
if (empty($search)) {
    header('Location: index.php');
    exit;
}

$lotList = getSearchLotList($search,9);
$lotListContent = '';
foreach ($lotList as $lot) {
    $lotListContent .= includeTemplate('lot-item', $lot);
}

$categoryList = getCatList();
$catListContent = '';
foreach ($categoryList as $category) {
    $catListContent .= includeTemplate('nav-item', [
        'id' => $category['id'],
        'name' => $category['name']
    ]);
}

$pageContent = includeTemplate('search', [
    'catListContent' => $catListContent,
    'search' => $search,
    'lotListContent' => $lotListContent
]);

$layoutContent = includeTemplate('layout', [
    'content' => $pageContent,
    'catListContent' => $catListContent,
    'isAuth' => empty(getCurrentUser()) ? false : true,
    'userName' => getCurrentUser()['name'] ?? null,
    'userAvatar' => getCurrentUser()['avatar'] ?? null,
    'title' => 'Yeticave - Результаты поиска «' . $search . '»'
]);

echo $layoutContent;
