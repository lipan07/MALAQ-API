<?php

namespace App\Enums;

enum HeavyMachineryBrand: string
{
    case KOMATSU = 'Komatsu';
    case TATA_HITACHI = 'Tata Hitachi';
    case HYUNDAI = 'Hyundai';
    case HITACHI = 'Hitachi';
    case HINDUSTAN_ENTERPRISES = 'Hindustan Enterprises';
    case ACE_CONSTRUCTION_EQUIPMENT = 'ACE Construction Equipment';
    case JCB = 'JCB';
    case JOHN_DEERE = 'John Deere';
    case VOLVO_CONSTRUCTION_EQUIPMENT = 'Volvo Construction Equipment';
    case CASE = 'Case';
    case VOLVO = 'Volvo';
    case SANY = 'Sany';
    case LIEBHERR = 'Liebherr';
        // Additional brands as identified
    case BEML = 'Bharat Earth Movers Limited';
    case L_AND_T_CONSTRUCTION_EQUIPMENT = 'L&T Construction Equipment';
    case SCHWING_STETTER = 'Schwing Stetter India';
    case TEREX = 'Terex India';
    case KOBELCO = 'Kobelco Construction Equipment India';
    case SANY_HEAVY_INDUSTRY_INDIA = 'Sany Heavy Industry India';
    case PUZZOLANA = 'Puzzolana Machinery Fabricators';
    case OTHERS = 'Others';

    /**
     * Returns all types as an array.
     *
     * @return array
     */
    public static function allTypes(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
