<?php

function formatPrice($num) {
    return number_format(ceil($num), 0, '', ' ') . ' ₽';
}

function includeTemplate($tpl, $data) {
    if (is_readable(__DIR__ . '/templates/' . $tpl . '.php')) {

        extract($data, EXTR_PREFIX_ALL, '_');
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
