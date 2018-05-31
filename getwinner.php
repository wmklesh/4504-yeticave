<?php

$completedLots = getCompletedLot();

foreach ($completedLots as $lot) {
    updateWinUserLot($lot);
}
