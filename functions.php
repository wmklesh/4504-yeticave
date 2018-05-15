<?php

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

function processQuery($connection, array $param) {
    addLimit($param);

    $stmt = db_get_prepare_stmt($connection, $param['sql'], $param['date']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}

function addLimit(array &$param) {
    if ( (int) $param['limit']) {
        $param['sql'] .= ' LIMIT ?';
        $param['date'][] = $param['limit'];
    }

    return;
}

function getLotList($connection, int $limit = 0) {
    $sql = 'SELECT l.*, c.name category_name
            FROM lot l
              JOIN category c ON c.id = l.category_id
            WHERE l.end_time > NOW()
            ORDER BY l.add_time DESC';

    $param = [
        'sql' => $sql,
        'limit' => $limit
    ];

    return processQuery($connection, $param);
}

function getCatList($connection, int $limit = 0) {
    $sql = 'SELECT * FROM category';

    $param = [
        'sql' => $sql,
        'limit' => $limit
    ];

    return processQuery($connection, $param);
}
