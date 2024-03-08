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
                ['name' => 'Shop & Offices']
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
            ['name' => 'Bikes', 'children' => [
                ['name' => 'Motorecycles'],
                ['name' => 'Scooters'],
                ['name' => 'Bycycles'],
                ['name' => 'Accessories'],
            ]],
            ['name' => 'Electronics & Appliances', 'children' => [
                ['name' => 'Computers & Laptops'],
                ['name' => 'TVs, Video & Audio'],
                ['name' => 'ACs'],
                ['name' => 'Fridges'],
                ['name' => 'Washing Machines'],
                ['name' => 'Cameras & Lenses'],
                ['name' => 'Harddisks. Printers & Monitors'],
                ['name' => 'Kitchen & Other Appliances'],
                ['name' => 'Accessories']
            ]],
            ['name' => 'Commercial Vehicle & Spare Parts', 'children' => [
                ['name' => 'Commercial & Heavy Vehicles'],
                ['name' => 'Spare Parts']
            ]],
            ['name' => 'Commercial Machinery & Spare Parts', 'children' => [
                ['name' => 'Commercial & Heavy Machinery'],
                ['name' => 'Spare Parts']
            ]],
            ['name' => 'Furniture', 'children' => [
                ['name' => 'Sofa & Dining'],
                ['name' => 'Beds & Wardrobes'],
                ['name' => 'Home Decor and Garden'],
                ['name' => 'Kids Furniture'],
                ['name' => 'Other Household Items']
            ]],
            ['name' => 'Fashion', 'children' => [
                ['name' => 'Men'],
                ['name' => 'Women'],
                ['name' => 'Kids']
            ]],
            ['name' => 'Books, Sports & Hobbies', 'children' => [
                ['name' => 'Books'],
                ['name' => 'Gym & Fitness'],
                ['name' => 'Musical Instruments'],
                ['name' => 'Sports Equipment'],
                ['name' => 'Other Hobbies']
            ]],
            ['name' => 'Pets', 'children' => [
                ['name' => 'Dogs'],
                ['name' => 'Fish & Aquarium'],
                ['name' => 'Pets Food & Accessories'],
                ['name' => 'Other Pets']
            ]],
            ['name' => 'Services', 'children' => [
                ['name' => 'Education & Classes'],
                ['name' => 'Tours & Travels'],
                ['name' => 'Electronics Repair and Services'],
                ['name' => 'Health & Beauty'],
                ['name' => 'Home Renovation and Repair'],
                ['name' => 'Cleaning & Pest Control'],
                ['name' => 'Legal & Documentation Services'],
                ['name' => 'Packers and Movers'],
                ['name' => 'Other Services']
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
