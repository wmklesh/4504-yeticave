<?php

require __DIR__ . '/core/bootstrap.php';

$lotId = filter_var($_GET['id'], FILTER_VALIDATE_INT);

if ($lotId) {
    $lot = getLot($lotId);
}

if ($lotId == false || $lot == false) {
    http_response_code(404);
    exit;
}

$betList = getBetList($lot['id']) ?? [];

if (isAuthorized() === true && $_POST) {
    $minPrice = ($betList[0]['price'] ?? $lot['price']) + $lot['price_step'];
    $resultAddBet = addBet($lot['id'], $_POST['bet'], $minPrice);

    var_dump($resultAddBet);

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

$pageContent = includeTemplate('lot', [
    'isAuth' => empty($_SESSION['user']) ? false : true,
    'lotId' => $lot['id'],
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
    'isAuth' => empty($_SESSION['user']) ? false : true,
    'userName' => $_SESSION['user']['name'] ?? null,
    'userAvatar' => $_SESSION['user']['avatar'] ?? null,
    'title' => 'Yeticave - ' . $lot['name']
]);

echo $layoutContent;
