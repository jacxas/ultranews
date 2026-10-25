<?php

namespace App\Support;

/**
 * Formateo de números para las vistas.
 *
 * Se implementa a mano (sin la extensión intl) para que funcione en
 * cualquier instalación de PHP: 1.500 → "1,5K", 15.400.000.000 → "15,4B".
 */
class NumberFormat
{
    /**
     * Abrevia un número con sufijos K / M / B.
     */
    public static function compact(float|int|null $number, int $decimals = 1): string
    {
        $number = (float) ($number ?? 0);
        $abs = abs($number);

        $format = function (float $value, string $suffix) use ($decimals): string {
            $formatted = number_format($value, $decimals + 1, '.', '');
            $formatted = rtrim(rtrim($formatted, '0'), '.');

            // Punto decimal → coma (estilo es-AR) solo para la parte abreviada.
            return str_replace('.', ',', $formatted).$suffix;
        };

        return match (true) {
            $abs >= 1_000_000_000 => $format($number / 1_000_000_000, 'B'),
            $abs >= 1_000_000 => $format($number / 1_000_000, 'M'),
            $abs >= 1_000 => $format($number / 1_000, 'K'),
            default => number_format($number, 0, ',', '.'),
        };
    }
}
