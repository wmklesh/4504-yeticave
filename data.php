<?php
// ставки пользователей, которыми надо заполнить таблицу
$bets = [
    ['name' => 'Иван', 'price' => 11500, 'ts' => strtotime('-' . rand(1, 50) .' minute')],
    ['name' => 'Константин', 'price' => 11000, 'ts' => strtotime('-' . rand(1, 18) .' hour')],
    ['name' => 'Евгений', 'price' => 10500, 'ts' => strtotime('-' . rand(25, 50) .' hour')],
    ['name' => 'Семён', 'price' => 10000, 'ts' => strtotime('last week')]
];

$is_auth = (bool) rand(0, 1);

$user_name = 'Константин';
$user_avatar = 'img/user.jpg';

$categoryList = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];

$timeEndLot = strtotime('tomorrow');
$lotList = [
    [
        'name' => '2014 Rossignol District Snowboard',
        'categories' => 'Доски и лыжи',
        'price' => 10999,
        'imgUrl' => 'img/lot-1.jpg',
        'timeEndLot' => $timeEndLot
    ],
    [
        'name' => 'DC Ply Mens 2016/2017 Snowboard',
        'categories' => 'Доски и лыжи',
        'price' => 159999,
        'imgUrl' => 'img/lot-2.jpg',
        'timeEndLot' => $timeEndLot
    ],
    [
        'name' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'categories' => 'Крепления',
        'price' => 8000,
        'imgUrl' => 'img/lot-3.jpg',
        'timeEndLot' => $timeEndLot
    ],
    [
        'name' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'categories' => 'Ботинки',
        'price' => 10999,
        'imgUrl' => 'img/lot-4.jpg',
        'timeEndLot' => $timeEndLot
    ],
    [
        'name' => 'Куртка для сноуборда DC Mutiny Charocal',
        'categories' => 'Одежда',
        'price' => 7500,
        'imgUrl' => 'img/lot-5.jpg',
        'timeEndLot' => $timeEndLot
    ],
    [
        'name' => 'Маска Oakley Canopy',
        'categories' => 'Разное',
        'price' => 5400,
        'imgUrl' => 'img/lot-6.jpg',
        'timeEndLot' => $timeEndLot
    ],
];
