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
        $data = array_map(function ($value) {
            return is_scalar($value) ? htmlspecialchars($value) : $value;
        }, $data);
        extract($data);

        ob_start();
        require __DIR__ . '/../../templates/' . $tpl . '.php';
        return ob_get_clean();
    }

    return '';
}

function finishTimer($endTime)
{
    $endTime = is_int($endTime) ?: strtotime($endTime);

    return $endTime - time() < 0 ? true : false;
}

function formatLotTimer($endTime, bool $viewSec = false)
{
    $endTime = is_int($endTime) ?: strtotime($endTime);
    $time = $endTime - time();

    if ($time < 0) {
        return '00:00';
    }

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

    if ($res === false) {
        return $res;
    }

    return mysqli_fetch_all($res, MYSQLI_ASSOC);
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

function getSearchLotList(string $search, int $limit = null, $connection = null)
{
    $sql = 'SELECT l.*, c.name categoryName
            FROM lot l
              JOIN category c ON c.id = l.category_id
            WHERE MATCH(l.name, l.description) AGAINST(?)
            ORDER BY l.add_time DESC';

    $parameterList = [
        'sql' => $sql,
        'data' => [$search],
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

    $result = processQuery($parametersList, $connection);

    return reset($result);
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

function getRandomPhotoName(string $name)
{
    return uniqid('', true) . '.' . pathinfo($name, PATHINFO_EXTENSION);
}

function saveImage(array $image, string $dir)
{
    $uploadDir = __DIR__ . '/../../';
    $uploadFile = $dir . '/' . getRandomPhotoName($image['name']);

    if (move_uploaded_file($image['tmp_name'], $uploadDir . $uploadFile)) {
        return $uploadFile;
    } else {
        return false;
    }
}

function addLot(array $lot, array $photo)
{
    $errors = array_merge(checkFormAddLot($lot), checkImage($photo, 'photo'));

    if (empty($errors)) {
        $config = getConfig();
        if ($fileName = saveImage($photo, $config['imgDirUpload'])) {
            $sql = 'INSERT INTO lot
                      (add_user_id, category_id, name, description, img, price, price_step, add_time, end_time)
                    VALUES 
                      (?, ?, ?, ?, ?, ?, ?, NOW(), ?)';

            $parameterList = [
                'sql' => $sql,
                'data' => [
                    getCurrentUser()['id'],
                    $lot['category'],
                    $lot['name'],
                    $lot['message'],
                    $fileName,
                    $lot['rate'],
                    $lot['step'],
                    $lot['date']
                ],
                'limit' => null
            ];

            processQuery($parameterList);

            return mysqli_insert_id(getConnection());
        }
    } else {
        return $errors;
    }
}

function formRequiredFields(array $form, array $fields)
{
    $errors = [];

    foreach ($fields as $field) {
        if (empty($form[$field])) {
            $errors[$field] = 'Поле не заполнено';
        }
    }

    return $errors;
}

function checkFromRate($var)
{
    return filter_var($var, FILTER_VALIDATE_INT);
}

function checkFromStep($var)
{
    return filter_var($var, FILTER_VALIDATE_INT);
}

function isDate($var)
{
    return is_numeric(strtotime($var));
}

function isFutureDate($var)
{
    return strtotime($var) >= strtotime("+1 day");
}

function isEmail($var)
{
    return filter_var($var, FILTER_VALIDATE_EMAIL);
}

function checkFormAddLot(array $lot)
{
    $errors = formRequiredFields($lot, ['name', 'message', 'rate', 'step']);

    if (!getLot($lot['category'])) {
        $errors['category'] = 'Выберите категорию';
    }

    if (!checkFromRate($lot['rate']) && empty($errors['rate'])) {
        $errors['rate'] = 'Введите число';
    }

    if (!checkFromStep($lot['step']) && empty($errors['step'])) {
        $errors['step'] = 'Введите число';
    }

    if (isDate($lot['date'])) {
        if (!isFutureDate($lot['date'])) {
            $errors['date'] = 'Аукцион должен длится не менее одного дня';
        }
    } else {
        $errors['date'] = 'Выберите дату';
    }

    return $errors;
}

function checkImage(array $img, string $key)
{
    $error = [];

    if (empty($img['size'])) {
        $error[$key] = 'Выберите изображение';
    } else {
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileName = $img['tmp_name'];
        $fileType = finfo_file($fileInfo, $fileName);

        $fileFormat = ['image/jpeg', 'image/png'];

        if (!in_array($fileType, $fileFormat)) {
            $error[$key] = 'Выберите фотографию формата JPEG, JPG или PNG';
        }
    }

    return $error;
}

function checkFromAddUser(array $user)
{
    $errors = formRequiredFields($user, ['email', 'password', 'name', 'message']);

    if (empty($errors['email'])) {
        if (!isEmail($user['email'])) {
            $errors['email'] = 'Не верно указан email';
        } elseif (getUserByEmail($user['email'])) {
            $errors['email'] = 'Пользователь с указанным email уже существует';
        }
    }

    return $errors;
}

function addUser(array $user, array $avatar)
{
    $errors = array_merge(checkFromAddUser($user), checkImage($avatar, 'avatar'));

    if (empty($errors)) {
        $config = getConfig();
        if ($fileName = saveImage($avatar, $config['avatarDirUpload'])) {
            $sql = 'INSERT INTO user 
                      (email, name, password_hash, avatar, contact)
                    VALUES
                      (?, ?, ?, ?, ?)';

            $parameterList = [
                'sql' => $sql,
                'data' => [
                    $user['email'],
                    $user['name'],
                    password_hash($user['password'], PASSWORD_DEFAULT),
                    $fileName,
                    $user['message']
                ],
                'limit' => null
            ];

            processQuery($parameterList);

            return true;
        }
    } else {
        return $errors;
    }
}

function getUserByEmail(string $email)
{
    if (empty($email)) {
        return false;
    }

    $sql = 'SELECT * FROM user WHERE email = ?';

    $parameterList = [
        'sql' => $sql,
        'data' => [
            $email
        ],
        'limit' => 1
    ];

    $result = processQuery($parameterList);

    return reset($result);
}

function checkFormLoginUser(array $user)
{
    $errors = formRequiredFields($user, ['email', 'password']);

    if (empty($errors['email'])) {
        if (!isEmail($user['email'])) {
            $errors['email'] = 'Нe верно указан email';
        }
    }

    return $errors;
}

function passwordUpdate(int $userId, string $password)
{
    $newHash = password_hash($password, PASSWORD_DEFAULT);

    $sql = 'UPDATE user SET password_hash = ? WHERE id = ?';

    $parameterList = [
        'sql' => $sql,
        'data' => [
            $newHash,
            $userId
        ],
        'limit' => 1
    ];

    processQuery($parameterList);

    return $newHash;
}

function passwordReHash(array $queryUser, string $password)
{
    if (password_needs_rehash($queryUser['password_hash'], PASSWORD_DEFAULT)) {
        return passwordUpdate($queryUser['id'], $password);
    }

    return $queryUser['password_hash'];
}

function loginUser(array $user)
{
    $errors = checkFormLoginUser($user);

    if (empty($errors)) {
        if ($queryUser = getUserByEmail($user['email'])) {
            if (password_verify($user['password'], $queryUser['password_hash'])) {
                $queryUser['password_hash'] = passwordReHash($queryUser, $user['password']);

                return [true, $queryUser];
            } else {
                $errors['password'] = 'Неверный пароль.';
            }
        } else {
            $errors['email'] = 'Пользователь с таким email не найден.';
        }
    }

    return [false, $errors];
}

function isAuthorized()
{
    if (!empty(getCurrentUser())) {
        return true;
    }

    return false;
}

function isBet($var)
{
    return filter_var($var, FILTER_VALIDATE_INT);
}

function formAddBet(array $bet, $minPrice)
{
    $errors = formRequiredFields($bet, ['cost']);

    if (empty($errors)) {
        if (isBet($bet['cost'])) {
            if ($bet['cost'] < $minPrice) {
                $errors['cost'] = 'Ставка не может быть ниже минимальной';
            }
        } else {
            $errors['cost'] = 'Неверно указана цена';
        }
    }

    return $errors;
}

function addBet(int $lotId, array $bet, int $minPrice)
{
    $errors = formAddBet($bet, $minPrice);

    if (empty($errors)) {
        $sql = 'INSERT INTO bet
                  (user_id, lot_id, add_time, price)
                VALUES
                  (?, ?, NOW(), ?)';

        $parameterList = [
            'sql' => $sql,
            'data' => [
                getCurrentUser()['id'],
                $lotId,
                $bet['cost']
            ],
            'limit' => null
        ];

        processQuery($parameterList);

        return true;
    }

    return $errors;
}

function viewBetFrom(array $lot, $lastBetUserId)
{
    if (isAuthorized()) {
        if (strtotime($lot['end_time']) > time() && getCurrentUser()['id'] != $lot['add_user_id']) {
            if ($lastBetUserId != getCurrentUser()['id']) {
                return true;
            }
        }
    }

    return false;
}

function getCurrentUser()
{
    return $_SESSION['user'];
}

function getCompletedLot()
{
    $sql = 'SELECT l.id, l.name, IFNULL(b.user_id, 0) user_id, u.name user_name, u.email
        FROM lot l
          LEFT JOIN bet b ON b.id = (SELECT max(id) FROM bet WHERE lot_id = l.id)
          LEFT JOIN user u ON u.id = b.user_id
        WHERE l.win_user_id IS NULL AND l.end_time <= NOW()';

    $parameterList = [
        'sql' => $sql,
        'data' => [],
        'limit' => null
    ];

    return processQuery($parameterList);
}

function updateWinUserLot($lot)
{
    $sql = 'UPDATE lot SET win_user_id = ? WHERE id = ?';

    $parameterList = [
        'sql' => $sql,
        'data' => [
            $lot['user_id'],
            $lot['id']
        ],
        'limit' => 1
    ];

    processQuery($parameterList);

    if ($lot['user_id']) {
        toInformByWin($lot);
    }

    return $lot['user_id'] ? true : false;
}

function toInformByWin($lot)
{
    $mailContent = includeTemplate('email', [
        'userName' => $lot['user_name'],
        'lotId' => $lot['id'],
        'lotName' => $lot['name']
    ]);

    return sendEmail('Ваша ставка победила', [$lot['email'] => $lot['user_name']], $mailContent);
}

function getSmtp()
{
    $config = getConfig();

    $transport = (new Swift_SmtpTransport($config['smtp_server'], $config['smtp_port']))
        ->setUsername($config['smtp_name'])
        ->setPassword($config['smtp_pass']);

    return $transport;
}

function sendEmail($title, $to, $content, $from = ['keks@phpdemo.ru' => 'Keks'])
{
    $mailer = new Swift_Mailer(getSmtp());

    $message = (new Swift_Message($title))
        ->setFrom($from)
        ->setTo($to)
        ->setBody($content, 'text/html');

    return $mailer->send($message);
}

