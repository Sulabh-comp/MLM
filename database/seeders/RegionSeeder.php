<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            [
                'name' => 'North India',
                'code' => 'NORTH',
                'description' => 'Northern region covering states like Delhi, Punjab, Haryana, Uttar Pradesh, etc.',
                'states' => ['Delhi', 'Punjab', 'Haryana', 'Uttar Pradesh', 'Uttarakhand', 'Himachal Pradesh', 'Jammu and Kashmir'],
                'status' => 1
            ],
            [
                'name' => 'South India',
                'code' => 'SOUTH',
                'description' => 'Southern region covering states like Karnataka, Tamil Nadu, Kerala, Andhra Pradesh, etc.',
                'states' => ['Karnataka', 'Tamil Nadu', 'Kerala', 'Andhra Pradesh', 'Telangana'],
                'status' => 1
            ],
            [
                'name' => 'East India',
                'code' => 'EAST',
                'description' => 'Eastern region covering states like West Bengal, Odisha, Jharkhand, Bihar, etc.',
                'states' => ['West Bengal', 'Odisha', 'Jharkhand', 'Bihar', 'Assam', 'Tripura', 'Manipur', 'Meghalaya', 'Mizoram', 'Nagaland', 'Arunachal Pradesh', 'Sikkim'],
                'status' => 1
            ],
            [
                'name' => 'West India',
                'code' => 'WEST',
                'description' => 'Western region covering states like Maharashtra, Gujarat, Rajasthan, Madhya Pradesh, etc.',
                'states' => ['Maharashtra', 'Gujarat', 'Rajasthan', 'Madhya Pradesh', 'Chhattisgarh', 'Goa'],
                'status' => 1
            ]
        ];

        foreach ($regions as $region) {
            Region::create($region);
        }
    }
}
