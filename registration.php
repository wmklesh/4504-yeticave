<?php

require __DIR__ . '/core/bootstrap.php';

if ($_POST) {
    $user = postQuery()['user'];
    $resultAddUser = addUser($user, $_FILES['avatar']);

    if ($resultAddUser === true) {
        header('Location: login.php');
    } else {
        $errors = $resultAddUser;
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

$pageContent = includeTemplate('registration', [
    'catListContent' => $catListContent,
    'email' => $user['email'] ?? '',
    'password' => $user['password'] ?? '',
    'name' => $user['name'] ?? '',
    'message' => $user['message'] ?? '',
    'errors' => $errors ?? []
]);

$layoutContent = includeTemplate('layout', [
    'content' => $pageContent,
    'catListContent' => $catListContent,
    'isAuth' => empty(getCurrentUser()) ? false : true,
    'userName' => getCurrentUser()['name'] ?? null,
    'userAvatar' => getCurrentUser()['avatar'] ?? null,
    'title' => 'Yeticave - Регистрация'
]);

echo $layoutContent;
