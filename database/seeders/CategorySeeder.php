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
            ['name' => 'Cars', 'guard_name' => 'cars'],
            ['name' => 'Properties', 'guard_name' => 'properties', 'children' => [
                ['name' => 'Houses & Apartments', 'guard_name' => 'houses_apartments'],
                ['name' => 'Land & Plots', 'guard_name' => 'land_plots'],
                ['name' => 'PG & Guest Houses', 'guard_name' => 'pg_guest_houses'],
                ['name' => 'Shop & Offices', 'guard_name' => 'shop_offices']
            ]],
            ['name' => 'Mobiles', 'guard_name' => 'mobiles'],
            ['name' => 'Job', 'guard_name' => 'job', 'children' => [
                ['name' => 'Data entry and Back office', 'guard_name' => 'data_entry_back_office'],
                ['name' => 'Sales & Marketing', 'guard_name' => 'sales_marketing'],
                ['name' => 'BPO & Telecaller', 'guard_name' => 'bpo_telecaller'],
                ['name' => 'Driver', 'guard_name' => 'driver'],
                ['name' => 'Office Assistant', 'guard_name' => 'office_assistant'],
                ['name' => 'Delivery & Collection', 'guard_name' => 'delivery_collection'],
                ['name' => 'Teacher', 'guard_name' => 'teacher'],
                ['name' => 'Cook', 'guard_name' => 'cook'],
                ['name' => 'Receptionist & Front office', 'guard_name' => 'receptionist_front_office'],
                ['name' => 'Operator & Technician', 'guard_name' => 'operator_technician'],
                ['name' => 'IT Engineer & Developer', 'guard_name' => 'engineer_developer'],
                ['name' => 'Hotel & Travel Executive', 'guard_name' => 'hotel_travel_executive'],
                ['name' => 'Accountant', 'guard_name' => 'accountant'],
                ['name' => 'Designer', 'guard_name' => 'designer'],
                ['name' => 'Other Jobs', 'guard_name' => 'other_jobs']
            ]],
            ['name' => 'Bikes', 'guard_name' => 'bikes', 'children' => [
                ['name' => 'Motorecycles', 'guard_name' => 'motorcycles'],
                ['name' => 'Scooters', 'guard_name' => 'scooters'],
                ['name' => 'Bycycles', 'guard_name' => 'bycycles'],
                ['name' => 'Accessories', 'guard_name' => 'accessories'],
            ]],
            ['name' => 'Electronics & Appliances', 'guard_name' => 'electronics_appliances', 'children' => [
                ['name' => 'Computers & Laptops', 'guard_name' => 'computers_laptops'],
                ['name' => 'TVs, Video & Audio', 'guard_name' => 'tvs_video_audio'],
                ['name' => 'ACs', 'guard_name' => 'acs'],
                ['name' => 'Fridges', 'guard_name' => 'fridges'],
                ['name' => 'Washing Machines', 'guard_name' => 'washing_machines'],
                ['name' => 'Cameras & Lenses', 'guard_name' => 'cameras_lenses'],
                ['name' => 'Harddisks. Printers & Monitors', 'guard_name' => 'harddisks_printers_monitors'],
                ['name' => 'Kitchen & Other Appliances', 'guard_name' => 'kitchen_other_appliances'],
                ['name' => 'Accessories', 'guard_name' => 'accessories']
            ]],
            ['name' => 'Commercial Vehicle & Spare Parts', 'guard_name' => 'commercial_vehicle_spare_part', 'children' => [
                ['name' => 'Commercial & Heavy Vehicles', 'guard_name' => 'commercial_heavy_vehicles'],
                ['name' => 'Spare Parts', 'guard_name' => 'vehicle_spare_parts']
            ]],
            ['name' => 'Commercial Machinery & Spare Parts', 'guard_name' => 'commercial_mechinery_spare_parts', 'children' => [
                ['name' => 'Commercial & Heavy Machinery', 'guard_name' => 'commercial_heavy_machinery'],
                ['name' => 'Spare Parts', 'guard_name' => 'machinery_spare_parts']
            ]],
            ['name' => 'Furniture', 'guard_name' => 'furniture', 'children' => [
                ['name' => 'Sofa & Dining', 'guard_name' => 'sofa_dining'],
                ['name' => 'Beds & Wardrobes', 'guard_name' => 'beds_wardrobes'],
                ['name' => 'Home Decor and Garden', 'guard_name' => 'home_decor_garden'],
                ['name' => 'Kids Furniture', 'guard_name' => 'kids_furniture'],
                ['name' => 'Other Household Items', 'guard_name' => 'other_household_items']
            ]],
            ['name' => 'Fashion', 'guard_name' => 'fashion', 'children' => [
                ['name' => 'Men', 'guard_name' => 'mens_fashion'],
                ['name' => 'Women', 'guard_name' => 'womens_fashion'],
                ['name' => 'Kids', 'guard_name' => 'kids_fashion']
            ]],
            ['name' => 'Books, Sports & Hobbies', 'guard_name' => 'boks_sports_hobbies', 'children' => [
                ['name' => 'Books', 'guard_name' => 'books'],
                ['name' => 'Gym & Fitness', 'guard_name' => 'gym_fitness'],
                ['name' => 'Musical Instruments', 'guard_name' => 'musical_instruments'],
                ['name' => 'Sports Equipment', 'guard_name' => 'sports_instrument'],
                ['name' => 'Other Hobbies', 'guard_name' => 'other_hobbies']
            ]],
            ['name' => 'Pets', 'guard_name' => 'pets', 'children' => [
                ['name' => 'Dogs', 'guard_name' => 'dogs'],
                ['name' => 'Fish & Aquarium', 'guard_name' => 'fish_aquarium'],
                ['name' => 'Pets Food & Accessories', 'guard_name' => 'pets_food_accessories'],
                ['name' => 'Other Pets', 'guard_name' => 'other_pets']
            ]],
            ['name' => 'Services', 'guard_name' => 'services', 'children' => [
                ['name' => 'Education & Classes', 'guard_name' => 'education_classes'],
                ['name' => 'Tours & Travels', 'guard_name' => 'tours_travels'],
                ['name' => 'Electronics Repair and Services', 'guard_name' => 'electronics_repair_services'],
                ['name' => 'Health & Beauty', 'guard_name' => 'health_beauty'],
                ['name' => 'Home Renovation and Repair', 'guard_name' => 'home_renovation_repair'],
                ['name' => 'Cleaning & Pest Control', 'guard_name' => 'cleaning_pest_control'],
                ['name' => 'Legal & Documentation Services', 'guard_name' => 'legal_documentation_sevices'],
                ['name' => 'Packers and Movers', 'guard_name' => 'packers_movers'],
                ['name' => 'Other Services', 'guard_name' => 'other_services']
            ]],
        ];

        foreach ($categories as $category) {
            $createdCategory = Category::create(['name' => $category['name'], 'guard_name' => $category['guard_name']]);

            if (isset($category['children'])) {
                foreach ($category['children'] as $child) {
                    Category::create(['name' => $child['name'], 'guard_name' => $child['guard_name'], 'parent_id' => $createdCategory->id]);
                }
            }
        }
    }
}
