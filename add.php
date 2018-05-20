<?php

require __DIR__ . '/core/bootstrap.php';

$required_fields = ['name', 'category', 'message', 'rate', 'step', 'date'];
$errors = [];

if ($_POST) {
    $lot = $_POST['lot'];

    foreach ($required_fields as $field) {
        if (empty($lot[$field])) {
            $errors[$field] = 'Поле не заполнено';
        }

        if ($field == 'rate' || $field == 'step') {
            if (!filter_var($lot[$field], FILTER_VALIDATE_INT)) {
                $errors[$field] = 'Введите число';
            }
        }
    }

    if ($_FILES['photo']['name'] != '') {
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileName = $_FILES['photo']['tmp_name'];
        $fileType = finfo_file($fileInfo, $fileName);

        $fileFormat = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($fileType, $fileFormat)) {
            $errors['photo'] = 'Загрузите картинку формата JPEG, JPG или PNG';
        }
    } else {
        $errors['photo'] = 'Загрузите изображение';
    }

    if (!count($errors)) {
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

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $uploadFile)) {
            processQuery($parameterList);
            header('Location: lot.php?id=' . mysqli_insert_id(getConnection()));
        }
    }
}

$pageContent = includeTemplate('add', [
    'name' => $lot['name'] ?? '',
    'message' => $lot['message'] ?? '',
    'rate' => $lot['rate'] ?? '',
    'step' => $lot['step'] ?? '',
    'date' => $lot['date'] ?? '',
    'errorCount' => count($errors),
    'errorName' => $errors['name'] ?? '',
    'errorCategory' => $errors['category'] ?? '',
    'errorMessage' => $errors['message'] ?? '',
    'errorRate' => $errors['rate'] ?? '',
    'errorStep' => $errors['step'] ?? '',
    'errorDate' => $errors['date'] ?? '',

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
    'title' => 'Yeticave - Добавление лота'
]);

echo $layoutContent;
