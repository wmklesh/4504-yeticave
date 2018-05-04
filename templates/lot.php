<li class="lots__item lot">
    <div class="lot__image">
        <img src="<?= $imgUrl ?>" width="350" height="260" alt="<?= $name ?>">
    </div>
    <div class="lot__info">
        <span class="lot__category"><?= $categories ?></span>
        <h3 class="lot__title"><a class="text-link" href="lot.html"><?= $name ?></a></h3>
        <div class="lot__state">
            <div class="lot__rate">
                <span class="lot__amount">Стартовая цена</span>
                <span class="lot__cost"><?= formatPrice($price) ?><b class="rub">р</b></span>
            </div>
            <div class="lot__timer timer">
                <?= formatLotTimer($timeEndLot)?>
            </div>
        </div>
    </div>
</li>
