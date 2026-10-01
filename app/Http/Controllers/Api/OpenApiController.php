<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OpenApiController extends Controller
{
    /** GET /api/openapi.json */
    public function spec(Request $request): JsonResponse
    {
        $base = rtrim(request()->getSchemeAndHttpHost(), '/');

        $spec = [
            'openapi' => '3.0.3',
            'info' => [
                'title'       => 'ARK Jyotish API',
                'description' => "REST API consumed by the ARK Jyotish mobile app.\n\n"
                    . "**Base URL:** `{$base}/api`\n\n"
                    . "All responses are JSON. Language-specific fields fall back to English when the requested translation is unavailable.\n\n"
                    . "**Modules:**\n"
                    . "- 🕉 **Gurujis** — list and fetch Guruji profiles with donation categories\n"
                    . "- ₹ **Donations** — fee config and create donation transactions\n"
                    . "- ✦ **Services** — multilingual service catalogue\n"
                    . "- 🌙 **Panchang** — daily Vedic Panchang data (Tithi, Nakshatra, Yoga, Karana, Choghadiya, Hora, Rahu Kaal, Abhijit) via Navamsha API with location-aware caching",
                'version' => '1.0.0',
                'contact' => ['name' => 'ARK Jyotish Admin', 'url' => $base],
            ],
            'servers' => [
                ['url' => $base, 'description' => 'Current server (' . $base . ')'],
            ],
            'tags' => [
                ['name' => 'Gurujis',   'description' => 'Guruji profiles, donation categories, and per-Guruji stats'],
                ['name' => 'Donations', 'description' => 'App handling fee configuration and donation transactions'],
                ['name' => 'Services',  'description' => 'Multilingual booking services'],
                ['name' => 'Location',  'description' => 'Cascading location master — countries, states, districts, cities, PIN codes'],
                ['name' => 'Panchang',  'description' => 'Daily Vedic Panchang data — Tithi, Nakshatra, Yoga, Karana, Choghadiya, Hora, Rahu Kaal, Abhijit. Served from cache; Navamsha API key is server-side only.'],
            ],

            // ── Paths ────────────────────────────────────────────────────────
            'paths' => [

                // ── GURUJIS ──────────────────────────────────────────────────

                '/api/gurus' => [
                    'get' => [
                        'tags'        => ['Gurujis'],
                        'summary'     => 'List all active Gurujis',
                        'description' => 'Returns all Gurujis with status = active. Used by the APK to populate the Guruji selection screen.',
                        'operationId' => 'listGurus',
                        'responses'   => [
                            '200' => [
                                'description' => 'Success',
                                'content'     => [
                                    'application/json' => [
                                        'schema'  => ['$ref' => '#/components/schemas/GuruListResponse'],
                                        'example' => [
                                            'data' => [
                                                ['id' => 1, 'name' => 'Shri XYZ Maharaj', 'description' => 'Renowned spiritual guide.', 'image' => null, 'status' => 'active'],
                                                ['id' => 2, 'name' => 'Shri ABC Swami',   'description' => 'Vedic scholar.',            'image' => null, 'status' => 'active'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                '/api/gurus/{id}' => [
                    'get' => [
                        'tags'        => ['Gurujis'],
                        'summary'     => 'Get a single Guruji with stats',
                        'operationId' => 'getGuru',
                        'parameters'  => [
                            ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 1],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Success',
                                'content'     => [
                                    'application/json' => [
                                        'schema'  => ['$ref' => '#/components/schemas/GuruSingleResponse'],
                                        'example' => [
                                            'data' => [
                                                'id' => 1, 'name' => 'Shri XYZ Maharaj', 'description' => '...', 'image' => null, 'status' => 'active',
                                                'stats' => ['total_donations' => 52500.00, 'total_donors' => 120, 'total_transactions' => 145],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            '404' => ['$ref' => '#/components/responses/NotFound'],
                        ],
                    ],
                ],

                '/api/gurus/{id}/donation-categories' => [
                    'get' => [
                        'tags'        => ['Gurujis'],
                        'summary'     => 'Get donation categories for a Guruji',
                        'description' => "Returns the Guruji's active donation categories together with the current fee config. "
                            . "Use this endpoint to populate the APK donation screen — it provides everything needed to build the payment breakdown.",
                        'operationId' => 'getGuruCategories',
                        'parameters'  => [
                            ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 1],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Success',
                                'content'     => [
                                    'application/json' => [
                                        'schema'  => ['$ref' => '#/components/schemas/GuruCategoriesResponse'],
                                        'example' => [
                                            'data' => [
                                                'guru'       => ['id' => 1, 'name' => 'Shri XYZ Maharaj', 'image' => null, 'status' => 'active'],
                                                'categories' => [
                                                    ['id' => 1, 'name' => 'Ann Prasadhan', 'description' => 'Food donation.', 'image' => null, 'status' => 'active'],
                                                    ['id' => 2, 'name' => 'Gau Seva',      'description' => 'Cow service.',   'image' => null, 'status' => 'active'],
                                                ],
                                                'fee_config' => ['handling_charge' => 1.00, 'gst_rate' => 18.00, 'gst_label' => 'GST @ 18%'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            '404' => ['$ref' => '#/components/responses/NotFound'],
                        ],
                    ],
                ],

                // ── DONATIONS ────────────────────────────────────────────────

                '/api/donation/fee-config' => [
                    'get' => [
                        'tags'        => ['Donations'],
                        'summary'     => 'Get current app handling fee configuration',
                        'description' => "Returns the active handling charge and GST rate, plus a live example on ₹500. "
                            . "The APK **must** use this to calculate and display the breakdown before payment — never hardcode the fee.",
                        'operationId' => 'getDonationFeeConfig',
                        'responses'   => [
                            '200' => [
                                'description' => 'Success',
                                'content'     => [
                                    'application/json' => [
                                        'schema'  => ['$ref' => '#/components/schemas/FeeConfigResponse'],
                                        'example' => [
                                            'data' => [
                                                'handling_charge' => 1.00,
                                                'gst_rate'        => 18.00,
                                                'gst_label'       => 'GST @ 18%',
                                                'example_on_500'  => [
                                                    'donation_amount' => 500.00,
                                                    'handling_charge' => 1.00,
                                                    'gst_rate'        => 18.00,
                                                    'gst_amount'      => 0.18,
                                                    'total_amount'    => 501.18,
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                '/api/donations' => [
                    'post' => [
                        'tags'        => ['Donations'],
                        'summary'     => 'Create a donation transaction',
                        'description' => "Creates a new donation. The server **always recalculates** handling_charge, gst_amount, and total_amount "
                            . "from the active fee config — never trust a client-submitted total.\n\n"
                            . "**Idempotency:** if `transaction_id` is supplied and already exists, the existing record is returned with `created: false`.",
                        'operationId' => 'createDonation',
                        'requestBody' => [
                            'required' => true,
                            'content'  => [
                                'application/json' => [
                                    'schema'  => ['$ref' => '#/components/schemas/CreateDonationRequest'],
                                    'example' => [
                                        'guru_id'         => 1,
                                        'category_id'     => 1,
                                        'donation_amount' => 500,
                                        'user_id'         => 1,
                                        'payment_status'  => 'success',
                                        'payment_id'      => 'pay_Abc123XYZ',
                                        'transaction_id'  => 'TXN20260930001',
                                        'payment_method'  => 'UPI',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '201' => [
                                'description' => 'Donation created',
                                'content'     => [
                                    'application/json' => [
                                        'schema'  => ['$ref' => '#/components/schemas/DonationResponse'],
                                        'example' => [
                                            'data' => [
                                                'id'             => 1,
                                                'donation_id'    => 'DON-00001',
                                                'guru'           => ['id' => 1, 'name' => 'Shri XYZ Maharaj'],
                                                'category'       => ['id' => 1, 'name' => 'Ann Prasadhan'],
                                                'breakdown'      => [
                                                    'donation_amount' => 500.00,
                                                    'handling_charge' => 1.00,
                                                    'gst_rate'        => 18.00,
                                                    'gst_amount'      => 0.18,
                                                    'total_amount'    => 501.18,
                                                    'currency'        => 'INR',
                                                ],
                                                'payment_status' => 'success',
                                                'transaction_id' => 'TXN20260930001',
                                                'payment_method' => 'UPI',
                                                'created_at'     => '2026-09-30T10:30:00.000000Z',
                                            ],
                                            'created' => true,
                                        ],
                                    ],
                                ],
                            ],
                            '200' => [
                                'description' => 'Existing donation returned (idempotent — same transaction_id)',
                                'content'     => [
                                    'application/json' => [
                                        'example' => ['data' => ['donation_id' => 'DON-00001', '...' => '...'], 'created' => false],
                                    ],
                                ],
                            ],
                            '422' => ['$ref' => '#/components/responses/ValidationError'],
                        ],
                    ],
                ],

                // ── SERVICES ─────────────────────────────────────────────────

                '/api/services' => [
                    'get' => [
                        'tags'        => ['Services'],
                        'summary'     => 'List all services',
                        'description' => 'Returns services with translatable fields resolved for the requested language. Falls back to English.',
                        'operationId' => 'listServices',
                        'parameters'  => [
                            [
                                'name' => 'language', 'in' => 'query', 'required' => false,
                                'description' => 'BCP-47 language code',
                                'schema' => ['type' => 'string', 'enum' => ['en','hi','mr','gu','ta','te','bn'], 'default' => 'en'],
                                'example' => 'hi',
                            ],
                            [
                                'name' => 'status', 'in' => 'query', 'required' => false,
                                'schema' => ['type' => 'string', 'enum' => ['active','inactive','all'], 'default' => 'active'],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Success',
                                'content'     => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ServiceListResponse']]],
                            ],
                        ],
                    ],
                ],

                '/api/services/{id}' => [
                    'get' => [
                        'tags'        => ['Services'],
                        'summary'     => 'Get a single service',
                        'operationId' => 'getService',
                        'parameters'  => [
                            ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 1],
                            [
                                'name' => 'language', 'in' => 'query', 'required' => false,
                                'schema' => ['type' => 'string', 'enum' => ['en','hi','mr','gu','ta','te','bn'], 'default' => 'en'],
                            ],
                        ],
                        'responses' => [
                            '200'  => ['description' => 'Success',       'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ServiceSingleResponse']]]],
                            '404'  => ['$ref' => '#/components/responses/NotFound'],
                        ],
                    ],
                ],
                // ── LOCATION ─────────────────────────────────────────────────

                '/api/locations/countries' => [
                    'get' => [
                        'tags'        => ['Location'],
                        'summary'     => 'List all active countries',
                        'operationId' => 'listCountries',
                        'responses'   => [
                            '200' => [
                                'description' => 'Success',
                                'content'     => [
                                    'application/json' => [
                                        'example' => [
                                            ['id' => 1, 'name' => 'India', 'iso_code' => 'IN', 'phone_code' => '+91', 'currency_code' => 'INR'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                '/api/locations/countries/{country_id}/states' => [
                    'get' => [
                        'tags'        => ['Location'],
                        'summary'     => 'List states / UTs for a country',
                        'operationId' => 'listStatesByCountryId',
                        'parameters'  => [
                            ['name' => 'country_id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 1],
                        ],
                        'responses'   => [
                            '200' => [
                                'description' => 'Success',
                                'content'     => [
                                    'application/json' => [
                                        'example' => [
                                            ['id' => 13, 'name' => 'Madhya Pradesh', 'code' => 'MP', 'type' => 'STATE'],
                                            ['id' => 32, 'name' => 'Delhi',           'code' => 'DL', 'type' => 'UNION_TERRITORY'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                '/api/locations/countries/iso/{iso}/states' => [
                    'get' => [
                        'tags'        => ['Location'],
                        'summary'     => 'List states / UTs by country ISO code',
                        'operationId' => 'listStatesByIso',
                        'parameters'  => [
                            ['name' => 'iso', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string', 'maxLength' => 2], 'example' => 'IN'],
                        ],
                        'responses'   => ['200' => ['description' => 'Success']],
                    ],
                ],

                '/api/locations/states/{state_id}/districts' => [
                    'get' => [
                        'tags'        => ['Location'],
                        'summary'     => 'List districts for a state',
                        'operationId' => 'listDistricts',
                        'parameters'  => [
                            ['name' => 'state_id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 13],
                        ],
                        'responses'   => [
                            '200' => [
                                'description' => 'Success',
                                'content'     => [
                                    'application/json' => [
                                        'example' => [
                                            ['id' => 1, 'state_id' => 13, 'name' => 'Indore', 'code' => null],
                                            ['id' => 2, 'state_id' => 13, 'name' => 'Bhopal', 'code' => null],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                '/api/locations/districts/{district_id}/cities' => [
                    'get' => [
                        'tags'        => ['Location'],
                        'summary'     => 'List cities for a district',
                        'operationId' => 'listCities',
                        'parameters'  => [
                            ['name' => 'district_id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 1],
                        ],
                        'responses'   => [
                            '200' => [
                                'description' => 'Success',
                                'content'     => [
                                    'application/json' => [
                                        'example' => [
                                            ['id' => 1, 'district_id' => 1, 'state_id' => 13, 'name' => 'Indore'],
                                            ['id' => 2, 'district_id' => 1, 'state_id' => 13, 'name' => 'Rau'],
                                            ['id' => 3, 'district_id' => 1, 'state_id' => 13, 'name' => 'Mhow'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                '/api/locations/cities/{city_id}/pincodes' => [
                    'get' => [
                        'tags'        => ['Location'],
                        'summary'     => 'List PIN codes (post offices) for a city',
                        'description' => 'One city can have multiple post offices and multiple PIN codes. Returns all active entries.',
                        'operationId' => 'listPincodes',
                        'parameters'  => [
                            ['name' => 'city_id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 1],
                        ],
                        'responses'   => [
                            '200' => [
                                'description' => 'Success',
                                'content'     => [
                                    'application/json' => [
                                        'example' => [
                                            ['id' => 1,  'pincode' => '452001', 'post_office_name' => 'Indore H.O',           'office_type' => 'HEAD POST OFFICE',  'delivery_status' => 'Delivery'],
                                            ['id' => 2,  'pincode' => '452002', 'post_office_name' => 'Indore Cloth Market S.O','office_type' => 'SUB POST OFFICE', 'delivery_status' => 'Delivery'],
                                            ['id' => 3,  'pincode' => '452003', 'post_office_name' => 'Palasia S.O',            'office_type' => 'SUB POST OFFICE', 'delivery_status' => 'Delivery'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                '/api/locations/pincodes/{pincode}' => [
                    'get' => [
                        'tags'        => ['Location'],
                        'summary'     => 'Reverse PIN code lookup',
                        'description' => 'Returns all post offices matching the 6-digit PIN, each with its state, district and city. Use this to auto-fill an address form when the user enters a PIN.',
                        'operationId' => 'lookupPincode',
                        'parameters'  => [
                            ['name' => 'pincode', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string', 'pattern' => '^\d{6}$'], 'example' => '452001'],
                        ],
                        'responses'   => [
                            '200' => [
                                'description' => 'One or more post offices matching this PIN',
                                'content'     => [
                                    'application/json' => [
                                        'example' => [
                                            [
                                                'id' => 1, 'pincode' => '452001', 'post_office_name' => 'Indore H.O', 'office_type' => 'HEAD POST OFFICE',
                                                'state'    => ['id' => 13, 'name' => 'Madhya Pradesh', 'code' => 'MP'],
                                                'district' => ['id' => 1,  'name' => 'Indore'],
                                                'city'     => ['id' => 1,  'name' => 'Indore'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            '404' => ['$ref' => '#/components/responses/NotFound'],
                        ],
                    ],
                ],

                '/api/locations/search' => [
                    'get' => [
                        'tags'        => ['Location'],
                        'summary'     => 'Search cities, districts or PIN codes',
                        'description' => 'Type-aware search: a digit string searches PIN codes; text searches cities and districts. Minimum 2 characters.',
                        'operationId' => 'searchLocations',
                        'parameters'  => [
                            [
                                'name'        => 'q',
                                'in'          => 'query',
                                'required'    => true,
                                'description' => 'Search query — text for city/district, digits for PIN prefix',
                                'schema'      => ['type' => 'string', 'minLength' => 2],
                                'example'     => 'indore',
                            ],
                        ],
                        'responses'   => [
                            '200' => [
                                'description' => 'Array of matched results (type = city | district | pincode)',
                                'content'     => [
                                    'application/json' => [
                                        'example' => [
                                            ['type' => 'city', 'id' => 1, 'label' => 'Indore, Indore, Madhya Pradesh', 'city' => 'Indore', 'district' => 'Indore', 'state' => 'Madhya Pradesh', 'state_code' => 'MP'],
                                            ['type' => 'district', 'id' => 1, 'label' => 'Indore, Madhya Pradesh', 'district' => 'Indore', 'state' => 'Madhya Pradesh', 'state_code' => 'MP'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                // ── PANCHANG ─────────────────────────────────────────────────

                '/api/v1/panchang' => [
                    'get' => [
                        'tags'        => ['Panchang'],
                        'summary'     => 'Get daily Vedic Panchang for a location and date',
                        'description' => "Returns full or feature-specific Panchang data for a given location and date.\n\n"
                            . "**Caching:** coordinates are rounded to 2 decimal places (~1 km grid). "
                            . "The first request for a new location+date hits the Navamsha API; subsequent requests are served from the database cache. "
                            . "Cache expires at the end of the local calendar day (historical records are kept permanently).\n\n"
                            . "**No auth required** — this endpoint is called directly from the mobile APK. "
                            . "The Navamsha API key is **never** exposed; it is used only on the server.",
                        'operationId' => 'getPanchang',
                        'parameters'  => [
                            [
                                'name'        => 'latitude',
                                'in'          => 'query',
                                'required'    => true,
                                'description' => 'Latitude of the birth/query location (decimal degrees)',
                                'schema'      => ['type' => 'number', 'format' => 'float', 'minimum' => -90, 'maximum' => 90],
                                'example'     => 22.7196,
                            ],
                            [
                                'name'        => 'longitude',
                                'in'          => 'query',
                                'required'    => true,
                                'description' => 'Longitude of the birth/query location (decimal degrees)',
                                'schema'      => ['type' => 'number', 'format' => 'float', 'minimum' => -180, 'maximum' => 180],
                                'example'     => 75.8577,
                            ],
                            [
                                'name'        => 'timezone',
                                'in'          => 'query',
                                'required'    => true,
                                'description' => 'UTC offset in decimal hours (e.g. 5.5 for IST, -5.0 for EST)',
                                'schema'      => ['type' => 'number', 'format' => 'float', 'minimum' => -14, 'maximum' => 14],
                                'example'     => 5.5,
                            ],
                            [
                                'name'        => 'date',
                                'in'          => 'query',
                                'required'    => false,
                                'description' => 'Calendar date in YYYY-MM-DD format. Defaults to today in the server\'s local timezone.',
                                'schema'      => ['type' => 'string', 'format' => 'date'],
                                'example'     => '2026-10-01',
                            ],
                            [
                                'name'        => 'feature',
                                'in'          => 'query',
                                'required'    => false,
                                'description' => 'Which subset of Panchang data to return. Defaults to `panchang_full`.',
                                'schema'      => [
                                    'type'    => 'string',
                                    'enum'    => ['panchang_full', 'choghadiya', 'hora', 'rahu_kaal', 'sun_times', 'abhijit'],
                                    'default' => 'panchang_full',
                                ],
                                'example' => 'panchang_full',
                            ],
                            [
                                'name'        => 'location',
                                'in'          => 'query',
                                'required'    => false,
                                'description' => 'Human-readable location name stored in the cache record (informational only).',
                                'schema'      => ['type' => 'string', 'maxLength' => 255],
                                'example'     => 'Indore, Madhya Pradesh',
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Panchang data (live from Navamsha API or served from cache)',
                                'content'     => [
                                    'application/json' => [
                                        'schema'  => ['$ref' => '#/components/schemas/PanchangResponse'],
                                        'example' => [
                                            'success' => true,
                                            'cached'  => false,
                                            'date'    => '2026-10-01',
                                            'location' => [
                                                'name'      => 'Indore, Madhya Pradesh',
                                                'latitude'  => 22.72,
                                                'longitude' => 75.86,
                                                'timezone'  => 5.5,
                                            ],
                                            'data' => [
                                                'tithi'    => ['name' => 'Tritiya', 'number' => 3, 'paksha' => 'Shukla', 'end_time' => '14:32:00'],
                                                'nakshatra'=> ['name' => 'Rohini', 'number' => 4, 'end_time' => '18:45:00'],
                                                'yoga'     => ['name' => 'Shobhana', 'number' => 6, 'end_time' => '11:15:00'],
                                                'karana'   => ['name' => 'Bava', 'number' => 1, 'end_time' => '14:32:00'],
                                                'weekday'  => ['name' => 'Thursday', 'number' => 4],
                                                'sun_rise' => '06:17:42',
                                                'sun_set'  => '18:08:33',
                                                'rahu_kaal'=> ['start' => '13:45:00', 'end' => '15:15:00'],
                                                'abhijit'  => ['start' => '11:52:00', 'end' => '12:38:00'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            '422' => ['$ref' => '#/components/responses/ValidationError'],
                            '503' => [
                                'description' => 'Navamsha API unavailable or returned an error',
                                'content'     => [
                                    'application/json' => [
                                        'schema'  => ['$ref' => '#/components/schemas/PanchangError'],
                                        'example' => [
                                            'success' => false,
                                            'error'   => ['code' => 'API_ERROR', 'message' => 'Navamsha API returned HTTP 500'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

            ],

            // ── Components ───────────────────────────────────────────────────
            'components' => [
                'responses' => [
                    'NotFound' => [
                        'description' => 'Resource not found',
                        'content'     => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ErrorMessage']]],
                    ],
                    'ValidationError' => [
                        'description' => 'Validation failed',
                        'content'     => [
                            'application/json' => [
                                'schema'  => ['$ref' => '#/components/schemas/ValidationErrors'],
                                'example' => ['message' => 'The donation_amount field is required.', 'errors' => ['donation_amount' => ['The donation_amount field is required.']]],
                            ],
                        ],
                    ],
                ],
                'schemas' => [

                    // ── Guru ─────────────────────────────────────────────────
                    'Guru' => [
                        'type'       => 'object',
                        'properties' => [
                            'id'          => ['type' => 'integer',          'example' => 1],
                            'name'        => ['type' => 'string',           'example' => 'Shri XYZ Maharaj'],
                            'description' => ['type' => 'string', 'nullable' => true, 'example' => 'Renowned spiritual guide.'],
                            'image'       => ['type' => 'string', 'nullable' => true, 'format' => 'uri'],
                            'status'      => ['type' => 'string', 'enum' => ['active','inactive'], 'example' => 'active'],
                        ],
                    ],
                    'GuruWithStats' => [
                        'allOf' => [
                            ['$ref' => '#/components/schemas/Guru'],
                            [
                                'type'       => 'object',
                                'properties' => [
                                    'stats' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'total_donations'    => ['type' => 'number', 'format' => 'float', 'example' => 52500.00],
                                            'total_donors'       => ['type' => 'integer', 'example' => 120],
                                            'total_transactions' => ['type' => 'integer', 'example' => 145],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'GuruListResponse'       => ['type' => 'object', 'properties' => ['data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Guru']]]],
                    'GuruSingleResponse'     => ['type' => 'object', 'properties' => ['data' => ['$ref' => '#/components/schemas/GuruWithStats']]],
                    'GuruCategoriesResponse' => [
                        'type'       => 'object',
                        'properties' => [
                            'data' => [
                                'type'       => 'object',
                                'properties' => [
                                    'guru'       => ['$ref' => '#/components/schemas/Guru'],
                                    'categories' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/DonationCategory']],
                                    'fee_config' => ['$ref' => '#/components/schemas/FeeConfigSimple'],
                                ],
                            ],
                        ],
                    ],

                    // ── Donation Category ─────────────────────────────────────
                    'DonationCategory' => [
                        'type'       => 'object',
                        'properties' => [
                            'id'          => ['type' => 'integer', 'example' => 1],
                            'name'        => ['type' => 'string',  'example' => 'Ann Prasadhan'],
                            'description' => ['type' => 'string',  'nullable' => true],
                            'image'       => ['type' => 'string',  'nullable' => true, 'format' => 'uri'],
                            'status'      => ['type' => 'string',  'enum' => ['active','inactive']],
                        ],
                    ],

                    // ── Fee Config ───────────────────────────────────────────
                    'FeeConfigSimple' => [
                        'type'       => 'object',
                        'properties' => [
                            'handling_charge' => ['type' => 'number', 'format' => 'float', 'example' => 1.00],
                            'gst_rate'        => ['type' => 'number', 'format' => 'float', 'example' => 18.00],
                            'gst_label'       => ['type' => 'string', 'example' => 'GST @ 18%'],
                        ],
                    ],
                    'FeeCalculation' => [
                        'type'       => 'object',
                        'properties' => [
                            'donation_amount' => ['type' => 'number', 'format' => 'float', 'example' => 500.00],
                            'handling_charge' => ['type' => 'number', 'format' => 'float', 'example' => 1.00],
                            'gst_rate'        => ['type' => 'number', 'format' => 'float', 'example' => 18.00],
                            'gst_amount'      => ['type' => 'number', 'format' => 'float', 'example' => 0.18],
                            'total_amount'    => ['type' => 'number', 'format' => 'float', 'example' => 501.18],
                        ],
                    ],
                    'FeeConfigResponse' => [
                        'type'       => 'object',
                        'properties' => [
                            'data' => [
                                'allOf' => [
                                    ['$ref' => '#/components/schemas/FeeConfigSimple'],
                                    ['type' => 'object', 'properties' => ['example_on_500' => ['$ref' => '#/components/schemas/FeeCalculation']]],
                                ],
                            ],
                        ],
                    ],

                    // ── Donation ─────────────────────────────────────────────
                    'CreateDonationRequest' => [
                        'type'     => 'object',
                        'required' => ['guru_id', 'category_id', 'donation_amount', 'payment_status'],
                        'properties' => [
                            'guru_id'         => ['type' => 'integer', 'example' => 1, 'description' => 'ID of the Guruji'],
                            'category_id'     => ['type' => 'integer', 'example' => 1, 'description' => 'ID of the donation category'],
                            'donation_amount' => ['type' => 'number',  'format' => 'float', 'minimum' => 1, 'example' => 500, 'description' => 'Pure donation amount (excluding fees)'],
                            'user_id'         => ['type' => 'integer', 'nullable' => true, 'example' => 1],
                            'payment_status'  => ['type' => 'string',  'enum' => ['pending','success','failed','cancelled'], 'example' => 'success'],
                            'payment_id'      => ['type' => 'string',  'nullable' => true, 'example' => 'pay_Abc123XYZ', 'description' => 'Payment gateway payment ID'],
                            'transaction_id'  => ['type' => 'string',  'nullable' => true, 'example' => 'TXN20260930001', 'description' => 'Idempotency key — same transaction_id returns existing record'],
                            'payment_method'  => ['type' => 'string',  'nullable' => true, 'example' => 'UPI', 'enum' => ['UPI','card','netbanking','wallet','cash',null]],
                        ],
                    ],
                    'DonationBreakdown' => [
                        'type'       => 'object',
                        'properties' => [
                            'donation_amount' => ['type' => 'number', 'format' => 'float', 'example' => 500.00],
                            'handling_charge' => ['type' => 'number', 'format' => 'float', 'example' => 1.00],
                            'gst_rate'        => ['type' => 'number', 'format' => 'float', 'example' => 18.00],
                            'gst_amount'      => ['type' => 'number', 'format' => 'float', 'example' => 0.18],
                            'total_amount'    => ['type' => 'number', 'format' => 'float', 'example' => 501.18],
                            'currency'        => ['type' => 'string', 'example' => 'INR'],
                        ],
                    ],
                    'Donation' => [
                        'type'       => 'object',
                        'properties' => [
                            'id'             => ['type' => 'integer', 'example' => 1],
                            'donation_id'    => ['type' => 'string',  'example' => 'DON-00001'],
                            'guru'           => ['type' => 'object',  'properties' => ['id' => ['type' => 'integer'], 'name' => ['type' => 'string']]],
                            'category'       => ['type' => 'object',  'properties' => ['id' => ['type' => 'integer'], 'name' => ['type' => 'string']]],
                            'breakdown'      => ['$ref' => '#/components/schemas/DonationBreakdown'],
                            'payment_status' => ['type' => 'string',  'enum' => ['pending','success','failed','cancelled','refunded']],
                            'payment_id'     => ['type' => 'string',  'nullable' => true],
                            'transaction_id' => ['type' => 'string',  'nullable' => true],
                            'payment_method' => ['type' => 'string',  'nullable' => true],
                            'created_at'     => ['type' => 'string',  'format' => 'date-time'],
                        ],
                    ],
                    'DonationResponse' => [
                        'type'       => 'object',
                        'properties' => [
                            'data'    => ['$ref' => '#/components/schemas/Donation'],
                            'created' => ['type' => 'boolean', 'example' => true, 'description' => 'false when an existing record was returned due to idempotency'],
                        ],
                    ],

                    // ── Services ─────────────────────────────────────────────
                    'ServicePricing' => [
                        'type'       => 'object',
                        'properties' => [
                            'amount'          => ['type' => 'integer', 'nullable' => true, 'example' => 999],
                            'currency'        => ['type' => 'string',  'example' => 'INR'],
                            'discount_amount' => ['type' => 'integer', 'nullable' => true],
                            'formatted'       => ['type' => 'string',  'nullable' => true, 'example' => '₹999'],
                        ],
                    ],
                    'Service' => [
                        'type'       => 'object',
                        'properties' => [
                            'id'                  => ['type' => 'integer', 'example' => 1],
                            'name'                => ['type' => 'string',  'nullable' => true, 'example' => 'Hair Spa'],
                            'title'               => ['type' => 'string',  'nullable' => true],
                            'description'         => ['type' => 'string',  'nullable' => true],
                            'language'            => ['type' => 'string',  'example' => 'en', 'description' => 'Language actually returned'],
                            'fallback_used'       => ['type' => 'boolean', 'example' => false],
                            'available_languages' => ['type' => 'array',   'items' => ['type' => 'string'], 'example' => ['en','hi']],
                            'primary_image'       => ['type' => 'string',  'nullable' => true, 'format' => 'uri'],
                            'gallery'             => ['type' => 'array',   'items' => ['type' => 'string', 'format' => 'uri']],
                            'pricing'             => ['$ref' => '#/components/schemas/ServicePricing'],
                            'status'              => ['type' => 'string',  'enum' => ['active','inactive']],
                            'created_at'          => ['type' => 'string',  'format' => 'date-time'],
                            'updated_at'          => ['type' => 'string',  'format' => 'date-time'],
                        ],
                    ],
                    'ServiceListResponse'   => ['type' => 'object', 'properties' => ['data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Service']]]],
                    'ServiceSingleResponse' => ['type' => 'object', 'properties' => ['data' => ['$ref' => '#/components/schemas/Service']]],

                    // ── Panchang ─────────────────────────────────────────────
                    'PanchangLocation' => [
                        'type'       => 'object',
                        'properties' => [
                            'name'      => ['type' => 'string',  'nullable' => true, 'example' => 'Indore, Madhya Pradesh'],
                            'latitude'  => ['type' => 'number',  'format' => 'float', 'example' => 22.72, 'description' => 'Normalized to 2 decimal places'],
                            'longitude' => ['type' => 'number',  'format' => 'float', 'example' => 75.86],
                            'timezone'  => ['type' => 'number',  'format' => 'float', 'example' => 5.5],
                        ],
                    ],
                    'PanchangResponse' => [
                        'type'       => 'object',
                        'properties' => [
                            'success'  => ['type' => 'boolean', 'example' => true],
                            'cached'   => ['type' => 'boolean', 'example' => false, 'description' => 'true when served from the database cache, false when freshly fetched from Navamsha API'],
                            'date'     => ['type' => 'string',  'format' => 'date', 'example' => '2026-10-01'],
                            'location' => ['$ref' => '#/components/schemas/PanchangLocation'],
                            'data'     => ['type' => 'object', 'description' => 'Panchang payload from Navamsha API. Shape varies by `feature`; `panchang_full` includes tithi, nakshatra, yoga, karana, weekday, sun_rise, sun_set, rahu_kaal, abhijit.', 'additionalProperties' => true],
                        ],
                    ],
                    'PanchangError' => [
                        'type'       => 'object',
                        'properties' => [
                            'success' => ['type' => 'boolean', 'example' => false],
                            'error'   => [
                                'type'       => 'object',
                                'properties' => [
                                    'code'    => ['type' => 'string', 'example' => 'API_ERROR'],
                                    'message' => ['type' => 'string', 'example' => 'Navamsha API returned HTTP 500'],
                                ],
                            ],
                        ],
                    ],

                    // ── Errors ───────────────────────────────────────────────
                    'ErrorMessage' => [
                        'type'       => 'object',
                        'properties' => ['message' => ['type' => 'string', 'example' => 'No query results for model.']],
                    ],
                    'ValidationErrors' => [
                        'type'       => 'object',
                        'properties' => [
                            'message' => ['type' => 'string'],
                            'errors'  => ['type' => 'object', 'additionalProperties' => ['type' => 'array', 'items' => ['type' => 'string']]],
                        ],
                    ],
                ],
            ],
        ];

        return response()->json($spec)
            ->header('Access-Control-Allow-Origin', '*');
    }
}
