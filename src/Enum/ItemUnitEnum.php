<?php

namespace App\Enum;

enum ItemUnitEnum : string
{
    case CENTIMETER = 'cm';
    case METER = 'm';
    case MILLIMETER = 'mm';
    case INCH = 'in';
    case FOOT = 'ft';
    case GRAM = 'g';
    case KILOGRAM = 'kg';
    case POUND = 'lb';
    case OUNCE = 'oz';
    case LITER = 'l';
    case MILLILITER = 'ml';
    case GALLON = 'gal';
    case FLUID_OUNCE = 'fl_oz';

    case PIECE = 'pc';

}
