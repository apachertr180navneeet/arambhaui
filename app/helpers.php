<?php

if (!function_exists('format_quantity')) {
    /**
     * Format a quantity or number cleanly:
     * - If value has decimal point digits (e.g. 12.55), show decimal point digits (12.55)
     * - If value is a whole number (e.g. 12 or 12.00), show integer without decimal point (12)
     */
    function format_quantity($val, $maxDecimals = 2)
    {
        if ($val === null || $val === '') {
            return '0';
        }
        $floatVal = (float)$val;
        if ($floatVal == (int)$floatVal) {
            return (string)(int)$floatVal;
        }
        $formatted = number_format($floatVal, $maxDecimals, '.', '');
        return rtrim(rtrim($formatted, '0'), '.');
    }
}
