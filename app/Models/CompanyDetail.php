<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CompanyDetail extends Model
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $table = 'company_details';

    protected $fillable = [
        'name',
        'type',
        'address',
        'website',
        'users_id',
    ];
}
