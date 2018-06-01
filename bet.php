<?php

require __DIR__ . '/core/bootstrap.php';

if (isAuthorized() === false) {
    stopScript();
}

$categoryList = getCatList();
$catListContent = '';
foreach ($categoryList as $category) {
    $catListContent .= includeTemplate('nav-item', [
        'id' => $category['id'],
        'name' => $category['name']
    ]);
}

$betList = getUserBetList();
$betListContent = '';
foreach ($betList as $bet) {
    $betListContent .= includeTemplate('bet-item', [
        'lotId' => $bet['lot_id'],
        'img' => $bet['img'],
        'name' => $bet['name'],
        'category' => $bet['category'],
        'endLotTime' => $bet['end_time'],
        'price' => $bet['price'],
        'addBetTime' => $bet['add_time'],
        'userId' => getCurrentUser()['id'],
        'winUserId' => $bet['win_user_id'],
        'contact' => $bet['contact']
    ]);
}

$pageContent = includeTemplate('bet', [
    'catListContent' => $catListContent,
    'betListContent' => $betListContent ?? null
]);

$layoutContent = includeTemplate('layout', [
    'content' => $pageContent,
    'catListContent' => $catListContent,
    'isAuth' => empty(getCurrentUser()) ? false : true,
    'userName' => getCurrentUser()['name'],
    'userAvatar' => getCurrentUser()['avatar'],
    'title' => 'Yeticave - Мои ставки'
]);

echo $layoutContent;
