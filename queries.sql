USE yeticave;

INSERT INTO category
  (name)
VALUES
  ('Доски и лыжи'),
  ('Крепления'),
  ('Ботинки'),
  ('Одежда'),
  ('Инструменты'),
  ('Разное');

INSERT INTO user
  (email, name, password_hash, contact)
VALUES
  ('ivan@mail.ru', 'Иван', '$2y$11$q5MkhSBtlsJcNEVsYh64a', 'Телефон: +7 901 234 23 23'),
  ('konstantin@mail.ru', 'Константин', '$2y$11$q5MkhSBtlsJcNEVsYh64a', 'Email: konstantin@mail.ru'),
  ('eugene@mail.ru', 'Евгений', '$2y$11$q5MkhSBtlsJcNEVsYh64a', 'Телефон: +7 902 456 56 78 или email: eugene@mail.ru'),
  ('semyon@mail.ru', 'Семён', '$2y$11$q5MkhSBtlsJcNEVsYh64a', 'Тел: +7 903 789 46 75');

INSERT INTO lot
  (add_user_id, category_id, name, description, img, price, price_step, add_time, end_time)
VALUES
  (1, 1, '2014 Rossignol District Snowboard', 'Описание товара', 'img/lot-1.jpg', 10999, 500, '2018-04-02 10:20:45', '2018-05-02 10:20:45'),
  (1, 2, 'DC Ply Mens 2016/2017 Snowboard', 'Описание товара', 'img/lot-2.jpg', 159999, 1000, '2018-04-03 16:23:56', '2018-05-03 16:23:56'),
  (2, 3, 'Крепления Union Contact Pro 2015 года размер L/XL', 'Описание товара', 'img/lot-3.jpg', 8000, 250, '2018-04-04 09:11:54', '2018-05-04 09:11:54'),
  (3, 1, 'Ботинки для сноуборда DC Mutiny Charocal', 'Описание товара', 'img/lot-4.jpg', 10999, 500, '2018-04-05 05:58:03', '2018-05-05 05:58:03'),
  (4, 4, 'Куртка для сноуборда DC Mutiny Charocal', 'Описание товара', 'img/lot-5.jpg', 7500, 100, '2018-04-06 11:55:56', '2018-05-06 11:55:56'),
  (6, 2, 'Маска Oakley Canopy', 'Описание товара', 'img/lot-6.jpg', 5400, 50, '2018-04-06 18:24:46', '2018-05-06 18:24:46');

INSERT INTO bet
  (user_id, lot_id, add_time, price)
VALUES
  (2, 1, '2018-04-07 11:23:56', 11499),
  (4, 1, '2018-04-08 20:57:11', 11999);

/* получить все категории */
SELECT * FROM category;

/* получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, количество ставок, название категории */
SELECT l.name, l.price, img, c.name category_name, SUM(b.lot_id) sum_bet FROM lot l
  JOIN category c ON c.id = l.category_id
  JOIN bet b ON b.lot_id = l.id
WHERE l.end_time > '2018-04-08 16:00:00'
GROUP BY b.lot_id
ORDER BY l.add_time DESC;

/* показать лот по его id. Получите также название категории, к которой принадлежит лот */
SELECT *, c.name category_name FROM lot l
  JOIN category c ON c.id = l.category_id
WHERE l.id = 3 LIMIT 1;

/* обновить название лота по его идентификатору */
UPDATE lot SET name = 'Лучшая маска Oakley Canopy' WHERE id = 6 LIMIT 1;

/* получить список самых свежих ставок для лота по его идентификатору */
SELECT * FROM bet WHERE lot_id = 1 ORDER BY add_time DESC;
