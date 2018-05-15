<?php

function getConnection() {
    static $connection;

    if ($connection === null) {
        $connection = mysqli_connect('localhost', 'root', 'root', 'yeticave');
        mysqli_set_charset($connection, 'utf8');

        if (! $connection) {
            print('Ошибка: Невозможно подключиться к MySQL  ' . mysqli_connect_error());
            die();
        }
    }

    return $connection;
}

function formatPrice($num) {
    return sprintf('%s ₽', number_format(ceil($num), 0, '', ' '));
}

function includeTemplate($tpl, $data) {
    if (is_readable(__DIR__ . '/templates/' . $tpl . '.php')) {

        extract($data, EXTR_PREFIX_ALL, '');
        array_walk($data, function (&$value) {
            $value = htmlspecialchars($value);
        });
        extract($data);

        ob_start();
        require __DIR__ . '/templates/' . $tpl . '.php';
        return ob_get_clean();
    }

    return '';
}

function formatLotTimer($endTime) {
    $endTime = is_int($endTime)?: strtotime($endTime);
    $time = $endTime - time();
    return sprintf('%02d:%02d', ($time / 3600) % 24, ($time / 60) % 60);
}

function processQuery(array $param, $connection = null) {
    if ($connection === null) {
        $connection = getConnection();
    }

    addLimit($param);

    $stmt = db_get_prepare_stmt($connection, $param['sql'], $param['date']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}

function addLimit(array &$param) {
    if ( (int) $param['limit']) {
        $param['sql'] .= ' LIMIT ?';
        $param['date'][] = (int) $param['limit'];
    }

    return;
}

function getLotList(int $limit = 0, $connection = null) {
    $sql = 'SELECT l.*, c.name category_name
            FROM lot l
              JOIN category c ON c.id = l.category_id
            WHERE l.end_time > NOW()
            ORDER BY l.add_time DESC';

    $param = [
        'sql' => $sql,
        'limit' => $limit
    ];

    return processQuery($param, $connection);
}

function getCatList(int $limit = 0, $connection = null) {
    $sql = 'SELECT * FROM category';

    $param = [
        'sql' => $sql,
        'limit' => $limit
    ];

    return processQuery($param, $connection);
}
