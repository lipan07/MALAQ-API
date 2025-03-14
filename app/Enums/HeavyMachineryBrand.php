<?php

namespace App\Enums;

enum HeavyMachineryBrand: string
{
    case CATERPILLAR = 'Caterpillar';
    case JCB = 'JCB';
    case TATA_HITACHI = 'Tata Hitachi';
    case VOLVO = 'Volvo';
    case KOMATSU = 'Komatsu';
    case L_AND_T_CONSTRUCTION_EQUIPMENT = 'L&T Construction Equipment';
    case BEML = 'BEML (Bharat Earth Movers Limited)';
    case HYUNDAI_CONSTRUCTION_EQUIPMENT = 'Hyundai Construction Equipment';
    case SANY = 'SANY';
    case CASE_CONSTRUCTION = 'Case Construction';
    case DOOSAN = 'Doosan';
    case MAHINDRA_CONSTRUCTION_EQUIPMENT = 'Mahindra Construction Equipment';
    case LIUGONG = 'LiuGong';
    case JOHN_DEERE = 'John Deere';
    case XCMG = 'XCMG';
    case OTHERS = 'Others';

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
