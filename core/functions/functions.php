<?php

/**
 * Хранит подключение к БД, если подключения нет - создает его.
 *
 * @return mysqli Подключение к БД
 */
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

/**
 * Получает настройки сайта из файла config.php
 *
 * @return array Массив настроек
 */
function getConfig()
{
    static $config = null;

    if ($config === null) {
        $config = require __DIR__ . '/../config.php';
    }

    return $config;
}

/**
 * Добавляет разделители в цену и знак ₽ в конец
 * @param integer $num Цена для форматирования
 *
 * @return string Отформатированная фена
 */
function formatPrice(int $num)
{
    return sprintf('%s ₽', number_format(ceil($num), 0, '', ' '));
}

/**
 * Подключает шаблон, фильтрует данные, передает данные в шаблон
 * @param string $tpl Имя шаблона
 * @param array $data Передоваемые данные
 *
 * @return string Готовый шаблон для отображения
 */
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

/**
 * Проверяет прошла ли указаная дата
 * @param $endTime Дата для првоерки
 *
 * @return bool Результат проверки
 */
function finishTimer($endTime)
{
    $endTime = is_int($endTime) ?: strtotime($endTime);

    return $endTime - time() < 0 ? true : false;
}

/**
 * Считает время до закрытия лота и форматирует дату, оставляя только время
 * @param $endTime Время окончания
 * @param bool $viewSec Отображать секунды или нет
 *
 * @return string Отформатированное время
 */
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

/**
 * Форматирует дату добавления ставки
 * @param $time Время
 *
 * @return string Отформатированная дата
 */
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

/**
 * Подготавливает и выполняет SQL запрос
 * @param array $parameterList Параметры запроса
 * @param null $connection Подключение к БД
 *
 * @return array|bool|mysqli_result|null Результат выполнения запроса
 */
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

/**
 * Добавляет LIMIT и OFFSET к SQL запросу если нужно
 *
 * @param array $parameterList Параметры запроса
 */
function addLimit(array &$parameterList)
{

    if ((int)$parameterList['limit']) {
        $parameterList['sql'] .= ' LIMIT ?';
        $parameterList['data'][] = (int)$parameterList['limit'];
    }

    if (!empty($parameterList['offset'])) {
        if ((int)$parameterList['offset']) {
            $parameterList['sql'] .= ' OFFSET ?';
            $parameterList['data'][] = (int)$parameterList['offset'];
        }
    }

    return;
}

/**
 * Получает список лотов, можно указать лимит или категорию
 *
 * @param int|null $limit Лимит лотов
 * @param int|null $offset Сдвиг для пагинации
 * @param int|null $category Категория
 * @param null $connection Подключение к БД
 *
 * @return array|bool|mysqli_result|null Список лотов
 */
function getLotList(int $limit = null, int $offset = null, int $category = null, $connection = null)
{
    $sql = 'SELECT l.*, c.name categoryName
            FROM lot l
              JOIN category c ON c.id = l.category_id
            WHERE l.end_time > NOW()';
    if ($category) {
        $sql .= ' AND category_id = ? ';
    }
    $sql .= 'ORDER BY l.add_time DESC';

    $parameterList = [
        'sql' => $sql,
        'data' => [
            $category
        ],
        'limit' => $limit,
        'offset' => $offset
    ];

    return processQuery($parameterList, $connection);
}

/**
 * Получает список лотов используя поиск по названию и описанию
 * @param string $search Строка для поиска
 * @param int|null $limit Лимит лотов
 * @param int|null $offset Сдвиг для пагинации
 * @param null $connection Подключение к БД
 *
 * @return array|bool|mysqli_result|null Список лотов
 */
function getSearchLotList(string $search, int $limit = null, int $offset = null, $connection = null)
{
    $sql = 'SELECT l.*, c.name categoryName
            FROM lot l
              JOIN category c ON c.id = l.category_id
            WHERE MATCH(l.name, l.description) AGAINST(?)
            ORDER BY l.add_time DESC';

    $parameterList = [
        'sql' => $sql,
        'data' => [$search],
        'limit' => $limit,
        'offset' => $offset
    ];

    return processQuery($parameterList, $connection);
}

