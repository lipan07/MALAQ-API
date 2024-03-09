<?php

namespace App\Enums;

enum SalaryPeriod: string
{
    case Hourly = 'hourly';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Yearly = 'yearly';

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
        return [self::Hourly, self::Weekly, self::Monthly, self::Yearly];
    }
}
