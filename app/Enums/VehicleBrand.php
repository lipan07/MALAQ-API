<?php

namespace App\Enums;

enum VehicleBrand: string
{
        // Scooters
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
    case IVOOMi = 'iVOOMi';
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
    case BajajScooter = 'Bajaj';
    case HeroScooter = 'Hero';
    case HondaScooter = 'Honda';
    case MahindraScooter = 'Mahindra';
    case SuzukiScooter = 'Suzuki';
    case TVSScooter = 'TVS';

        // Bicycles
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
    case BicyclesOther = 'Other Brands';

        // Motorcycles
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
