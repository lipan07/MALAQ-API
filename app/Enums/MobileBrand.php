<?php

namespace App\Enums;

enum MobileBrand: string
{
    case IPHONE = 'iphone';
    case SAMSUNG = 'samsung';
    case MI_PHONE = 'mi-phone';
    case VIVO_PHONE = 'vivo-phone';
    case OPPO_PHONE = 'oppo-phone';
    case REALME = 'realme';
    case ASUS = 'asus';
    case BLACKBERRY = 'phones-blackberry';
    case GIONEE_PHONE = 'gionee-phone';
    case GOOGLE_PIXEL = 'google-pixel';
    case HONOR = 'honor';
    case HTC = 'htc';
    case HUAWEI = 'huawei';
    case INFINIX = 'infinix';
    case INTEX = 'intex';
    case KARBONN = 'karbonn';
    case LAVA = 'lava';
    case LENOVO_MOBILE = 'lenovo-mobile';
    case LG = 'lg';
    case MICROMAX = 'micromax';
    case MOTOROLA_PHONE = 'motorola-phone';
    case NOKIA = 'nokia';
    case ONE_PLUS = 'one-plus';
    case SONY = 'sony';
    case TECHNO = 'techno';
    case OTHER_MOBILES = 'other-mobiles';

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
