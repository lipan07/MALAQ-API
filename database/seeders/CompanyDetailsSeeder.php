<?php

namespace Database\Seeders;

use App\Models\CompanyDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompanyDetail::create([
            'name' => 'Company A',
            'type' => 'Type 1',
            'address' => '123 Main St, City, Country',
            'website' => 'https://www.companya.com',
            'users_id' => 1,
        ]);

        CompanyDetail::create([
            'name' => 'Company B',
            'type' => 'Type 2',
            'address' => '456 Elm St, City, Country',
            'website' => 'https://www.companyb.com',
            'users_id' => 2,
        ]);

        CompanyDetail::create([
            'name' => 'Company C',
            'type' => 'Type 3',
            'address' => '789 Oak St, City, Country',
            'website' => 'https://www.companyc.com',
            'users_id' => 3,
        ]);
    }
}
