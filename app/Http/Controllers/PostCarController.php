<?php

namespace App\Http\Controllers;

use App\Enums\VehicleBycycleBrand;
use App\Enums\VehicleMotorCycleBrand;
use App\Enums\VehicleScooterBrand;
use App\Http\Requests\StorePostCarRequest;
use App\Http\Requests\UpdatePostCarRequest;
use App\Models\PostCar;

class PostCarController extends Controller
{
    public function scooterBrand()
    {
        return response()->json(VehicleScooterBrand::allTypes(), 200);
    }

    public function bycycleBrand()
    {
        return response()->json(VehicleBycycleBrand::allTypes(), 200);
    }

    public function motorcycleBrand()
    {
        return response()->json(VehicleMotorCycleBrand::allTypes(), 200);
    }
}
