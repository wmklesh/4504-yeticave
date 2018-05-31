<tr class="rates__item <?= finishTimer($endLotTime) && $winUserId != $userId ? 'rates__item--end' : '' ?>">
    <td class="rates__info">
        <div class="rates__img">
            <img src="<?= $img ?>" width="54" height="40" alt="<?= $name ?>">
        </div>
        <h3 class="rates__title"><a href="lot.php?id=<?= $lotId ?>"><?= $name ?></a></h3>
        <?php if (finishTimer($endLotTime)): ?>
            <p><?= $contact ?></p>
        <?php endif; ?>
    </td>
    <td class="rates__category">
        <?= $category ?>
    </td>
    <td class="rates__timer">
        <?php if ($winUserId == $userId): ?>
            <div class="timer timer--win">Ставка выиграла</div>
        <?php elseif (finishTimer($endLotTime)): ?>
            <div class="timer timer--end">Торги окончены</div>
        <?php else: ?>
            <div class="timer"><?= formatLotTimer($endLotTime, true)?></div>
        <?php endif; ?>
    </td>
    <td class="rates__price">
        <?= formatPrice($price)?>
    </td>
    <td class="rates__time">
        <?= formatBetTime($addBetTime)?>
    </td>
</tr>
