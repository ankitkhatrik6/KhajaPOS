<?php

if (!function_exists('format_npr')) {
    function format_npr($amount, string $prefix = 'Rs. '): string
    {
        return $prefix . number_format((float)($amount ?? 0), 2);
    }
}
