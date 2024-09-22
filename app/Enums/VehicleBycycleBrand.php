<?php

namespace App\Enums;

enum VehicleBycycleBrand: string
{
    case Hercules = 'Hercules';
    case Hero = 'Hero';
    case Adrenix = 'Adrenix';
    case Atlas = 'Atlas';
    case BSA = 'BSA';
    case BTwin = 'BTwin';
    case Firefox = 'Firefox';
    case GSports = 'G Sports';
    case Giant = 'Giant';
    case HRX = 'HRX';
    case Keysto = 'Keysto';
    case Leader = 'Leader';
    case Montra = 'Montra';
    case NinetyOne = 'Ninety one';
    case Scott = 'Scott';
    case Trek = 'Trek';
    case Triban = 'Triban';
    case Vector91 = 'Vector 91';
    case OtherBrands = 'Other Brands';

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
