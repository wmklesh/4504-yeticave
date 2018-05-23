<?php

require __DIR__ . '/core/bootstrap.php';

if ($_POST) {
    $user = $_POST['user'];
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
    'isAuth' => empty($_SESSION['user']) ? false : true,
    'userName' => $_SESSION['user']['name'] ?? null,
    'userAvatar' => $_SESSION['user']['avatar'] ?? null,
    'title' => 'Yeticave - Регистрация'
]);

echo $layoutContent;
