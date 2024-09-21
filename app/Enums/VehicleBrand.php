<?php

namespace App\Enums;

enum VehicleBrand: string
{
        //Bycycle
    case Hercules = 'Hercules';
    case Hero = 'Hero';
    case Adrenix = 'Adrenix';
    case Atlas = 'Atlas';
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
    case Vector = 'Vector 91';
        //Motorcycle
    case HarleyDavidson = 'Harley-Davidson';
    case Yezdi = 'Yezdi';
    case BMW = 'BMW';
    case Kawasaki = 'Kawasaki';
    case Revolt = 'Revolt';
    case Ducati = 'Ducati';
    case Jawa = 'Jawa';
    case Benelli = 'Benelli';
    case AvanturaaChoppers = 'Avanturaa Choppers';
    case BSA = 'BSA';
    case CFMoto = 'CFMoto';
    case ClevelandCycleWerks = 'Cleveland CycleWerks';
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
    case Victory = 'Victory';
    case Vida = 'Vida';
    case Zontes = 'Zontes';
    case Bajaj = 'Bajaj';
    case HeroHonda = 'Hero Honda';
    case Honda = 'Honda';
    case KTM = 'KTM';
    case RoyalEnfield = 'Royal Enfield';
    case Suzuki = 'Suzuki';
    case TVS = 'TVS';
    case Yamaha = 'Yamaha';
        //Scooter
    case Vespa = 'Vespa';
    case Ampere = 'Ampere';
    case Ather = 'Ather';
    case Chetak = 'Chetak';
    case BGauss = 'BGauss';
    case Kymco22 = '22Kymco';
    case Aprilia = 'Aprilia';
    case AvanMotors = 'Avan Motors';
    case Benling = 'Benling';
    case Bounce = 'Bounce';
    case EeVe = 'EeVe';
    case Eider = 'Eider';
    case Gemopai = 'Gemopai';
    case iVOOMi = 'iVOOMi';
    case JoyEBike = 'Joy e-bike';
    case Kinetic = 'Kinetic';
    case Lambretta = 'Lambretta';
    case Okaya = 'Okaya';
    case Piaggio = 'Piaggio';
    case SimpleEnergy = 'Simple Energy';
    case TechoElectra = 'Techo Electra';
    case TwentyTwoMotors = 'Twenty Two Motors';
    case Yo = 'Yo';
        //Other
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
