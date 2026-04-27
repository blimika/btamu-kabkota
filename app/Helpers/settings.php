<?php
// JANGAN ada baris namespace di sini!

if (!function_exists('get_setting')) {
    function get_setting($key, $default = null) {
        return cache()->rememberForever("setting.{$key}", function () use ($key, $default) {
            $setting = \App\Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }
}
