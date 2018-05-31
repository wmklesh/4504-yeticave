<?php

require __DIR__ . '/core/bootstrap.php';

if (isAuthorized() === false) {
    http_response_code(404);
    exit;
}

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
$catListContent = '';
foreach ($categoryList as $category) {
    $catListContent .= includeTemplate('nav-item', [
        'id' => $category['id'],
        'name' => $category['name']
    ]);
}

$pageContent = includeTemplate('add', [
    'catListContent' => $catListContent,
    'name' => $lot['name'] ?? '',
    'category' => $lot['category'] ?? '',
    'message' => $lot['message'] ?? '',
    'rate' => $lot['rate'] ?? '',
    'step' => $lot['step'] ?? '',
    'date' => $lot['date'] ?? '',
    'errors' => $errors ?? [],
    'categoryList' => $categoryList
]);

$layoutContent = includeTemplate('layout', [
    'content' => $pageContent,
    'catListContent' => $catListContent,
    'isAuth' => empty($_SESSION['user']) ? false : true,
    'userName' => $_SESSION['user']['name'],
    'userAvatar' => $_SESSION['user']['avatar'],
    'title' => 'Yeticave - Добавление лота'
]);

echo $layoutContent;