/**
 * Получает список категорий
 * @param int|null $limit Лимит категорий
 * @param null $connection Подключение к БД
 *
 * @return array|bool|mysqli_result|null Список категорий
 */
function getCatList(int $limit = null, $connection = null)
{
    $sql = 'SELECT * FROM category ORDER BY id';

    $parameterList = [
        'sql' => $sql,
        'data' => [],
        'limit' => $limit
    ];

    return processQuery($parameterList, $connection);
}

/**
 * Получает один лот по его id
 * @param int $lotId Id лота
 * @param null $connection Подключение к БД
 *
 * @return mixed Лот
 */
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

/**
 * Получает список ставок для указанного лота
 * @param int $lotId Id лота
 * @param int|null $limit Лимит ставок
 * @param null $connection Подключение к БД
 *
 * @return array|bool|mysqli_result|null Список ставок
 */
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

/**
 * Генерирует уникальное имя для изображения
 * @param string $name Имя файла
 *
 * @return string Уникальное имя
 */
function getRandomPhotoName(string $name)
{
    return uniqid('', true) . '.' . pathinfo($name, PATHINFO_EXTENSION);
}

/**
 * Сохраняет изображение на сервер
 * @param array $image Картинка для сохранения
 * @param string $dir Директория для сохранения
 *
 * @return bool|string Результат загрузки
 */
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

/**
 * Добавляет новый лот
 * @param array $lot Данные лота
 * @param array $photo Данные изображения
 * @param null $connection Подключение к БД
 *
 * @return array|int|string Id добавленного лота или список ошибок
 */
function addLot(array $lot, array $photo, $connection = null)
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

            processQuery($parameterList, $connection);

            return mysqli_insert_id(getConnection());
        }
    } else {
        return $errors;
    }
}

/**
 * Проверяет по списку заполнены ли поля формы
 * @param array $form Данные формы
 * @param array $fields Список обязательных полей
 *
 * @return array Список ошибок
 */
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

/**
 * Проверка начальной цены
 * @param $var Цена
 *
 * @return mixed Результат
 */
function checkFromRate($var)
{
    return filter_var($var, FILTER_VALIDATE_INT);
}

/**
 * Проверка значения шага
 * @param $var Шаг
 *
 * @return mixed Результат
 */
function checkFromStep($var)
{
    return filter_var($var, FILTER_VALIDATE_INT);
}

/**
 * Проверка является ли значение датой
 * @param $var Значение
 *
 * @return bool Результат
 */
function isDate($var)
{
    return is_numeric(strtotime($var));
}

/**
 * Проверка в будущем ли дата
 * @param $var Дата
 *
 * @return bool Результат
 */
function isFutureDate($var)
{
    return strtotime($var) >= strtotime("+1 day");
}

/**
 * Проверка являетяс ли значение электронной почтой
 * @param $var Значение
 *
 * @return mixed Результат
 */
function isEmail($var)
{
    return filter_var($var, FILTER_VALIDATE_EMAIL);
}

/**
 * Получает категорию по ее id
 * @param int $id Id категории
 *
 * @return mixed Категория
 */
function getCat(int $id)
{
    $sql = 'SELECT * FROM category WHERE id = ?';

    $parameterList = [
        'sql' => $sql,
        'data' => [$id],
        'limit' => 1
    ];

    $result = processQuery($parameterList);

    return reset($result);
}

/**
 * Проверка формы перед добавлением нового лота
 * @param array $lot Данные формы
 *
 * @return array Список ошибок
 */
