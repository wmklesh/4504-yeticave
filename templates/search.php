<nav class="nav">
    <ul class="nav__list container">
        <?= $_catListContent ?>
    </ul>
</nav>
<div class="container">
    <section class="lots">
        <h2>Результаты поиска по запросу «<span><?= $search ?></span>»</h2>
        <ul class="lots__list">
            <?= !empty($_lotListContent) ? $_lotListContent : 'Ничего не найдено по вашему запросу' ?>
        </ul>
    </section>

</div>
