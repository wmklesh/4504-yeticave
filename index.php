<?php

require __DIR__ . '/core/bootstrap.php';
require __DIR__ . '/getwinner.php';

$SelectCategory = false;
if (!empty($_GET['category'])) {
    $SelectCategory = getCat($_GET['category']);

    if ($SelectCategory == false) {
        http_response_code(404);
        exit;
    }
}

$pageItem = 9;
$curPage = $_GET['page'] ?? 1;
list($pagesCount, $offset) = pagination($curPage, $pageItem, getCountLot(
        true, $SelectCategory['id'] ?? null)
);

$lotList = getLotList($pageItem, $offset, $SelectCategory['id'] ?? null);
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
        'link' => $SelectCategory['id'] ? '?category=' . $SelectCategory['id'] . '&' : '?'
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
    'title' => 'Yeticave - ' . ($SelectCategory ? 'Все лоты в категории ' . $SelectCategory['name'] : 'Главная страница')
]);

echo $layoutContent;
