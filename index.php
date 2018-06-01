<?php

require __DIR__ . '/core/bootstrap.php';
require __DIR__ . '/getwinner.php';

$selectCategory = false;
if (!empty($_GET['category'])) {
    $selectCategory = getCat(getQuery()['category']);

    if ($selectCategory == false) {
        stopScript();
    }
}

$pageItem = 9;
$curPage = getQuery()['page'] ?? 1;
list($pagesCount, $offset) = pagination(
    $curPage,
    $pageItem,
    getCountLot(true, $selectCategory['id'] ?? null)
);

$lotList = getLotList($pageItem, $offset, $selectCategory['id'] ?? null);
$lotListContent = '';
foreach ($lotList as $lot) {
    $lotListContent .= includeTemplate('lot-item', $lot);
}

$paginationContent = '';
if ($pagesCount > 1) {
    $paginationContent = includeTemplate('pagination', [
        'pages' => range(1, $pagesCount),
        'pageCount' => $pagesCount,
        'curPage' => $curPage,
        'link' => $selectCategory['id'] ? '?category=' . $selectCategory['id'] . '&' : '?'
    ]);
}

$pageContent = includeTemplate('index', [
    'lotListContent' => $lotListContent,
    'paginationContent' => $paginationContent,
]);

$categoryList = getCatList();
$catListContent = '';
foreach ($categoryList as $category) {
    $catListContent .= includeTemplate('nav-item', [
        'id' => $category['id'],
        'name' => $category['name']
    ]);
}

$layoutContent = includeTemplate('layout', [
    'content' => $pageContent,
    'catListContent' => $catListContent,
    'isAuth' => empty(getCurrentUser()) ? false : true,
    'userName' => getCurrentUser()['name'] ?? null,
    'userAvatar' => getCurrentUser()['avatar'] ?? null,
    'title' => 'Yeticave - ' . ($selectCategory ? 'Все лоты в категории ' . $selectCategory['name'] : 'Главная страница')
]);

echo $layoutContent;
