<?php

require 'functions.php';

$is_auth = (bool) rand(0, 1);

$user_name = 'Константин';
$user_avatar = 'img/user.jpg';

$categoryList = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];

$lotList = [
    [
        'name' => '2014 Rossignol District Snowboard',
        'categories' => 'Доски и лыжи',
        'price' => 10999,
        'imgUrl' => 'img/lot-1.jpg'
    ],
    [
        'name' => 'DC Ply Mens 2016/2017 Snowboard',
        'categories' => 'Доски и лыжи',
        'price' => 159999,
        'imgUrl' => 'img/lot-2.jpg'
    ],
    [
        'name' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'categories' => 'Крепления',
        'price' => 8000,
        'imgUrl' => 'img/lot-3.jpg'
    ],
    [
        'name' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'categories' => 'Ботинки',
        'price' => 10999,
        'imgUrl' => 'img/lot-4.jpg'
    ],
    [
        'name' => 'Куртка для сноуборда DC Mutiny Charocal',
        'categories' => 'Одежда',
        'price' => 7500,
        'imgUrl' => 'img/lot-5.jpg'
    ],
    [
        'name' => 'Маска Oakley Canopy',
        'categories' => 'Разное',
        'price' => 5400,
        'imgUrl' => 'img/lot-6.jpg'
    ],
];

$pageContent = includeTemplate('index.php', ['lotList' => $lotList]);
$layoutContent = includeTemplate('layout.php', [
    'content' => $pageContent,
    'categoryList' => $categoryList,
    'title' => 'Yeticave - Главная страница'
]);

echo $layoutContent;
