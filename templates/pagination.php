<ul class="pagination-list">
    <li class="pagination-item pagination-item-prev"><a
            <?= $curPage > 1
                ? 'href=' . $link . 'page=' . ($curPage - 1)
                : ''
            ?>>Назад</a></li>
    <?php foreach ($pages as $page): ?>
        <li class="pagination-item
            <?= $curPage == $page
            ? 'pagination-item-active'
            : '' ?>">
            <a <?= $curPage != $page
                ? 'href="' . $link . 'page=' . $page . '"'
                : '' ?>
            ><?= $page ?></a>
        </li>
    <?php endforeach; ?>
    <li class="pagination-item pagination-item-next"><a
            <?= $curPage < $pageCount
                ? 'href=' . $link . 'page=' . ($curPage + 1)
                : ''
            ?>>Вперед</a></li>
</ul>
