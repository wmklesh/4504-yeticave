<?php

function getConnection()
{
    static $connection;

    if ($connection === null) {
        $config = getConfig();

        $connection = mysqli_connect($config['dbHost'], $config['dbUser'], $config['dbPass'], $config['dbName']);
        mysqli_set_charset($connection, 'utf8');

        if (!$connection) {
            print('Ошибка: Невозможно подключиться к MySQL  ' . mysqli_connect_error());
            die();
        }
    }

    return $connection;
}

function getConfig()
{
    static $config = null;

    if ($config === null) {
        $config = require __DIR__ . '/../config.php';
    }

    return $config;
}

function formatPrice($num)
{
    return sprintf('%s ₽', number_format(ceil($num), 0, '', ' '));
}

function includeTemplate(string $tpl, array $data)
{
    if (is_readable(__DIR__ . '/../../templates/' . $tpl . '.php')) {

        extract($data, EXTR_PREFIX_ALL, '');
        array_walk($data, function (&$value) {
            if (is_scalar($value)) {
                $value = htmlspecialchars($value);
            }
        });
        extract($data);

        ob_start();
        require __DIR__ . '/../../templates/' . $tpl . '.php';
        return ob_get_clean();
    }

    return '';
}

function formatLotTimer($endTime, bool $viewSec = false)
{
    $endTime = is_int($endTime) ?: strtotime($endTime);
    $time = $endTime - time();
    $format = '%02d:%02d';
    !$viewSec ?: $format .= ':%02d';

    return sprintf($format, ($time / 3600) % 24, ($time / 60) % 60, $time % 60);
}

function formatBetTime($time)
{
    $time = is_int($time) ?: strtotime($time);

    if ($time < 60) {
        $result = ($time % 60) . ' секунд назад';
    } elseif ($time < 3600) {
        $result = ($time / 60) % 60 . ' минут назад';
    } else {
        $result = date('d.m.y в H:i', $time);
    }

    return $result;
}

function processQuery(array $parameterList, $connection = null)
{
    if ($connection === null) {
        $connection = getConnection();
    }

    addLimit($parameterList);

    $stmt = db_get_prepare_stmt($connection, $parameterList['sql'], $parameterList['data']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($res) > 1) {
        $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
        $result = mysqli_fetch_array($res, MYSQLI_ASSOC);
    }

    return $result;
}

function addLimit(array &$parameterList)
{
    if ((int)$parameterList['limit']) {
        $parameterList['sql'] .= ' LIMIT ?';
        $parameterList['data'][] = (int)$parameterList['limit'];
    }

    return;
}

function getLotList(int $limit = null, $connection = null)
{
    $sql = 'SELECT l.*, c.name categoryName
            FROM lot l
              JOIN category c ON c.id = l.category_id
            WHERE l.end_time > NOW()
            ORDER BY l.add_time DESC';

    $parameterList = [
        'sql' => $sql,
        'data' => [],
        'limit' => $limit
    ];

    return processQuery($parameterList, $connection);
}

function getCatList(int $limit = null, $connection = null)
{
    $sql = 'SELECT * FROM category';

    $parameterList = [
        'sql' => $sql,
        'data' => [],
        'limit' => $limit
    ];

    return processQuery($parameterList, $connection);
}

function getLot(int $lotId, $connection = null)
{
    $sql = 'SELECT l.*, c.name category_name 
            FROM lot l
              JOIN category c ON c.id = l.category_id
            WHERE l.id = ?';

    $parametersList = [
        'sql' => $sql,
        'data' => [$lotId],
        'limit' => 1
    ];

    return processQuery($parametersList, $connection);
}

function getBetList(int $lotId, int $limit = null, $connection = null)
{
    $sql = 'SELECT b.*, u.name
            FROM bet b
              JOIN user u ON u.id = b.user_id
            WHERE b.lot_id = ? 
            ORDER BY b.add_time DESC';

    $parameterList = [
        'sql' => $sql,
        'data' => [$lotId],
        'limit' => $limit
    ];

    return processQuery($parameterList, $connection);
}

function checkFormLotAdd(array $lot, array $photo)
{
    $required_fields = ['name', 'message', 'rate', 'step'];
    $errors = [];

    foreach ($required_fields as $field) {
        if (empty($lot[$field])) {
            $errors[$field] = 'Поле не заполнено';
        }
    }

    $sql = 'SELECT * FROM category WHERE id = ?';

    $parameterList = [
        'sql' => $sql,
        'data' => [$lot['category']],
        'limit' => 1
    ];

    if (!processQuery($parameterList)) {
        $errors['category'] = 'Выберите категорию';
    }

    if (!filter_var($lot['rate'], FILTER_VALIDATE_INT)) {
        $errors['rate'] = 'Введите число';
    }

    if (!filter_var($lot['step'], FILTER_VALIDATE_INT)) {
        $errors['rate'] = 'Введите число';
    }

    $lotDate = strtotime($lot['date']);
    if (is_numeric($lotDate)) {
        if ($lotDate < time()) {
            $errors['date'] = 'Дата не может быть в прошлом';
        }
    } else {
        $errors['date'] = 'Выберите дату';
    }

    if ($photo['name'] == '') {
        $errors['photo'] = 'Выберите изображение';
    } else {
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileName = $photo['tmp_name'];
        $fileType = finfo_file($fileInfo, $fileName);

        $fileFormat = ['image/jpeg', 'image/jpg', 'image/png'];

        if (!in_array($fileType, $fileFormat)) {
            $errors['photo'] = 'Выберите фотографию формата JPEG, JPG или PNG';
        }
    }

    return count($errors) ? $errors : true;
}
