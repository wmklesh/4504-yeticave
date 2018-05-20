<?php

require __DIR__ . '/core/bootstrap.php';

if ($_POST) {
    $lot = $_POST['lot'];
    $photo = $_FILES['photo'];
    $checkResult = checkFormLotAdd($lot, $photo);

    if ($checkResult === true) {
        $uploadDir = __DIR__;
        $uploadFile = '/img/' . uniqid() . '.' . basename($_FILES['photo']['name']);

        $sql = 'INSERT INTO lot
                  (add_user_id, category_id, name, description, img, price, price_step, add_time, end_time)
                VALUES 
                  (1, 1, ?, ?, ?, ?, ?, NOW(), 1)';

        $parameterList = [
            'sql' => $sql,
            'data' => [$lot['name'], $lot['message'], $uploadFile, $lot['rate'], $lot['step']],
            'limit' => null
        ];

        if (move_uploaded_file($photo['tmp_name'], $uploadDir . $uploadFile)) {
            processQuery($parameterList);
            header('Location: lot.php?id=' . mysqli_insert_id(getConnection()));
        }
    } else {
        $errors = $checkResult;
    }
}

$categoryList = getCatList();
$pageContent = includeTemplate('add', [
    'name' => $lot['name'] ?? '',
    'message' => $lot['message'] ?? '',
    'rate' => $lot['rate'] ?? '',
    'step' => $lot['step'] ?? '',
    'date' => $lot['date'] ?? '',
    'errors' => $errors ?? [],
    'categoryList' => $categoryList

]);

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
    'title' => 'Yeticave - Добавление лота'
]);

echo $layoutContent;
