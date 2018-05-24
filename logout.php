<?php

require __DIR__ . '/core/bootstrap.php';

unset($_SESSION['user']);
header('Location: index.php');
