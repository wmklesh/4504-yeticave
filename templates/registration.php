<nav class="nav">
    <ul class="nav__list container">
        <?= $_catListContent ?>
    </ul>
</nav>
<form class="form container <?= !count($errors) ?: 'form--invalid' ?>" action="registration.php" enctype="multipart/form-data" method="post"> <!-- form--invalid -->
    <h2>Регистрация нового аккаунта</h2>
    <div class="form__item <?= !isset($errors['email']) ?: 'form__item--invalid' ?>"> <!-- form__item--invalid -->
        <label for="email">E-mail*</label>
        <input id="email" type="text" name="user[email]" value="<?= $email ?>" placeholder="Введите e-mail" >
        <span class="form__error"><?= $errors['email'] ?? '' ?></span>
    </div>
    <div class="form__item <?= !isset($errors['password']) ?: 'form__item--invalid' ?>">
        <label for="password">Пароль*</label>
        <input id="password" type="text" name="user[password]" value="<?= $password ?>" placeholder="Введите пароль" >
        <span class="form__error"><?= $errors['password'] ?? '' ?></span>
    </div>
    <div class="form__item <?= !isset($errors['name']) ?: 'form__item--invalid' ?>">
        <label for="name">Имя*</label>
        <input id="name" type="text" name="user[name]" value="<?= $name ?>" placeholder="Введите имя" >
        <span class="form__error"><?= $errors['name'] ?? '' ?></span>
    </div>
    <div class="form__item <?= !isset($errors['message']) ?: 'form__item--invalid' ?>">
        <label for="message">Контактные данные*</label>
        <textarea id="message" name="user[message]" placeholder="Напишите как с вами связаться" ><?= $message ?></textarea>
        <span class="form__error"><?= $errors['message'] ?? '' ?></span>
    </div>
    <div class="form__item form__item--file form__item--last <?= !isset($errors['avatar']) ?: 'form__item--invalid' ?>">
        <label>Аватар</label>
        <div class="preview">
            <button class="preview__remove" type="button">x</button>
            <div class="preview__img">
                <img src="img/avatar.jpg" width="113" height="113" alt="Ваш аватар">
            </div>
        </div>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" id="photo2" name="avatar" value="">
            <label for="photo2">
                <span>+ Добавить</span>
            </label>
        </div>
        <span class="form__error"><?= $errors['avatar'] ?></span>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="#">Уже есть аккаунт</a>
</form>
