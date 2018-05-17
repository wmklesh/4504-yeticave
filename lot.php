<?php

require __DIR__ . '/core/bootstrap.php';
require __DIR__ . '/data.php';

$lotId = filter_var($_GET['id'], FILTER_VALIDATE_INT);

if ($lotId) {
    $lot = getLot($lotId);
}

if ($lotId == false || $lot == false) {
    http_response_code(404);
    exit;
}

$betList = getBetList($lot['id']);
$betListContent = '';
foreach ($betList as $bet) {
    $betListContent .= includeTemplate('bet-table', [
        'name' => $bet['name'],
        'price' => $bet['price'],
        'addTime' => $bet['add_time']
    ]);
}

$pageContent = includeTemplate('lot', [
    'name' => $lot['name'],
    'description' => $lot['description'],
    'img' => $lot['img'],
    'price' => $betList[0]['price'] ?? $lot['price'],
    'priceStep' => $lot['price_step'],
    'categoryName' => $lot['category_name'],
    'endTime' => $lot['end_time'],
    'betCount' => count($betList),
    'betListContent' => $betListContent
]);

$categoryList = getCatList();
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
    'title' => ''
]);

echo $layoutContent;
