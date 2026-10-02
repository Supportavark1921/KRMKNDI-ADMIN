<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Pincode;
use App\Models\State;
use Illuminate\Database\Seeder;

/**
 * Real India Post PIN codes for seeded cities.
 * Source: India Post official directory (indiapost.gov.in).
 */
class IndiaPincodeSeeder extends Seeder
{
    public function run(): void
    {
        $india = Country::where('iso_code', 'IN')->firstOrFail();

        // [state_name, district_name, city_name, pincode, post_office_name, office_type]
        $records = [
            // ── Madhya Pradesh ────────────────────────────────────────────────
            ['Madhya Pradesh', 'Indore', 'Indore', '452001', 'Indore H.O',            'HEAD POST OFFICE'],
            ['Madhya Pradesh', 'Indore', 'Indore', '452002', 'Indore Cloth Market S.O', 'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Indore', 'Indore', '452003', 'Palasia S.O',            'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Indore', 'Indore', '452004', 'Rajendra Nagar S.O',     'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Indore', 'Indore', '452005', 'Vijay Nagar S.O',        'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Indore', 'Indore', '452006', 'Annapurna Road S.O',     'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Indore', 'Indore', '452007', 'Aerodrome Road S.O',     'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Indore', 'Indore', '452008', 'Scheme 54 S.O',          'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Indore', 'Indore', '452009', 'AB Road S.O',            'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Indore', 'Indore', '452010', 'Bhawrasla B.O',          'BRANCH POST OFFICE'],
            ['Madhya Pradesh', 'Indore', 'Indore', '452011', 'Kanadiya Road S.O',      'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Indore', 'Indore', '452012', 'Super Corridor S.O',     'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Indore', 'Indore', '452013', 'MR 10 S.O',              'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Indore', 'Rau',    '453331', 'Rau S.O',                'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Indore', 'Mhow',   '453441', 'Mhow H.O',              'HEAD POST OFFICE'],
            ['Madhya Pradesh', 'Indore', 'Sanwer',  '453551', 'Sanwer S.O',            'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Indore', 'Depalpur', '453115', 'Depalpur S.O',          'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Bhopal', 'Bhopal', '462001', 'Bhopal H.O',             'HEAD POST OFFICE'],
            ['Madhya Pradesh', 'Bhopal', 'Bhopal', '462002', 'New Market S.O',         'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Bhopal', 'Bhopal', '462003', 'Arera Colony S.O',       'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Bhopal', 'Bhopal', '462004', 'Shahjahanabad S.O',      'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Bhopal', 'Bhopal', '462011', 'Misrod S.O',             'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Bhopal', 'Bhopal', '462016', 'Kolar Road S.O',         'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Bhopal', 'Bhopal', '462021', 'Bairagarh S.O',          'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Bhopal', 'Bhopal', '462023', 'BHEL Bhopal S.O',        'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Bhopal', 'Bhopal', '462026', 'Karond S.O',             'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Bhopal', 'Mandideep', '462046', 'Mandideep S.O',          'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Ujjain', 'Ujjain', '456001', 'Ujjain H.O',             'HEAD POST OFFICE'],
            ['Madhya Pradesh', 'Ujjain', 'Ujjain', '456006', 'Freeganj S.O',           'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Ujjain', 'Ujjain', '456010', 'Madhav Nagar S.O',       'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Ujjain', 'Nagda',  '456335', 'Nagda S.O',              'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Gwalior', 'Gwalior', '474001', 'Gwalior H.O',             'HEAD POST OFFICE'],
            ['Madhya Pradesh', 'Gwalior', 'Gwalior', '474002', 'Lashkar S.O',             'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Gwalior', 'Gwalior', '474005', 'Morar S.O',               'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Gwalior', 'Gwalior', '474006', 'City Centre S.O',         'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Jabalpur', 'Jabalpur', '482001', 'Jabalpur H.O',           'HEAD POST OFFICE'],
            ['Madhya Pradesh', 'Jabalpur', 'Jabalpur', '482002', 'Wright Town S.O',        'SUB POST OFFICE'],
            ['Madhya Pradesh', 'Dewas',  'Dewas',  '455001', 'Dewas H.O',              'HEAD POST OFFICE'],
            ['Madhya Pradesh', 'Ratlam', 'Ratlam', '457001', 'Ratlam H.O',             'HEAD POST OFFICE'],
            ['Madhya Pradesh', 'Ratlam', 'Mandsaur', '458001', 'Mandsaur H.O',           'HEAD POST OFFICE'],
            ['Madhya Pradesh', 'Ratlam', 'Neemuch', '458441', 'Neemuch H.O',            'HEAD POST OFFICE'],
            ['Madhya Pradesh', 'Sagar',  'Sagar',  '470001', 'Sagar H.O',              'HEAD POST OFFICE'],
            ['Madhya Pradesh', 'Dhar',   'Dhar',   '454001', 'Dhar H.O',               'HEAD POST OFFICE'],
            ['Madhya Pradesh', 'Hoshangabad', 'Hoshangabad', '461001', 'Hoshangabad H.O',  'HEAD POST OFFICE'],
            ['Madhya Pradesh', 'Hoshangabad', 'Itarsi',    '461111', 'Itarsi H.O',        'HEAD POST OFFICE'],

            // ── Maharashtra ───────────────────────────────────────────────────
            ['Maharashtra', 'Mumbai',  'Mumbai', '400001', 'Mumbai G.P.O',             'HEAD POST OFFICE'],
            ['Maharashtra', 'Mumbai',  'Mumbai', '400002', 'Mandvi S.O',               'SUB POST OFFICE'],
            ['Maharashtra', 'Mumbai',  'Mumbai', '400003', 'Masjid S.O',               'SUB POST OFFICE'],
            ['Maharashtra', 'Mumbai',  'Mumbai', '400004', 'Girgaon S.O',              'SUB POST OFFICE'],
            ['Maharashtra', 'Mumbai',  'Mumbai', '400005', 'Colaba S.O',               'SUB POST OFFICE'],
            ['Maharashtra', 'Mumbai',  'Mumbai', '400006', 'Malabar Hill S.O',         'SUB POST OFFICE'],
            ['Maharashtra', 'Mumbai',  'Mumbai', '400007', 'Grant Road S.O',           'SUB POST OFFICE'],
            ['Maharashtra', 'Mumbai',  'Mumbai', '400008', 'Mumbai Central S.O',       'SUB POST OFFICE'],
            ['Maharashtra', 'Mumbai',  'Dadar',  '400014', 'Dadar S.O',                'SUB POST OFFICE'],
            ['Maharashtra', 'Mumbai',  'Andheri', '400053', 'Andheri S.O',              'SUB POST OFFICE'],
            ['Maharashtra', 'Mumbai',  'Andheri', '400058', 'Andheri East S.O',         'SUB POST OFFICE'],
            ['Maharashtra', 'Mumbai',  'Borivali', '400066', 'Borivali S.O',              'SUB POST OFFICE'],
            ['Maharashtra', 'Mumbai',  'Borivali', '400092', 'Borivali East S.O',         'SUB POST OFFICE'],
            ['Maharashtra', 'Mumbai',  'Malad',  '400064', 'Malad S.O',                'SUB POST OFFICE'],
            ['Maharashtra', 'Mumbai',  'Goregaon', '400063', 'Goregaon S.O',             'SUB POST OFFICE'],
            ['Maharashtra', 'Pune',    'Pune',   '411001', 'Pune H.O',                 'HEAD POST OFFICE'],
            ['Maharashtra', 'Pune',    'Pune',   '411002', 'Kasba Peth S.O',           'SUB POST OFFICE'],
            ['Maharashtra', 'Pune',    'Pune',   '411004', 'Shivajinagar S.O',         'SUB POST OFFICE'],
            ['Maharashtra', 'Pune',    'Pune',   '411005', 'Deccan Gymkhana S.O',      'SUB POST OFFICE'],
            ['Maharashtra', 'Pune',    'Pune',   '411007', 'Aundh S.O',                'SUB POST OFFICE'],
            ['Maharashtra', 'Pune',    'Kothrud', '411038', 'Kothrud S.O',              'SUB POST OFFICE'],
            ['Maharashtra', 'Pune',    'Baner',  '411045', 'Baner S.O',                'SUB POST OFFICE'],
            ['Maharashtra', 'Pune',    'Hadapsar', '411028', 'Hadapsar S.O',             'SUB POST OFFICE'],
            ['Maharashtra', 'Nagpur',  'Nagpur', '440001', 'Nagpur H.O',               'HEAD POST OFFICE'],
            ['Maharashtra', 'Nagpur',  'Nagpur', '440002', 'Nagpur City S.O',          'SUB POST OFFICE'],
            ['Maharashtra', 'Nagpur',  'Nagpur', '440010', 'Civil Lines Nagpur S.O',   'SUB POST OFFICE'],
            ['Maharashtra', 'Thane',   'Thane',  '400601', 'Thane S.O',                'HEAD POST OFFICE'],
            ['Maharashtra', 'Thane',   'Navi Mumbai', '400703', 'Vashi S.O',             'SUB POST OFFICE'],
            ['Maharashtra', 'Thane',   'Navi Mumbai', '400706', 'Nerul S.O',             'SUB POST OFFICE'],
            ['Maharashtra', 'Thane',   'Kalyan', '421301', 'Kalyan H.O',               'HEAD POST OFFICE'],
            ['Maharashtra', 'Nashik',  'Nashik', '422001', 'Nashik H.O',               'HEAD POST OFFICE'],
            ['Maharashtra', 'Nashik',  'Nashik', '422002', 'Panchavati S.O',           'SUB POST OFFICE'],
            ['Maharashtra', 'Aurangabad', 'Aurangabad', '431001', 'Aurangabad H.O',        'HEAD POST OFFICE'],

            // ── Delhi ─────────────────────────────────────────────────────────
            ['Delhi', 'Central Delhi',  'Connaught Place', '110001', 'New Delhi H.O',    'HEAD POST OFFICE'],
            ['Delhi', 'Central Delhi',  'Connaught Place', '110002', 'Parliament Street S.O', 'SUB POST OFFICE'],
            ['Delhi', 'Central Delhi',  'Karol Bagh',     '110005', 'Karol Bagh S.O',   'SUB POST OFFICE'],
            ['Delhi', 'Central Delhi',  'Paharganj',      '110055', 'Paharganj S.O',    'SUB POST OFFICE'],
            ['Delhi', 'South Delhi',    'Saket',          '110017', 'Saket S.O',        'SUB POST OFFICE'],
            ['Delhi', 'South Delhi',    'Hauz Khas',      '110016', 'Hauz Khas S.O',    'SUB POST OFFICE'],
            ['Delhi', 'South Delhi',    'Lajpat Nagar',   '110024', 'Lajpat Nagar S.O', 'SUB POST OFFICE'],
            ['Delhi', 'South Delhi',    'Kalkaji',        '110019', 'Kalkaji S.O',      'SUB POST OFFICE'],
            ['Delhi', 'North Delhi',    'Rohini',         '110085', 'Rohini S.O',       'SUB POST OFFICE'],
            ['Delhi', 'North Delhi',    'Rohini',         '110086', 'Rohini Sector 9 S.O', 'SUB POST OFFICE'],
            ['Delhi', 'North Delhi',    'Pitampura',      '110034', 'Pitampura S.O',    'SUB POST OFFICE'],
            ['Delhi', 'East Delhi',     'Laxmi Nagar',    '110092', 'Laxmi Nagar S.O',  'SUB POST OFFICE'],
            ['Delhi', 'East Delhi',     'Mayur Vihar',    '110091', 'Mayur Vihar S.O',  'SUB POST OFFICE'],
            ['Delhi', 'West Delhi',     'Janakpuri',      '110058', 'Janakpuri S.O',    'SUB POST OFFICE'],
            ['Delhi', 'West Delhi',     'Rajouri Garden', '110027', 'Rajouri Garden S.O', 'SUB POST OFFICE'],
            ['Delhi', 'South West Delhi', 'Dwarka',        '110075', 'Dwarka S.O',       'SUB POST OFFICE'],
            ['Delhi', 'South West Delhi', 'Dwarka',        '110077', 'Dwarka Sector 10 S.O', 'SUB POST OFFICE'],
            ['Delhi', 'South West Delhi', 'Uttam Nagar',   '110059', 'Uttam Nagar S.O',  'SUB POST OFFICE'],

            // ── Gujarat ───────────────────────────────────────────────────────
            ['Gujarat', 'Ahmedabad', 'Ahmedabad', '380001', 'Ahmedabad H.O',           'HEAD POST OFFICE'],
            ['Gujarat', 'Ahmedabad', 'Ahmedabad', '380002', 'Dariapur S.O',            'SUB POST OFFICE'],
            ['Gujarat', 'Ahmedabad', 'Ahmedabad', '380006', 'Ellisbridge S.O',         'SUB POST OFFICE'],
            ['Gujarat', 'Ahmedabad', 'Ahmedabad', '380007', 'Shahibaug S.O',           'SUB POST OFFICE'],
            ['Gujarat', 'Ahmedabad', 'Ahmedabad', '380009', 'Navrangpura S.O',         'SUB POST OFFICE'],
            ['Gujarat', 'Ahmedabad', 'Navrangpura', '380009', 'Navrangpura H.O',         'HEAD POST OFFICE'],
            ['Gujarat', 'Ahmedabad', 'Satellite', '380015', 'Satellite S.O',            'SUB POST OFFICE'],
            ['Gujarat', 'Ahmedabad', 'Bopal',     '380058', 'Bopal S.O',                'SUB POST OFFICE'],
            ['Gujarat', 'Ahmedabad', 'Chandkheda', '382424', 'Chandkheda S.O',           'SUB POST OFFICE'],
            ['Gujarat', 'Surat',     'Surat',     '395001', 'Surat H.O',               'HEAD POST OFFICE'],
            ['Gujarat', 'Surat',     'Surat',     '395002', 'Nanpura S.O',             'SUB POST OFFICE'],
            ['Gujarat', 'Surat',     'Adajan',    '395009', 'Adajan S.O',              'SUB POST OFFICE'],
            ['Gujarat', 'Surat',     'Varachha',  '395006', 'Varachha Road S.O',       'SUB POST OFFICE'],
            ['Gujarat', 'Vadodara',  'Vadodara',  '390001', 'Vadodara H.O',            'HEAD POST OFFICE'],
            ['Gujarat', 'Vadodara',  'Vadodara',  '390005', 'Sayajigunj S.O',          'SUB POST OFFICE'],
            ['Gujarat', 'Vadodara',  'Alkapuri',  '390007', 'Alkapuri S.O',            'SUB POST OFFICE'],
            ['Gujarat', 'Rajkot',    'Rajkot',    '360001', 'Rajkot H.O',              'HEAD POST OFFICE'],
            ['Gujarat', 'Rajkot',    'Rajkot',    '360002', 'Rajkot City S.O',         'SUB POST OFFICE'],

            // ── Karnataka ─────────────────────────────────────────────────────
            ['Karnataka', 'Bangalore Urban', 'Bangalore',      '560001', 'Bangalore H.O',          'HEAD POST OFFICE'],
            ['Karnataka', 'Bangalore Urban', 'Bangalore',      '560002', 'Shivajinagar Bangalore S.O', 'SUB POST OFFICE'],
            ['Karnataka', 'Bangalore Urban', 'Bangalore',      '560003', 'Benson Town S.O',        'SUB POST OFFICE'],
            ['Karnataka', 'Bangalore Urban', 'Indiranagar',    '560038', 'Indiranagar S.O',        'SUB POST OFFICE'],
            ['Karnataka', 'Bangalore Urban', 'Koramangala',    '560034', 'Koramangala S.O',        'SUB POST OFFICE'],
            ['Karnataka', 'Bangalore Urban', 'Koramangala',    '560095', 'Koramangala 8th Block S.O', 'SUB POST OFFICE'],
            ['Karnataka', 'Bangalore Urban', 'Whitefield',     '560066', 'Whitefield S.O',         'SUB POST OFFICE'],
            ['Karnataka', 'Bangalore Urban', 'Electronic City', '560100', 'Electronic City S.O',    'SUB POST OFFICE'],
            ['Karnataka', 'Mysore',          'Mysore',         '570001', 'Mysore H.O',             'HEAD POST OFFICE'],
            ['Karnataka', 'Mysore',          'Mysore',         '570002', 'Mysore City S.O',        'SUB POST OFFICE'],
            ['Karnataka', 'Hubli-Dharwad',   'Hubli',          '580020', 'Hubli H.O',              'HEAD POST OFFICE'],
            ['Karnataka', 'Hubli-Dharwad',   'Dharwad',        '580001', 'Dharwad H.O',            'HEAD POST OFFICE'],
            ['Karnataka', 'Mangalore',       'Mangalore',      '575001', 'Mangalore H.O',          'HEAD POST OFFICE'],
            ['Karnataka', 'Mangalore',       'Mangalore',      '575002', 'Balmatta S.O',           'SUB POST OFFICE'],

            // ── Tamil Nadu ────────────────────────────────────────────────────
            ['Tamil Nadu', 'Chennai', 'Chennai',   '600001', 'Chennai H.O',            'HEAD POST OFFICE'],
            ['Tamil Nadu', 'Chennai', 'Chennai',   '600002', 'Park Town S.O',          'SUB POST OFFICE'],
            ['Tamil Nadu', 'Chennai', 'Chennai',   '600003', 'Triplicane S.O',         'SUB POST OFFICE'],
            ['Tamil Nadu', 'Chennai', 'T. Nagar',  '600017', 'T. Nagar S.O',           'SUB POST OFFICE'],
            ['Tamil Nadu', 'Chennai', 'Adyar',     '600020', 'Adyar S.O',              'SUB POST OFFICE'],
            ['Tamil Nadu', 'Chennai', 'Anna Nagar', '600040', 'Anna Nagar S.O',         'SUB POST OFFICE'],
            ['Tamil Nadu', 'Chennai', 'Velachery', '600042', 'Velachery S.O',          'SUB POST OFFICE'],
            ['Tamil Nadu', 'Coimbatore', 'Coimbatore', '641001', 'Coimbatore H.O',         'HEAD POST OFFICE'],
            ['Tamil Nadu', 'Coimbatore', 'Coimbatore', '641002', 'Coimbatore City S.O',    'SUB POST OFFICE'],
            ['Tamil Nadu', 'Madurai',   'Madurai',  '625001', 'Madurai H.O',           'HEAD POST OFFICE'],
            ['Tamil Nadu', 'Salem',     'Salem',    '636001', 'Salem H.O',             'HEAD POST OFFICE'],

            // ── Rajasthan ─────────────────────────────────────────────────────
            ['Rajasthan', 'Jaipur',  'Jaipur',              '302001', 'Jaipur H.O',    'HEAD POST OFFICE'],
            ['Rajasthan', 'Jaipur',  'Jaipur',              '302002', 'Jaipur City S.O', 'SUB POST OFFICE'],
            ['Rajasthan', 'Jaipur',  'Jaipur',              '302003', 'Civil Lines Jaipur S.O', 'SUB POST OFFICE'],
            ['Rajasthan', 'Jaipur',  'Vaishali Nagar',      '302021', 'Vaishali Nagar S.O', 'SUB POST OFFICE'],
            ['Rajasthan', 'Jaipur',  'Vidhyadhar Nagar',    '302023', 'Vidhyadhar Nagar S.O', 'SUB POST OFFICE'],
            ['Rajasthan', 'Jodhpur', 'Jodhpur',             '342001', 'Jodhpur H.O',   'HEAD POST OFFICE'],
            ['Rajasthan', 'Jodhpur', 'Jodhpur',             '342003', 'Jodhpur City S.O', 'SUB POST OFFICE'],
            ['Rajasthan', 'Udaipur', 'Udaipur',             '313001', 'Udaipur H.O',   'HEAD POST OFFICE'],
            ['Rajasthan', 'Kota',    'Kota',                '324001', 'Kota H.O',      'HEAD POST OFFICE'],
            ['Rajasthan', 'Kota',    'Kota',                '324002', 'Kota City S.O', 'SUB POST OFFICE'],

            // ── Uttar Pradesh ─────────────────────────────────────────────────
            ['Uttar Pradesh', 'Lucknow',   'Lucknow',      '226001', 'Lucknow H.O',    'HEAD POST OFFICE'],
            ['Uttar Pradesh', 'Lucknow',   'Lucknow',      '226002', 'Lucknow City S.O', 'SUB POST OFFICE'],
            ['Uttar Pradesh', 'Lucknow',   'Gomti Nagar',  '226010', 'Gomti Nagar S.O', 'SUB POST OFFICE'],
            ['Uttar Pradesh', 'Lucknow',   'Aliganj',      '226024', 'Aliganj S.O',    'SUB POST OFFICE'],
            ['Uttar Pradesh', 'Lucknow',   'Hazratganj',   '226001', 'Hazratganj S.O', 'SUB POST OFFICE'],
            ['Uttar Pradesh', 'Agra',      'Agra',         '282001', 'Agra H.O',       'HEAD POST OFFICE'],
            ['Uttar Pradesh', 'Agra',      'Agra',         '282002', 'Agra City S.O',  'SUB POST OFFICE'],
            ['Uttar Pradesh', 'Kanpur',    'Kanpur',       '208001', 'Kanpur H.O',     'HEAD POST OFFICE'],
            ['Uttar Pradesh', 'Kanpur',    'Kanpur',       '208002', 'Kanpur City S.O', 'SUB POST OFFICE'],
            ['Uttar Pradesh', 'Varanasi',  'Varanasi',     '221001', 'Varanasi H.O',   'HEAD POST OFFICE'],
            ['Uttar Pradesh', 'Varanasi',  'Varanasi',     '221002', 'Varanasi City S.O', 'SUB POST OFFICE'],
            ['Uttar Pradesh', 'Prayagraj', 'Prayagraj',    '211001', 'Allahabad H.O',  'HEAD POST OFFICE'],
            ['Uttar Pradesh', 'Prayagraj', 'Naini',        '211008', 'Naini S.O',      'SUB POST OFFICE'],
            ['Uttar Pradesh', 'Noida',     'Noida',        '201301', 'Noida H.O',      'HEAD POST OFFICE'],
            ['Uttar Pradesh', 'Noida',     'Noida',        '201304', 'Noida Sector 18 S.O', 'SUB POST OFFICE'],
            ['Uttar Pradesh', 'Noida',     'Greater Noida', '201310', 'Greater Noida S.O', 'SUB POST OFFICE'],
            ['Uttar Pradesh', 'Ghaziabad', 'Ghaziabad',    '201001', 'Ghaziabad H.O',  'HEAD POST OFFICE'],
            ['Uttar Pradesh', 'Ghaziabad', 'Ghaziabad',    '201002', 'Ghaziabad City S.O', 'SUB POST OFFICE'],

            // ── West Bengal ───────────────────────────────────────────────────
            ['West Bengal', 'Kolkata', 'Kolkata', '700001', 'Kolkata H.O',             'HEAD POST OFFICE'],
            ['West Bengal', 'Kolkata', 'Kolkata', '700002', 'Kolkata City S.O',        'SUB POST OFFICE'],
            ['West Bengal', 'Kolkata', 'Kolkata', '700012', 'Shyambazar S.O',          'SUB POST OFFICE'],
            ['West Bengal', 'Kolkata', 'Kolkata', '700013', 'Ultadanga S.O',           'SUB POST OFFICE'],
            ['West Bengal', 'Kolkata', 'Salt Lake', '700091', 'Salt Lake S.O',            'SUB POST OFFICE'],
            ['West Bengal', 'Kolkata', 'Salt Lake', '700064', 'Salt Lake Sector 1 S.O',  'SUB POST OFFICE'],
            ['West Bengal', 'Kolkata', 'New Town', '700156', 'New Town S.O',            'SUB POST OFFICE'],
            ['West Bengal', 'Kolkata', 'Howrah',   '711101', 'Howrah H.O',              'HEAD POST OFFICE'],
            ['West Bengal', 'Kolkata', 'Dum Dum',  '700028', 'Dum Dum S.O',             'SUB POST OFFICE'],
            ['West Bengal', 'North 24 Parganas', 'Barasat', '700124', 'Barasat H.O',      'HEAD POST OFFICE'],
            ['West Bengal', 'Darjeeling', 'Darjeeling', '734101', 'Darjeeling H.O',        'HEAD POST OFFICE'],
            ['West Bengal', 'Darjeeling', 'Siliguri', '734001', 'Siliguri H.O',           'HEAD POST OFFICE'],
            ['West Bengal', 'Darjeeling', 'Siliguri', '734004', 'Siliguri City S.O',      'SUB POST OFFICE'],

            // ── Telangana ─────────────────────────────────────────────────────
            ['Telangana', 'Hyderabad',  'Hyderabad',   '500001', 'Hyderabad H.O',      'HEAD POST OFFICE'],
            ['Telangana', 'Hyderabad',  'Hyderabad',   '500002', 'Abids S.O',          'SUB POST OFFICE'],
            ['Telangana', 'Hyderabad',  'Hyderabad',   '500003', 'Koti S.O',           'SUB POST OFFICE'],
            ['Telangana', 'Hyderabad',  'Secunderabad', '500003', 'Secunderabad H.O',   'HEAD POST OFFICE'],
            ['Telangana', 'Hyderabad',  'Kukatpally',  '500072', 'Kukatpally S.O',     'SUB POST OFFICE'],
            ['Telangana', 'Hyderabad',  'Madhapur',    '500081', 'Madhapur S.O',       'SUB POST OFFICE'],
            ['Telangana', 'Hyderabad',  'Gachibowli',  '500032', 'Gachibowli S.O',     'SUB POST OFFICE'],
            ['Telangana', 'Rangareddy', 'LB Nagar',    '500074', 'LB Nagar S.O',       'SUB POST OFFICE'],
            ['Telangana', 'Rangareddy', 'Shamshabad',  '501218', 'Shamshabad S.O',     'SUB POST OFFICE'],
            ['Telangana', 'Warangal Urban', 'Warangal', '506001', 'Warangal H.O',       'HEAD POST OFFICE'],
            ['Telangana', 'Warangal Urban', 'Hanamkonda', '506001', 'Hanamkonda S.O',      'SUB POST OFFICE'],

            // ── Kerala ────────────────────────────────────────────────────────
            ['Kerala', 'Ernakulam',         'Kochi',               '682001', 'Kochi H.O',            'HEAD POST OFFICE'],
            ['Kerala', 'Ernakulam',         'Kochi',               '682002', 'Ernakulam S.O',        'SUB POST OFFICE'],
            ['Kerala', 'Ernakulam',         'Kochi',               '682016', 'Edapally S.O',         'SUB POST OFFICE'],
            ['Kerala', 'Ernakulam',         'Aluva',               '683101', 'Aluva S.O',            'SUB POST OFFICE'],
            ['Kerala', 'Thiruvananthapuram', 'Thiruvananthapuram',  '695001', 'Thiruvananthapuram H.O', 'HEAD POST OFFICE'],
            ['Kerala', 'Thiruvananthapuram', 'Thiruvananthapuram',  '695002', 'Palayam S.O',          'SUB POST OFFICE'],
            ['Kerala', 'Kozhikode',         'Kozhikode',           '673001', 'Kozhikode H.O',        'HEAD POST OFFICE'],
            ['Kerala', 'Kozhikode',         'Kozhikode',           '673002', 'Kozhikode City S.O',   'SUB POST OFFICE'],

            // ── Punjab ────────────────────────────────────────────────────────
            ['Punjab', 'Ludhiana',   'Ludhiana',  '141001', 'Ludhiana H.O',            'HEAD POST OFFICE'],
            ['Punjab', 'Ludhiana',   'Ludhiana',  '141002', 'Ludhiana City S.O',       'SUB POST OFFICE'],
            ['Punjab', 'Amritsar',   'Amritsar',  '143001', 'Amritsar H.O',            'HEAD POST OFFICE'],
            ['Punjab', 'Amritsar',   'Amritsar',  '143002', 'Amritsar City S.O',       'SUB POST OFFICE'],
            ['Punjab', 'Jalandhar',  'Jalandhar', '144001', 'Jalandhar H.O',           'HEAD POST OFFICE'],
            ['Punjab', 'Jalandhar',  'Jalandhar', '144002', 'Jalandhar City S.O',      'SUB POST OFFICE'],
            ['Punjab', 'Chandigarh', 'Sector 17', '160017', 'Chandigarh H.O',          'HEAD POST OFFICE'],
            ['Punjab', 'Chandigarh', 'Sector 22', '160022', 'Sector 22 Chandigarh S.O', 'SUB POST OFFICE'],
            ['Punjab', 'Chandigarh', 'Sector 35', '160035', 'Sector 35 Chandigarh S.O', 'SUB POST OFFICE'],
        ];

        foreach ($records as [$stateName, $districtName, $cityName, $pincode, $officeName, $officeType]) {
            $state = State::whereHas('country', fn ($q) => $q->where('iso_code', 'IN'))
                ->where('name', $stateName)
                ->first();

            if (! $state) {
                continue;
            }

            $district = District::firstOrCreate(
                ['state_id' => $state->id, 'name' => $districtName],
                ['status' => 'active']
            );

            $city = City::firstOrCreate(
                ['district_id' => $district->id, 'name' => $cityName],
                ['state_id' => $state->id, 'status' => 'active']
            );

            Pincode::firstOrCreate(
                [
                    'pincode' => $pincode,
                    'post_office_name' => $officeName,
                    'state_id' => $state->id,
                ],
                [
                    'country_id' => $india->id,
                    'district_id' => $district->id,
                    'city_id' => $city->id,
                    'office_type' => $officeType,
                    'delivery_status' => 'Delivery',
                    'status' => 'active',
                ]
            );
        }
    }
}
