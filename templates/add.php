<nav class="nav">
    <ul class="nav__list container">
        <li class="nav__item">
            <a href="all-lots.html">Доски и лыжи</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Крепления</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Ботинки</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Одежда</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Инструменты</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Разное</a>
        </li>
    </ul>
</nav>
<form class="form form--add-lot container <?= !$errorCount ?: 'form--invalid' ?>" enctype="multipart/form-data" action="add.php" method="post"> <!-- form--invalid -->
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <div class="form__item <?= !$errorName ?: 'form__item--invalid' ?>"> <!-- form__item--invalid -->
            <label for="lot-name">Наименование</label>
            <input id="lot-name" type="text" name="lot[name]" value="<?= $name ?>" placeholder="Введите наименование лота" >
            <span class="form__error"><?= $errorName ?></span>
        </div>
        <div class="form__item">
            <label for="category">Категория</label>
            <select id="category" name="lot[category]" >
                <option>Выберите категорию</option>
                <option>Доски и лыжи</option>
                <option>Крепления</option>
                <option>Ботинки</option>
                <option>Одежда</option>
                <option>Инструменты</option>
                <option>Разное</option>
            </select>
            <span class="form__error"><?= $errorCategory ?></span>
        </div>
    </div>
    <div class="form__item form__item--wide <?= !$errorMessage ?: 'form__item--invalid' ?>">
        <label for="message">Описание</label>
        <textarea id="message" name="lot[message]" placeholder="Напишите описание лота" ><?= $message ?></textarea>
        <span class="form__error"><?= $errorMessage ?></span>
    </div>
    <div class="form__item form__item--file"> <!-- form__item--uploaded -->
        <label>Изображение</label>
        <div class="preview">
            <button class="preview__remove" type="button">x</button>
            <div class="preview__img">
                <img src="img/avatar.jpg" width="113" height="113" alt="Изображение лота">
            </div>
        </div>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" id="photo2" name="photo" value="">
            <label for="photo2">
                <span>+ Добавить</span>
            </label>
        </div>
    </div>
    <div class="form__container-three">
        <div class="form__item form__item--small <?= !$errorRate ?: 'form__item--invalid' ?>">
            <label for="lot-rate">Начальная цена</label>
            <input id="lot-rate" type="number" name="lot[rate]" value="<?= $rate ?>" placeholder="0" >
            <span class="form__error"><?= $errorRate ?></span>
        </div>
        <div class="form__item form__item--small <?= !$errorStep ?: 'form__item--invalid' ?>">
            <label for="lot-step">Шаг ставки</label>
            <input id="lot-step" type="number" name="lot[step]" value="<?= $step ?>" placeholder="0" >
            <span class="form__error"><?= $errorStep ?></span>
        </div>
        <div class="form__item <?= !$errorDate ?: 'form__item--invalid' ?>">
            <label for="lot-date">Дата окончания торгов</label>
            <input class="form__input-date" id="lot-date" type="date" name="lot[date]" value="<?= $date ?>" >
            <span class="form__error"><?= $errorDate ?></span>
        </div>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Добавить лот</button>
</form>
