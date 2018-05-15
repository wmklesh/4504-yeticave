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

function processQuery($connection, string $sql, array $param = []) {
    if (empty($param)) {
        $query = mysqli_query($connection, $sql);
    }

    return mysqli_fetch_all($query, MYSQLI_ASSOC);
}

function addLimit(int $limit) {
    return ' LIMIT ' . $limit;
}

function getLotList($connection, int $limit = 0) {
    $sql = 'SELECT l.name, l.price, img, end_time, c.name category_name, IFNULL(b.count_bet, 0) count_bet
            FROM lot l
              JOIN category c ON c.id = l.category_id
              LEFT JOIN
                (SELECT b.lot_id, COUNT(b.id) count_bet FROM bet b GROUP BY b.lot_id) b
              ON b.lot_id = l.id
            WHERE l.end_time > NOW()
            ORDER BY l.add_time DESC';

    if ($limit) {
        $sql .= addLimit($limit);
    }

    return processQuery($connection, $sql);
}

function getCatList($connection, int $limit = 0) {
    $sql = 'SELECT * FROM category';

    if ($limit) {
        $sql .= addLimit($limit);
    }

    return processQuery($connection, $sql);
}
