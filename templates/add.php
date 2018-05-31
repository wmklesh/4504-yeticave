<nav class="nav">
    <ul class="nav__list container">
        <?= $_catListContent ?>
    </ul>
</nav>
<form class="form form--add-lot container <?= !count($errors) ?: 'form--invalid' ?>" enctype="multipart/form-data" action="add.php" method="post"> <!-- form--invalid -->
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <div class="form__item <?= !isset($errors['name']) ?: 'form__item--invalid' ?>"> <!-- form__item--invalid -->
            <label for="lot-name">Наименование</label>
            <input id="lot-name" type="text" name="lot[name]" value="<?= $name ?>" placeholder="Введите наименование лота" >
            <span class="form__error"><?= $errors['name'] ?? '' ?></span>
        </div>
        <div class="form__item <?= !isset($errors['category']) ?: 'form__item--invalid' ?>">
            <label for="category">Категория</label>
            <select id="category" name="lot[category]" >
                <option value="0">Выберите категорию</option>
                <?php foreach ($categoryList as $cat): ?>
                    <option <?= $category == $cat['id'] ? 'selected' : '' ?> value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                <?php endforeach;?>
            </select>
            <span class="form__error"><?= $errors['category'] ?></span>
        </div>
    </div>
    <div class="form__item form__item--wide <?= !isset($errors['message']) ?: 'form__item--invalid' ?>">
        <label for="message">Описание</label>
        <textarea id="message" name="lot[message]" placeholder="Напишите описание лота" ><?= $message ?></textarea>
        <span class="form__error"><?= $errors['message'] ?></span>
    </div>
    <div class="form__item form__item--file <?= !isset($errors['photo']) ?: 'form__item--invalid' ?>"> <!-- form__item--uploaded -->
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
        <span class="form__error"><?= $errors['photo'] ?></span>
    </div>
    <div class="form__container-three">
        <div class="form__item form__item--small <?= !isset($errors['rate']) ?: 'form__item--invalid' ?>">
            <label for="lot-rate">Начальная цена</label>
            <input id="lot-rate" type="number" name="lot[rate]" value="<?= $rate ?>" placeholder="0" >
            <span class="form__error"><?= $errors['rate'] ?></span>
        </div>
        <div class="form__item form__item--small <?= !isset($errors['step']) ?: 'form__item--invalid' ?>">
            <label for="lot-step">Шаг ставки</label>
            <input id="lot-step" type="number" name="lot[step]" value="<?= $step ?>" placeholder="0" >
            <span class="form__error"><?= $errors['step'] ?></span>
        </div>
        <div class="form__item <?= !isset($errors['date']) ?: 'form__item--invalid' ?>">
            <label for="lot-date">Дата окончания торгов</label>
            <input class="form__input-date" id="lot-date" type="date" name="lot[date]" value="<?= $date ?>" >
            <span class="form__error"><?= $errors['date'] ?></span>
        </div>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Добавить лот</button>
</form>
