<nav class="nav">
    <ul class="nav__list container">
        <?= $_catListContent ?>
    </ul>
</nav>
<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        <?= !empty($_betListContent) ? $_betListContent : 'У Вас нет ставок' ?>
    </table>
</section>