function checkFormAddLot(array $lot)
{
    $errors = formRequiredFields($lot, ['name', 'message', 'rate', 'step']);

    if (!getCat($lot['category'])) {
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

/**
 * Проверка загружаемого изображения
 * @param array $img Изображение
 * @param string $key Именование поля для возврата ошибки
 *
 * @return array Список ошибок
 */
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

/**
 * Проверка формы перед добалвением нового пользователя
 * @param array $user Данные формы
 *
 * @return array Список ошибок
 */
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

/**
 * Добавление нового пользователя
 * @param array $user Данные формы
 * @param array $avatar Загружаемая аватарка
 * @param null $connection Подключение к БД
 *
 * @return array|bool Список ошибок или результат добавления
 */
function addUser(array $user, array $avatar, $connection = null)
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

            processQuery($parameterList, $connection);

            return true;
        }
    } else {
        return $errors;
    }
}

/**
 * Полючение пользователя по электронной почте
 * @param string $email Электронная почта
 * @param null $connection Подключение к БД
 *
 * @return bool|mixed Пользователь
 */
function getUserByEmail(string $email, $connection = null)
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

    $result = processQuery($parameterList, $connection);

    return reset($result);
}

/**
 * Проверка формы перед авторизацией пользователя
 * @param array $user Данные формы
 *
 * @return array Список ошибок
 */
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

/**
 * Обновление кэша пароля у пользователя
 * @param int $userId Id пользователя
 * @param string $password Пароль пользователя
 * @param null $connection Подключение к БД
 *
 * @return bool|string Новый кэш пароля
 */
function passwordUpdate(int $userId, string $password, $connection = null)
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

    processQuery($parameterList, $connection);

    return $newHash;
}

/**
 * Проверка нужно ли обновить кэш пароля у пользователя
 * @param array $queryUser Данные пользователя
 * @param string $password Пароль полльзователя
 *
 * @return bool|mixed|string Кэш пароля
 */
function passwordReHash(array $queryUser, string $password)
{
    if (password_needs_rehash($queryUser['password_hash'], PASSWORD_DEFAULT)) {
        return passwordUpdate($queryUser['id'], $password);
    }

    return $queryUser['password_hash'];
}

/**
 * Авторизация пользователя
 * @param array $user Данные формы
 *
 * @return array Результат авторизации и данные пользователя или список ошибок
 */
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

/**
 * Проверка авторизован ли пользователь
 *
 * @return bool Результат
 */
function isAuthorized()
{
    if (!empty(getCurrentUser())) {
        return true;
    }

    return false;
}

/**
 * Проверка является ли значение ставкой
 * @param $var Значение
 *
 * @return mixed Результат
 */
function isBet($var)
{
    return filter_var($var, FILTER_VALIDATE_INT);
}

/**
 * Проверка формы перед добавлением новой ставки
 * @param array $bet Данные ставки
 * @param $minPrice Минимальная цена
 *
 * @return array Список ошибок
 */
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

/**
 * Добавление новой ставки к лоту
 * @param int $lotId Id лота
 * @param array $bet Данные ставки
 * @param int $minPrice Минимальная цена
 * @param null $connection Подключение к БД
 *
 * @return array|bool Список ошибок или результат
 */
function addBet(int $lotId, array $bet, int $minPrice, $connection = null)
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

        processQuery($parameterList, $connection);

        return true;
    }

    return $errors;
}

/**
 * Проверка нужно ли пользователю показывать форму для ставки
 * @param array $lot Данные лота
 * @param $lastBetUserId Id пользователя сделавшего последнюю ставку
 *
 * @return bool Результат
 */
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

/**
 * Получаем данные сессии пользователя
 *
 * @return bool Данные пользователя
 */
function getCurrentUser()
{
    return getSession()['user'] ?? false;
}

/**
 * Получаем список необработанных завершенных лотов
 * @return array|bool|mysqli_result|null Список лотов
 */
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

/**
 * Обновляем данные о победителе аукциона
 * @param $lot Данные лота
 * @param null $connection Подключение к БД
 *
 * @return bool Есть ли победитель
 */
function updateWinUserLot($lot, $connection = null)
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

    processQuery($parameterList, $connection);

    if ($lot['user_id']) {
        informWinner($lot);
    }

    return $lot['user_id'] ? true : false;
}

