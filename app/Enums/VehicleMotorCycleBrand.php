<?php

namespace App\Enums;

enum VehicleMotorCycleBrand: string
{
    case HarleyDavidson = 'Harley-Davidson';
    case Yezdi = 'Yezdi';
    case BMWMotorcycle = 'BMW';
    case Kawasaki = 'Kawasaki';
    case Revolt = 'Revolt';
    case Ducati = 'Ducati';
    case Jawa = 'Jawa';
    case Benelli = 'Benelli';
    case AvanturaaChoppers = 'Avanturaa Choppers';
    case CFmoto = 'CFMoto';
    case ClevelandCycleWerks = 'Cleveland CycleWerks';
    case EmfluxMotors = 'Emflux Motors';
    case Escorts = 'Escorts';
    case FBMondial = 'FB Mondial';
    case HopElectric = 'Hop Electric';
    case Indian = 'Indian';
    case Keeway = 'Keeway';
    case LMLMotorcycle = 'LML';
    case MahindraMotorcycle = 'Mahindra';
    case Matter = 'Matter';
    case MotoGuzzi = 'Moto Guzzi';
    case MotoMorini = 'Moto Morini';
    case MVAgusta = 'MV Agusta';
    case Norton = 'Norton';
    case QJMotor = 'QJ Motor';
    case SWM = 'SWM';
    case Tork = 'Tork';
    case Triumph = 'Triumph';
    case Victory = 'Victory';
    case Zontes = 'Zontes';
    case BajajMotorcycle = 'Bajaj';
    case HeroMotorcycle = 'Hero';
    case HeroHondaMotorcycle = 'Hero Honda';
    case HondaMotorcycle = 'Honda';
    case KTM = 'KTM';
    case RoyalEnfield = 'Royal Enfield';
    case SuzukiMotorcycle = 'Suzuki';
    case TVSMotorcycle = 'TVS';
    case YamahaMotorcycle = 'Yamaha';
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
