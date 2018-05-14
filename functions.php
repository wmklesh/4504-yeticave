<?php

function formatPrice($num) {
    return sprintf('%s ₽', number_format(ceil($num), 0, '', ' '));
}

function includeTemplate($tpl, $data) {
    if (is_readable(__DIR__ . '/templates/' . $tpl . '.php')) {

        extract($data, EXTR_PREFIX_ALL, '');
        array_walk($data, function (&$value) {
            $value = htmlspecialchars($value);
        });
        extract($data);

        ob_start();
        require __DIR__ . '/templates/' . $tpl . '.php';
        return ob_get_clean();
    }
    return '';
}

function formatLotTimer ($endTime) {
    $endTime = is_int($endTime)?: strtotime($endTime);
    $time = $endTime - time();
    return sprintf('%02d:%02d', ($time / 3600) % 24, ($time / 60) % 60);
}
