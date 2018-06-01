<?php

require __DIR__ . '/core/bootstrap.php';

$lotId = filter_var(getQuery()['id'] ?? 0, FILTER_VALIDATE_INT);

if ($lotId) {
    $lot = getLot($lotId);
}

if ($lotId == false || $lot == false) {
    stopScript();
}

$betList = getBetList($lot['id']) ?? [];

if (isAuthorized() && postQuery()) {
    $minPrice = ($betList[0]['price'] ?? $lot['price']) + $lot['price_step'];
    $resultAddBet = addBet($lot['id'], postQuery()['bet'], $minPrice);

    if ($resultAddBet === true) {
        header('Location: lot.php?id=' . $lot['id']);
        exit;
    } else {
        $errors = $resultAddBet;
    }
}

$betListContent = '';
foreach ($betList as $bet) {
    $betListContent .= includeTemplate('bet-table', [
        'name' => $bet['name'],
        'price' => $bet['price'],
        'addTime' => $bet['add_time']
    ]);
}

$categoryList = getCatList();
$catListContent = '';
foreach ($categoryList as $category) {
    $catListContent .= includeTemplate('nav-item', [
        'id' => $category['id'],
        'name' => $category['name']
    ]);
}

$pageContent = includeTemplate('lot', [
    'catListContent' => $catListContent,
    'isAuth' => empty(getCurrentUser()) ? false : true,
    'lotId' => $lot['id'],
    'name' => $lot['name'],
    'description' => $lot['description'],
    'img' => $lot['img'],
    'price' => $betList[0]['price'] ?? $lot['price'],
    'priceStep' => $lot['price_step'],
    'categoryName' => $lot['category_name'],
    'endTime' => $lot['end_time'],
    'betCount' => count($betList),
    'viewFormBet' => viewBetFrom($lot, $betList[0]['user_id'] ?? null),
    'errors' => $errors ?? [],
    'betListContent' => $betListContent
]);

$layoutContent = includeTemplate('layout', [
    'content' => $pageContent,
    'catListContent' => $catListContent,
    'isAuth' => empty(getCurrentUser()) ? false : true,
    'userName' => getCurrentUser()['name'] ?? null,
    'userAvatar' => getCurrentUser()['avatar'] ?? null,
    'title' => 'Yeticave - ' . $lot['name']
]);

echo $layoutContent;
