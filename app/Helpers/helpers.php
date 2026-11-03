<?php

// Backward compatibility loader
if (!class_exists(\App\Helpers\Tanggal::class)) {
    require_once __DIR__ . '/Tanggal.php';
}
if (!class_exists(\App\Helpers\Generate::class)) {
    require_once __DIR__ . '/Generate.php';
}
