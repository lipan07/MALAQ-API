<?php

namespace App\Enums;

enum CarBrand: string
{
    case MarutiSuzuki = 'Maruti Suzuki';
    case Hyundai = 'Hyundai';
    case Tata = 'Tata';
    case Mahindra = 'Mahindra';
    case Toyota = 'Toyota';
    case Honda = 'Honda';
    case BYD = 'BYD';
    case Audi = 'Audi';
    case Ambassador = 'Ambassador';
    case Ashok = 'Ashok';
    case AshokLeyland = 'Ashok Leyland';
    case Aston = 'Aston';
    case AstonMartin = 'Aston Martin';
    case Bajaj = 'Bajaj';
    case Bentley = 'Bentley';
    case Citroen = 'Citroen';
    case McLaren = 'McLaren';
    case Fisker = 'Fisker';
    case BMW = 'BMW';
    case Bugatti = 'Bugatti';
    case Cadillac = 'Cadillac';
    case Caterham = 'Caterham';
    case Chevrolet = 'Chevrolet';
    case Chrysler = 'Chrysler';
    case Conquest = 'Conquest';
    case Daewoo = 'Daewoo';
    case Datsun = 'Datsun';
    case Dc = 'Dc';
    case Dodge = 'Dodge';
    case EicherPolaris = 'Eicher Polaris';
    case Ferrari = 'Ferrari';
    case Fiat = 'Fiat';
    case ForceMotors = 'Force Motors';
    case Ford = 'Ford';
    case Hummer = 'Hummer';
    case ICML = 'ICML';
    case Infiniti = 'Infiniti';
    case Isuzu = 'Isuzu';
    case Jaguar = 'Jaguar';
    case Jeep = 'Jeep';
    case Kia = 'Kia';
    case Lamborghini = 'Lamborghini';
    case LandRover = 'Land Rover';
    case Lexus = 'Lexus';
    case MahindraRenault = 'Mahindra Renault';
    case Maserati = 'Maserati';
    case Maybach = 'Maybach';
    case Mazda = 'Mazda';
    case MercedesBenz = 'Mercedes-Benz';
    case MG = 'MG';
    case Mini = 'Mini';
    case Mitsubishi = 'Mitsubishi';
    case Nissan = 'Nissan';
    case Opel = 'Opel';
    case Peugeot = 'Peugeot';
    case Porsche = 'Porsche';
    case Premier = 'Premier';
    case Renault = 'Renault';
    case RollsRoyce = 'Rolls-Royce';
    case San = 'San';
    case Sipani = 'Sipani';
    case Skoda = 'Skoda';
    case Smart = 'Smart';
    case Ssangyong = 'Ssangyong';
    case Subaru = 'Subaru';
    case Volkswagen = 'Volkswagen';
    case Volvo = 'Volvo';
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