/**
 * Оповещаем победителя аукциона о том что он выиграл на электронную почту
 * @param $lot Данные лота
 *
 * @return bool Результат отправки электронного письма
 */
function informWinner($lot)
{
    $mailContent = includeTemplate('email', [
        'userName' => $lot['user_name'],
        'lotId' => $lot['id'],
        'lotName' => $lot['name']
    ]);

    return sendEmail('Ваша ставка победила', [$lot['email'] => $lot['user_name']], $mailContent);
}

/**
 * Получаем подключение SMTP для отправки электронных писем
 *
 * @return null|Swift_SmtpTransport Подключение SMTP
 */
function getSmtp()
{
    static $transport = null;

    if ($transport === null) {
        $config = getConfig();

        $transport = (new Swift_SmtpTransport($config['smtp_server'], $config['smtp_port']))
            ->setUsername($config['smtp_name'])
            ->setPassword($config['smtp_pass']);
    }

    return $transport;
}

/**
 * Отправка электронного письма
 * @param $title Заголовок
 * @param $to Кому
 * @param $content Тект письма
 * @param array $from От кого
 *
 * @return bool Результат отправки
 */
function sendEmail($title, $to, $content, $from = ['keks@phpdemo.ru' => 'Keks'])
{
    $mailer = new Swift_Mailer(getSmtp());

    $message = (new Swift_Message($title))
        ->setFrom($from)
        ->setTo($to)
        ->setBody($content, 'text/html');

    return $mailer->send($message);
}

/**
 * Получить сессию
 *
 * @return array Сессия
 */
function getSession()
{
    static $session = null;

    if ($session === null) {
        $session = $_SESSION;
    }

    return $session;
}

/**
 * Получить супер голобальный массив сервер
 *
 * @return array Данные
 */
function getServer()
{
    static $server = null;

    if ($server === null) {
        $server = $_SERVER;
    }

    return $server;
}

/**
 * Получаем количество лотов с возможностью сортировки по категориям
 * @param bool $active Только активные лоты
 * @param int|null $category Категория
 * @param null $connection Подключение к БД
 *
 * @return int Количество лотов
 */
function getCountLot(bool $active = false, int $category = null, $connection = null)
{
    $sql = 'SELECT COUNT(*) count FROM lot';
    if ($category || $active) {
        $sql .= ' WHERE';
    }
    if ($category) {
        $sql .= ' category_id = ?';
    }
    if ($active) {
        if ($category) {
            $sql .= ' AND';
        }
        $sql .= ' end_time > NOW()';
    }

    $parameterList = [
        'sql' => $sql,
        'data' => [],
        'limit' => null
    ];

    if ($category) {
        $parameterList['data'][] = $category;
    }

    $result = processQuery($parameterList, $connection);

    return reset($result)['count'];
}

/**
 * Расчет данных для пагинации
 * @param int $curPage Текущая страница
 * @param int $pageItem Элементов на страницу
 * @param int $itemsCount Всего элементов
 *
 * @return array Количество старниц и значение сдвига для OFFSET
 */
function pagination(int $curPage = 1, int $pageItem, int $itemsCount)
{
    $pagesCount = ceil($itemsCount / $pageItem);
    $offset = ($curPage - 1) * $pageItem;

    return [$pagesCount, $offset];
}

/**
 * Получаем все ставки пользователя
 * @param null $connection Подключение к БД
 *
 * @return array|bool|mysqli_result|null Список ставок
 */
function getUserBetList($connection = null)
{
    $sql = 'SELECT b.* , l.id lot_id, l.img, l.name, l.win_user_id, l.end_time, c.name category, u.contact
            FROM bet b 
              JOIN lot l ON l.id = b.lot_id
              JOIN category c ON c.id = l.category_id
              JOIN user u ON u.id = l.add_user_id
            WHERE b.user_id = ?';

    $parameterList = [
        'sql' => $sql,
        'data' => [getCurrentUser()['id']],
        'limit' => null
    ];

    return processQuery($parameterList, $connection);
}
