<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $regions = [
            [
                'name' => 'North Region',
                'code' => 'NORTH',
                'description' => 'Northern region covering Delhi, Punjab, Haryana, Uttar Pradesh',
                'status' => 1,
            ],
            [
                'name' => 'South Region',
                'code' => 'SOUTH',
                'description' => 'Southern region covering Karnataka, Tamil Nadu, Kerala, Andhra Pradesh',
                'status' => 1,
            ],
            [
                'name' => 'West Region',
                'code' => 'WEST',
                'description' => 'Western region covering Maharashtra, Gujarat, Rajasthan, Goa',
                'status' => 1,
            ],
            [
                'name' => 'East Region',
                'code' => 'EAST',
                'description' => 'Eastern region covering West Bengal, Odisha, Bihar, Jharkhand',
                'status' => 1,
            ],
            [
                'name' => 'Central Region',
                'code' => 'CENTRAL',
                'description' => 'Central region covering Madhya Pradesh, Chhattisgarh',
                'status' => 1,
            ],
        ];

        foreach ($regions as $region) {
            Region::create($region);
        }
    }
}
