<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\State;
use Illuminate\Database\Seeder;

/**
 * Sample districts and cities for key states.
 * Replace with the full India Post import for production data.
 */
class IndiaSampleDistrictCitySeeder extends Seeder
{
    public function run(): void
    {
        $india = Country::where('iso_code', 'IN')->firstOrFail();

        $data = [
            'Madhya Pradesh' => [
                'Indore' => ['Indore', 'Rau', 'Mhow', 'Sanwer', 'Depalpur', 'Hatod'],
                'Bhopal' => ['Bhopal', 'Berasia', 'Sehore', 'Mandideep'],
                'Ujjain' => ['Ujjain', 'Nagda', 'Tarana', 'Mahidpur'],
                'Gwalior' => ['Gwalior', 'Morar', 'Lashkar', 'Bhind'],
                'Jabalpur' => ['Jabalpur', 'Katni', 'Narsinghpur', 'Mandla'],
                'Dewas' => ['Dewas', 'Sonkatch', 'Khategaon'],
                'Ratlam' => ['Ratlam', 'Mandsaur', 'Neemuch', 'Jaora'],
                'Sagar' => ['Sagar', 'Damoh', 'Rahatgarh'],
                'Dhar' => ['Dhar', 'Manawar', 'Kukshi', 'Badnawar'],
                'Hoshangabad' => ['Hoshangabad', 'Itarsi', 'Pipariya'],
            ],
            'Maharashtra' => [
                'Mumbai' => ['Mumbai', 'Andheri', 'Borivali', 'Malad', 'Goregaon', 'Dadar'],
                'Pune' => ['Pune', 'Pimpri-Chinchwad', 'Hadapsar', 'Kothrud', 'Baner'],
                'Nagpur' => ['Nagpur', 'Kamptee', 'Wardha', 'Yavatmal'],
                'Thane' => ['Thane', 'Navi Mumbai', 'Kalyan', 'Ulhasnagar'],
                'Nashik' => ['Nashik', 'Malegaon', 'Ozar'],
                'Aurangabad' => ['Aurangabad', 'Jalna', 'Parbhani'],
            ],
            'Delhi' => [
                'Central Delhi' => ['Connaught Place', 'Karol Bagh', 'Paharganj'],
                'South Delhi' => ['Saket', 'Hauz Khas', 'Lajpat Nagar', 'Kalkaji'],
                'North Delhi' => ['Rohini', 'Pitampura', 'Model Town'],
                'East Delhi' => ['Laxmi Nagar', 'Preet Vihar', 'Mayur Vihar'],
                'West Delhi' => ['Janakpuri', 'Rajouri Garden', 'Tilak Nagar'],
                'South West Delhi' => ['Dwarka', 'Uttam Nagar', 'Palam'],
            ],
            'Gujarat' => [
                'Ahmedabad' => ['Ahmedabad', 'Navrangpura', 'Satellite', 'Bopal', 'Chandkheda'],
                'Surat' => ['Surat', 'Adajan', 'Katargam', 'Varachha'],
                'Vadodara' => ['Vadodara', 'Alkapuri', 'Gotri'],
                'Rajkot' => ['Rajkot', 'Gondal', 'Jetpur'],
            ],
            'Karnataka' => [
                'Bangalore Urban' => ['Bangalore', 'Whitefield', 'Electronic City', 'Indiranagar', 'Koramangala'],
                'Mysore' => ['Mysore', 'Mandya', 'Chamarajanagar'],
                'Hubli-Dharwad' => ['Hubli', 'Dharwad'],
                'Mangalore' => ['Mangalore', 'Udupi'],
            ],
            'Tamil Nadu' => [
                'Chennai' => ['Chennai', 'Anna Nagar', 'T. Nagar', 'Adyar', 'Velachery'],
                'Coimbatore' => ['Coimbatore', 'Tiruppur', 'Pollachi'],
                'Madurai' => ['Madurai', 'Dindigul', 'Sivakasi'],
                'Salem' => ['Salem', 'Namakkal', 'Erode'],
            ],
            'Rajasthan' => [
                'Jaipur' => ['Jaipur', 'Sanganer', 'Vidhyadhar Nagar', 'Vaishali Nagar'],
                'Jodhpur' => ['Jodhpur', 'Pali', 'Barmer'],
                'Udaipur' => ['Udaipur', 'Chittorgarh', 'Bhilwara'],
                'Kota' => ['Kota', 'Bundi', 'Baran'],
            ],
            'Uttar Pradesh' => [
                'Lucknow' => ['Lucknow', 'Gomti Nagar', 'Aliganj', 'Hazratganj'],
                'Agra' => ['Agra', 'Firozabad', 'Mathura'],
                'Kanpur' => ['Kanpur', 'Unnao', 'Fatehpur'],
                'Varanasi' => ['Varanasi', 'Mirzapur', 'Bhadohi'],
                'Prayagraj' => ['Prayagraj', 'Naini', 'Jhunsi'],
                'Noida' => ['Noida', 'Greater Noida', 'Dadri'],
                'Ghaziabad' => ['Ghaziabad', 'Loni', 'Modinagar'],
            ],
            'West Bengal' => [
                'Kolkata' => ['Kolkata', 'Howrah', 'Salt Lake', 'New Town', 'Dum Dum'],
                'North 24 Parganas' => ['Barasat', 'Kalyani', 'Naihati'],
                'Darjeeling' => ['Darjeeling', 'Siliguri', 'Kurseong'],
            ],
            'Telangana' => [
                'Hyderabad' => ['Hyderabad', 'Secunderabad', 'Kukatpally', 'Madhapur', 'Gachibowli'],
                'Rangareddy' => ['Rangareddy', 'LB Nagar', 'Shamshabad'],
                'Warangal Urban' => ['Warangal', 'Hanamkonda'],
            ],
            'Kerala' => [
                'Ernakulam' => ['Kochi', 'Aluva', 'Perumbavoor'],
                'Thiruvananthapuram' => ['Thiruvananthapuram', 'Attingal', 'Neyyattinkara'],
                'Kozhikode' => ['Kozhikode', 'Vatakara', 'Malappuram'],
            ],
            'Punjab' => [
                'Ludhiana' => ['Ludhiana', 'Khanna', 'Samrala'],
                'Amritsar' => ['Amritsar', 'Tarn Taran'],
                'Jalandhar' => ['Jalandhar', 'Nakodar', 'Phagwara'],
                'Chandigarh' => ['Sector 17', 'Sector 22', 'Sector 35', 'Industrial Area'],
            ],
        ];

        foreach ($data as $stateName => $districts) {
            $state = State::where('country_id', $india->id)
                ->where('name', $stateName)
                ->first();

            if (! $state) {
                // Try partial match (e.g. "Chandigarh" UT)
                $state = State::where('country_id', $india->id)
                    ->where('name', 'like', "%{$stateName}%")
                    ->first();
            }

            if (! $state) {
                continue;
            }

            foreach ($districts as $districtName => $cities) {
                $district = District::firstOrCreate(
                    ['state_id' => $state->id, 'name' => $districtName],
                    ['status' => 'active']
                );

                foreach ($cities as $cityName) {
                    City::firstOrCreate(
                        ['district_id' => $district->id, 'name' => $cityName],
                        ['state_id' => $state->id, 'status' => 'active']
                    );
                }
            }
        }
    }
}
