<?php

require __DIR__ . '/core/bootstrap.php';

if ($_POST) {
    $user = $_POST['user'];
    list($isLogin, $resultAutUser) = loginUser($user);

    if ($isLogin === true) {
        $_SESSION['user'] = $resultAutUser;
        header('Location: index.php');
        exit;
    } else {
        $errors = $resultAutUser;
    }
}

$pageContent = includeTemplate('login', [
    'email' => $user['email'] ?? '',
    'password' => $user['password'] ?? '',
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
    'isAuth' => empty(getCurrentUser()) ? false : true,
    'userName' => getCurrentUser()['name'] ?? null,
    'userAvatar' => getCurrentUser()['avatar'] ?? null,
    'title' => 'Yeticave - Авторизация'
]);

echo $layoutContent;
