<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;

class IndiaLocationSeeder extends Seeder
{
    public function run(): void
    {
        $india = Country::firstOrCreate(
            ['iso_code' => 'IN'],
            [
                'name'          => 'India',
                'phone_code'    => '+91',
                'currency_code' => 'INR',
                'status'        => 'active',
            ]
        );

        $states = [
            // States
            ['name' => 'Andhra Pradesh',       'code' => 'AP',  'type' => State::TYPE_STATE],
            ['name' => 'Arunachal Pradesh',     'code' => 'AR',  'type' => State::TYPE_STATE],
            ['name' => 'Assam',                 'code' => 'AS',  'type' => State::TYPE_STATE],
            ['name' => 'Bihar',                 'code' => 'BR',  'type' => State::TYPE_STATE],
            ['name' => 'Chhattisgarh',          'code' => 'CG',  'type' => State::TYPE_STATE],
            ['name' => 'Goa',                   'code' => 'GA',  'type' => State::TYPE_STATE],
            ['name' => 'Gujarat',               'code' => 'GJ',  'type' => State::TYPE_STATE],
            ['name' => 'Haryana',               'code' => 'HR',  'type' => State::TYPE_STATE],
            ['name' => 'Himachal Pradesh',      'code' => 'HP',  'type' => State::TYPE_STATE],
            ['name' => 'Jharkhand',             'code' => 'JH',  'type' => State::TYPE_STATE],
            ['name' => 'Karnataka',             'code' => 'KA',  'type' => State::TYPE_STATE],
            ['name' => 'Kerala',                'code' => 'KL',  'type' => State::TYPE_STATE],
            ['name' => 'Madhya Pradesh',        'code' => 'MP',  'type' => State::TYPE_STATE],
            ['name' => 'Maharashtra',           'code' => 'MH',  'type' => State::TYPE_STATE],
            ['name' => 'Manipur',               'code' => 'MN',  'type' => State::TYPE_STATE],
            ['name' => 'Meghalaya',             'code' => 'ML',  'type' => State::TYPE_STATE],
            ['name' => 'Mizoram',               'code' => 'MZ',  'type' => State::TYPE_STATE],
            ['name' => 'Nagaland',              'code' => 'NL',  'type' => State::TYPE_STATE],
            ['name' => 'Odisha',                'code' => 'OD',  'type' => State::TYPE_STATE],
            ['name' => 'Punjab',                'code' => 'PB',  'type' => State::TYPE_STATE],
            ['name' => 'Rajasthan',             'code' => 'RJ',  'type' => State::TYPE_STATE],
            ['name' => 'Sikkim',                'code' => 'SK',  'type' => State::TYPE_STATE],
            ['name' => 'Tamil Nadu',            'code' => 'TN',  'type' => State::TYPE_STATE],
            ['name' => 'Telangana',             'code' => 'TS',  'type' => State::TYPE_STATE],
            ['name' => 'Tripura',               'code' => 'TR',  'type' => State::TYPE_STATE],
            ['name' => 'Uttar Pradesh',         'code' => 'UP',  'type' => State::TYPE_STATE],
            ['name' => 'Uttarakhand',           'code' => 'UK',  'type' => State::TYPE_STATE],
            ['name' => 'West Bengal',           'code' => 'WB',  'type' => State::TYPE_STATE],
            // Union Territories
            ['name' => 'Andaman and Nicobar Islands',                    'code' => 'AN',  'type' => State::TYPE_UT],
            ['name' => 'Chandigarh',                                     'code' => 'CH',  'type' => State::TYPE_UT],
            ['name' => 'Dadra and Nagar Haveli and Daman and Diu',       'code' => 'DH',  'type' => State::TYPE_UT],
            ['name' => 'Delhi',                                          'code' => 'DL',  'type' => State::TYPE_UT],
            ['name' => 'Jammu and Kashmir',                              'code' => 'JK',  'type' => State::TYPE_UT],
            ['name' => 'Ladakh',                                         'code' => 'LA',  'type' => State::TYPE_UT],
            ['name' => 'Lakshadweep',                                    'code' => 'LD',  'type' => State::TYPE_UT],
            ['name' => 'Puducherry',                                     'code' => 'PY',  'type' => State::TYPE_UT],
        ];

        foreach ($states as $row) {
            State::firstOrCreate(
                ['country_id' => $india->id, 'code' => $row['code']],
                ['name' => $row['name'], 'type' => $row['type'], 'status' => 'active']
            );
        }
    }
}
