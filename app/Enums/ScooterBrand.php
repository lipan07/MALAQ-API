<?php

namespace App\Enums;

enum ScooterBrand: string
{
    case Vespa = 'Vespa';
    case Ampere = 'Ampere';
    case Ather = 'Ather';
    case OLA = 'OLA';
    case Husqvarna = 'Husqvarna';
    case Yamaha = 'Yamaha';
    case Vida = 'Vida';
    case Chetak = 'Chetak';
    case BGauss = 'BGauss';
    case BMW = 'BMW';
    case Kymco22 = '22Kymco';
    case Aprilia = 'Aprilia';
    case AvanMotors = 'Avan Motors';
    case Benling = 'Benling';
    case Bounce = 'Bounce';
    case EeVe = 'EeVe';
    case Eider = 'Eider';
    case Evolet = 'Evolet';
    case Gemopai = 'Gemopai';
    case HeroElectric = 'Hero Electric';
    case HeroHonda = 'Hero Honda';
    case Hyosung = 'Hyosung';
    case iVOOMi = 'iVOOMi';
    case JoyEBike = 'Joy e-bike';
    case Kinetic = 'Kinetic';
    case Lambretta = 'Lambretta';
    case LML = 'LML';
    case Odysse = 'Odysse';
    case Okaya = 'Okaya';
    case Okinawa = 'Okinawa';
    case Piaggio = 'Piaggio';
    case PureEV = 'PURE EV';
    case SimpleEnergy = 'Simple Energy';
    case TechoElectra = 'Techo Electra';
    case TwentyTwoMotors = 'Twenty Two Motors';
    case UM = 'UM';
    case Yo = 'Yo';
    case Bajaj = 'Bajaj';
    case Hero = 'Hero';
    case Honda = 'Honda';
    case Mahindra = 'Mahindra';
    case Suzuki = 'Suzuki';
    case TVS = 'TVS';
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
