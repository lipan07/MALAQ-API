<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            ['name' => 'Cars'],
            ['name' => 'Properties', 'children' => [
                ['name' => 'Houses & Apartments'],
                ['name' => 'Land & Plots'],
                ['name' => 'PG & Guest Houses'],
                ['name' => 'Shop & Offices'],
            ]],
            ['name' => 'Mobiles'],
            ['name' => 'Job', 'children' => [
                ['name' => 'Data entry and Back office'],
                ['name' => 'Sales & Marketing'],
                ['name' => 'BPO & Telecaller'],
                ['name' => 'Driver'],
                ['name' => 'Office Assistant'],
                ['name' => 'Delivery & Collection'],
                ['name' => 'Teacher'],
                ['name' => 'Cook'],
                ['name' => 'Receptionist & Front office'],
                ['name' => 'Operator & Technician'],
                ['name' => 'IT Engineer & Developer'],
                ['name' => 'Hotel & Travel Executive'],
                ['name' => 'Accountant'],
                ['name' => 'Designer'],
                ['name' => 'Other Jobs']
            ]],
        ];

        foreach ($categories as $category) {
            $createdCategory = Category::create(['name' => $category['name']]);

            if (isset($category['children'])) {
                foreach ($category['children'] as $child) {
                    Category::create(['name' => $child['name'], 'parent_id' => $createdCategory->id]);
                }
            }
        }
    }
}
