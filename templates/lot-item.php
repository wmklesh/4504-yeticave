<li class="lots__item lot">
    <div class="lot__image">
        <img src="<?= $img ?>" width="350" height="260" alt="<?= $name ?>">
    </div>
    <div class="lot__info">
        <span class="lot__category"><?= $categoryName ?></span>
        <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?= $id ?>"><?= $name ?></a></h3>
        <div class="lot__state">
            <div class="lot__rate">
                <span class="lot__amount">Стартовая цена</span>
                <span class="lot__cost"><?= formatPrice($price) ?><b class="rub">р</b></span>
            </div>
            <div class="lot__timer timer <?= finishTimer($end_time) ? 'timer--finishing' : null?>">
                <?= formatLotTimer($end_time) ?>
            </div>
        </div>
    </div>
</li>
