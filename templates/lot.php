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
<section class="lot-item container">
    <h2><?= $name ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="<?= $img ?>" width="730" height="548" alt="<?= $name ?>">
            </div>
            <p class="lot-item__category">Категория: <span><?= $categoryName ?></span></p>
            <p class="lot-item__description"><?= $description ?></p>
        </div>
        <div class="lot-item__right">
            <div class="lot-item__state">
                <div class="lot-item__timer timer">
                    <?= formatLotTimer($endTime, true) ?>
                </div>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span class="lot-item__cost"><?= formatPrice($price) ?></span>
                    </div>
                    <div class="lot-item__min-cost">
                        Мин. ставка <span><?= formatPrice($price + $priceStep) ?></span>
                    </div>
                </div>
                <?php if ($isAuth): ?>
                    <form class="lot-item__form" action="lot.php?id=<?= $lotId ?>" method="post">
                        <p class="lot-item__form-item">
                            <label for="cost">Ваша ставка</label>
                            <input id="cost" type="number" name="bet[cost]" placeholder="<?= ($price + $priceStep) ?>">
                        </p>
                        <button type="submit" class="button">Сделать ставку</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="history">
                <h3>История ставок (<span><?= $betCount ?></span>)</h3>
                <table class="history__list">
                    <?= $_betListContent ?>
                </table>
            </div>
        </div>
    </div>
</section>
