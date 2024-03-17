<?php

namespace App\Enums;

enum CommercialVehicleBrand: string
{
    case TATA_MOTORS = 'Tata Motors Limited';
    case MAHINDRA_AND_MAHINDRA = 'Mahindra & Mahindra Limited';
    case EICHER_MOTORS = 'Eicher Motors Limited';
    case ASHOK_LEYLAND = 'Ashok Leyland Limited';
    case FORCE_MOTORS = 'Force Motors Limited';
    case SML_ISUZU = 'SML ISUZU Limited';
    case HINDUSTAN_MOTORS = 'Hindustan Motors';
    case BHARATBENZ = 'Daimler India Commercial Vehicles\' BharatBenz';
    case VOLVO_TRUCKS = 'Volvo Trucks';
    case ASIA_MOTORWORKS = 'Asia Motorworks';
    case SCANIA = 'Scania Commercial Vehicles India Pvt. Ltd.';
    case MAN_TRUCKS = 'MAN Trucks India Pvt. Ltd.';
    case IVECO = 'Iveco';
    case BEML = 'Bharat Earth Movers Limited (BEML)';
    case JCB_INDIA = 'JCB India Limited';
    case KOMATSU_INDIA = 'Komatsu India Private Limited';
    case CATERPILLAR_INDIA = 'Caterpillar India';
    case JOHN_DEERE_INDIA = 'John Deere India Private Limited';
    case CNH_INDIA = 'Case New Holland Construction Equipment India Private Limited';
    case L_AND_T_CONSTRUCTION = 'L&T Construction Equipment Limited';
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
