<?php

function formatPrice($num) {
    return number_format(ceil($num), 0, '', ' ') . ' ₽';
}

function includeTemplate($tpl, $data) {
    if (file_exists('templates/' . $tpl)) {
        foreach ($data as $key => $value) {
            $$key = $value;
        }
        ob_start();
        require_once 'templates/' . $tpl;
        return ob_get_clean();
    }
    return '';
}
