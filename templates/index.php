<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <li class="promo__item promo__item--boards">
            <a class="promo__link" href="index.php?category=1">Доски и лыжи</a>
        </li>
        <li class="promo__item promo__item--attachment">
            <a class="promo__link" href="index.php?category=2">Крепления</a>
        </li>
        <li class="promo__item promo__item--boots">
            <a class="promo__link" href="index.php?category=3">Ботинки</a>
        </li>
        <li class="promo__item promo__item--clothing">
            <a class="promo__link" href="index.php?category=4">Одежда</a>
        </li>
        <li class="promo__item promo__item--tools">
            <a class="promo__link" href="index.php?category=5">Инструменты</a>
        </li>
        <li class="promo__item promo__item--other">
            <a class="promo__link" href="index.php?category=5">Разное</a>
        </li>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
    <ul class="lots__list">
        <?= !empty($_lotListContent) ? $_lotListContent : 'Нет открытых лотов в данной категории' ?>
    </ul>
    <?= $_paginationContent ?>
</section>
