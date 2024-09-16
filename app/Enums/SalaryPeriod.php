<?php

namespace App\Enums;

enum SalaryPeriod: string
{
    case Hourly = 'Hourly';
    case Daily = 'Daily';
    case Weekly = 'Weekly';
    case Monthly = 'Monthly';
    case Yearly = 'Yearly';

    /**
     * Returns the default type.
     *
     * @return Condition
     */
    public static function defaultType(): SalaryPeriod
    {
        return self::Monthly;
    }

    /**
     * Returns all types as an array.
     *
     * @return array
     */
    public static function allTypes(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
