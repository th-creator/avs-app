<?php

class AY
{
    const START_MONTH = 9; // September

    public static function fromYearMonth(int $year, int $month): ?string
    {
        if ($month === 8) {
            return null; // Exclude August
        }

        if ($month >= self::START_MONTH) {
            return "{$year}/" . ($year + 1);
        }

        if ($month <= 6) {
            return ($year - 1) . "/{$year}";
        }

        return null;
    }
}
