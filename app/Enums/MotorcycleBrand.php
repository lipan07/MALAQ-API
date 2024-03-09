<?php

namespace App\Enums;

enum MotorcycleBrand: string
{
    case HarleyDavidson = 'Harley-Davidson';
    case Yezdi = 'Yezdi';
    case BMW = 'BMW';
    case Kawasaki = 'Kawasaki';
    case Revolt = 'Revolt';
    case Ducati = 'Ducati';
    case Jawa = 'Jawa';
    case Benelli = 'Benelli';
    case Aprilia = 'Aprilia';
    case AvanturaaChoppers = 'Avanturaa Choppers';
    case BSA = 'BSA';
    case CFMoto = 'CFMoto';
    case ClevelandCycleWerks = 'Cleveland CycleWerks';
    case Eider = 'Eider';
    case EmfluxMotors = 'Emflux Motors';
    case Escorts = 'Escorts';
    case Evolet = 'Evolet';
    case FBMondial = 'FB Mondial';
    case HeroElectric = 'Hero Electric';
    case HopElectric = 'Hop Electric';
    case Husqvarna = 'Husqvarna';
    case Hyosung = 'Hyosung';
    case Indian = 'Indian';
    case Keeway = 'Keeway';
    case LML = 'LML';
    case Mahindra = 'Mahindra';
    case Matter = 'Matter';
    case MotoGuzzi = 'Moto Guzzi';
    case MotoMorini = 'Moto Morini';
    case MVAgusta = 'MV Agusta';
    case Norton = 'Norton';
    case Odysse = 'Odysse';
    case Okinawa = 'Okinawa';
    case OLA = 'OLA';
    case PureEV = 'PURE EV';
    case QJMotor = 'QJ Motor';
    case SWM = 'SWM';
    case Tork = 'Tork';
    case Triumph = 'Triumph';
    case UM = 'UM';
    case Vespa = 'Vespa';
    case Victory = 'Victory';
    case Vida = 'Vida';
    case Zontes = 'Zontes';
    case Bajaj = 'Bajaj';
    case Hero = 'Hero';
    case HeroHonda = 'Hero Honda';
    case Honda = 'Honda';
    case KTM = 'KTM';
    case RoyalEnfield = 'Royal Enfield';
    case Suzuki = 'Suzuki';
    case TVS = 'TVS';
    case Yamaha = 'Yamaha';
    case OtherBrands = 'Other Brands';

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
