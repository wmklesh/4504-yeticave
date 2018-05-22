<?php

require __DIR__ . '/core/bootstrap.php';

if ($_POST) {
    $user = $_POST['user'];
    var_dump($_FILES['avatar']);
    $resultAddUser = addUser($user, $_FILES['avatar']);

    if ($resultAddUser === true) {
        header('Location: login.php');
    } else {
        $errors = $resultAddUser;
    }
}

$pageContent = includeTemplate('registration', [
    'email' => $user['email'] ?? '',
    'password' => $user['password'] ?? '',
    'name' => $user['name'] ?? '',
    'message' => $user['message'] ?? '',
    'errors' => $errors ?? []
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
    'title' => 'Yeticave - Регистрация'
]);

echo $layoutContent;
