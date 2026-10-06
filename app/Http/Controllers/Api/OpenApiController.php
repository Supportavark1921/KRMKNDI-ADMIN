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
                'title' => 'ARK Jyotish API',
                'description' => "REST API consumed by the ARK Jyotish mobile app.\n\n"
                    ."**Base URL:** `{$base}/api`\n\n"
                    ."All responses are JSON. Language-specific fields fall back to English when the requested translation is unavailable.\n\n"
                    ."**Modules:**\n"
                    ."- 🕉 **Gurujis** — list and fetch Guruji profiles with donation categories\n"
                    ."- ₹ **Donations** — fee config and create donation transactions\n"
                    ."- ✦ **Services** — multilingual service catalogue\n"
                    ."- 🌙 **Panchang** — daily Vedic Panchang data (Tithi, Nakshatra, Yoga, Karana, Choghadiya, Hora, Rahu Kaal, Abhijit) via Navamsha API with location-aware caching\n"
                    ."- 🎯 **Promotions** — active promotional banners and offers, filterable by placement, audience, and language\n"
                    ."- 🛕 **Samagri** — poojan samagri product catalogue with categories, search, filter, sort, and paginated listing\n"
                    ."- 🛒 **Cart** — authenticated per-user cart (add, update quantity, remove, clear). Requires Bearer token.\n"
                    .'- 📦 **Orders** — place and view samagri orders with COD / online payment method, platform fee, and GST. Requires Bearer token.',
                'version' => '1.0.0',
                'contact' => ['name' => 'ARK Jyotish Admin', 'url' => $base],
            ],
            'servers' => [
                ['url' => $base, 'description' => 'Current server ('.$base.')'],
            ],
            'tags' => [
                ['name' => 'Gurujis',     'description' => 'Guruji profiles, donation categories, and per-Guruji stats'],
                ['name' => 'Donations',   'description' => 'App handling fee configuration and donation transactions'],
                ['name' => 'Services',    'description' => 'Multilingual booking services'],
                ['name' => 'Promotions',  'description' => 'Active promotional banners and offers shown in the app'],
                ['name' => 'Location',    'description' => 'Cascading location master — countries, states, districts, cities, PIN codes'],
                ['name' => 'Panchang',  'description' => 'Daily Vedic Panchang data — Tithi, Nakshatra, Yoga, Karana, Choghadiya, Hora, Rahu Kaal, Abhijit. Served from cache; Navamsha API key is server-side only.'],
                ['name' => 'Samagri',   'description' => 'Poojan samagri product catalogue — categories, paginated product listing with search/filter/sort, and product detail.'],
                ['name' => 'Cart',     'description' => 'Per-user cart stored server-side. All endpoints require a valid Bearer token (auth:sanctum).'],
                ['name' => 'Orders',   'description' => 'Samagri orders with line items, delivery details, payment method, platform fee, and GST. All endpoints require a valid Bearer token.'],
            ],

            // ── Paths ────────────────────────────────────────────────────────
            'paths' => [

                // ── GURUJIS ──────────────────────────────────────────────────

                '/api/gurus' => [
                    'get' => [
                        'tags'        => ['Gurujis'],
                        'summary'     => 'List all active Gurujis',
                        'description' => "Returns all Gurujis with `status = active`, ordered by name.\n\n"
                            ."**Mobile usage:** Displayed in the **Karma Consultations** section on the Home screen as a horizontal scroll of avatar cards (photo + name). "
                            .'Tap a card to open the Guruji detail screen.',
                        'operationId' => 'listGurus',
                        'responses'   => [
                            '200' => [
                                'description' => 'Success',
                                'content' => [
                                    'application/json' => [
                                        'schema'  => ['$ref' => '#/components/schemas/GuruListResponse'],
                                        'example' => [
                                            'data' => [
                                                ['id' => 1, 'name' => 'Shri XYZ Maharaj', 'description' => 'Renowned spiritual guide.', 'image' => 'https://krmknd.avark.biz/storage/gurus/1.jpg', 'status' => 'active'],
                                                ['id' => 2, 'name' => 'Shri ABC Swami',   'description' => 'Vedic scholar.',            'image' => null,                                              'status' => 'active'],
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
                        'description' => "Returns full Guruji profile including lifetime donation stats.\n\n"
                            ."**Mobile usage:** Displayed on the **Guruji Detail screen** — shows the photo, name, total donations, total donors, and an about section. "
                            .'Also provides the "Book Consultation" CTA that navigates to the Pooja booking flow.',
                        'operationId' => 'getGuru',
                        'parameters'  => [
                            ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 1],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Success',
                                'content' => [
                                    'application/json' => [
                                        'schema'  => ['$ref' => '#/components/schemas/GuruSingleResponse'],
                                        'example' => [
                                            'data' => [
                                                'id'          => 1,
                                                'name'        => 'Shri XYZ Maharaj',
                                                'description' => 'A renowned spiritual guide with 30 years of experience in Vedic rituals.',
                                                'image'       => 'https://krmknd.avark.biz/storage/gurus/1.jpg',
                                                'status'      => 'active',
                                                'stats'       => [
                                                    'total_donations'    => 52500.00,
                                                    'total_donors'       => 120,
                                                    'total_transactions' => 145,
                                                ],
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
                        'tags' => ['Gurujis'],
                        'summary' => 'Get donation categories for a Guruji',
                        'description' => "Returns the Guruji's active donation categories together with the current fee config.\n\n"
                            ."**Mobile usage:** Called when the user taps **Book Consultation** on the Guruji Detail screen. "
                            .'Populates the category picker and builds the payment breakdown (donation + handling charge + GST). '
                            .'Never hardcode fee values — always fetch from this endpoint.',
                        'operationId' => 'getGuruCategories',
                        'parameters' => [
                            ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 1],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Success',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/GuruCategoriesResponse'],
                                        'example' => [
                                            'data' => [
                                                'guru' => ['id' => 1, 'name' => 'Shri XYZ Maharaj', 'image' => null, 'status' => 'active'],
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
                        'tags' => ['Donations'],
                        'summary' => 'Get current app handling fee configuration',
                        'description' => 'Returns the active handling charge and GST rate, plus a live example on ₹500. '
                            .'The APK **must** use this to calculate and display the breakdown before payment — never hardcode the fee.',
                        'operationId' => 'getDonationFeeConfig',
                        'responses' => [
                            '200' => [
                                'description' => 'Success',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/FeeConfigResponse'],
                                        'example' => [
                                            'data' => [
                                                'handling_charge' => 1.00,
                                                'gst_rate' => 18.00,
                                                'gst_label' => 'GST @ 18%',
                                                'example_on_500' => [
                                                    'donation_amount' => 500.00,
                                                    'handling_charge' => 1.00,
                                                    'gst_rate' => 18.00,
                                                    'gst_amount' => 0.18,
                                                    'total_amount' => 501.18,
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
                        'tags' => ['Donations'],
                        'summary' => 'Create a donation transaction',
                        'description' => 'Creates a new donation. The server **always recalculates** handling_charge, gst_amount, and total_amount '
                            ."from the active fee config — never trust a client-submitted total.\n\n"
                            .'**Idempotency:** if `transaction_id` is supplied and already exists, the existing record is returned with `created: false`.',
                        'operationId' => 'createDonation',
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => ['$ref' => '#/components/schemas/CreateDonationRequest'],
                                    'example' => [
                                        'guru_id' => 1,
                                        'category_id' => 1,
                                        'donation_amount' => 500,
                                        'user_id' => 1,
                                        'payment_status' => 'success',
                                        'payment_id' => 'pay_Abc123XYZ',
                                        'transaction_id' => 'TXN20260930001',
                                        'payment_method' => 'UPI',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '201' => [
                                'description' => 'Donation created',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/DonationResponse'],
                                        'example' => [
                                            'data' => [
                                                'id' => 1,
                                                'donation_id' => 'DON-00001',
                                                'guru' => ['id' => 1, 'name' => 'Shri XYZ Maharaj'],
                                                'category' => ['id' => 1, 'name' => 'Ann Prasadhan'],
                                                'breakdown' => [
                                                    'donation_amount' => 500.00,
                                                    'handling_charge' => 1.00,
                                                    'gst_rate' => 18.00,
                                                    'gst_amount' => 0.18,
                                                    'total_amount' => 501.18,
                                                    'currency' => 'INR',
                                                ],
                                                'payment_status' => 'success',
                                                'transaction_id' => 'TXN20260930001',
                                                'payment_method' => 'UPI',
                                                'created_at' => '2026-09-30T10:30:00.000000Z',
                                            ],
                                            'created' => true,
                                        ],
                                    ],
                                ],
                            ],
                            '200' => [
                                'description' => 'Existing donation returned (idempotent — same transaction_id)',
                                'content' => [
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
                        'tags' => ['Services'],
                        'summary' => 'List all services',
                        'description' => 'Returns services with translatable fields resolved for the requested language. Falls back to English.',
                        'operationId' => 'listServices',
                        'parameters' => [
                            [
                                'name' => 'language', 'in' => 'query', 'required' => false,
                                'description' => 'BCP-47 language code',
                                'schema' => ['type' => 'string', 'enum' => ['en', 'hi', 'mr', 'gu', 'ta', 'te', 'bn'], 'default' => 'en'],
                                'example' => 'hi',
                            ],
                            [
                                'name' => 'status', 'in' => 'query', 'required' => false,
                                'schema' => ['type' => 'string', 'enum' => ['active', 'inactive', 'all'], 'default' => 'active'],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Success',
                                'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ServiceListResponse']]],
                            ],
                        ],
                    ],
                ],

                '/api/services/{id}' => [
                    'get' => [
                        'tags' => ['Services'],
                        'summary' => 'Get a single service',
                        'operationId' => 'getService',
                        'parameters' => [
                            ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 1],
                            [
                                'name' => 'language', 'in' => 'query', 'required' => false,
                                'schema' => ['type' => 'string', 'enum' => ['en', 'hi', 'mr', 'gu', 'ta', 'te', 'bn'], 'default' => 'en'],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Success',       'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ServiceSingleResponse']]]],
                            '404' => ['$ref' => '#/components/responses/NotFound'],
                        ],
                    ],
                ],
                // ── LOCATION ─────────────────────────────────────────────────

                '/api/locations/countries' => [
                    'get' => [
                        'tags' => ['Location'],
                        'summary' => 'List all active countries',
                        'operationId' => 'listCountries',
                        'responses' => [
                            '200' => [
                                'description' => 'Success',
                                'content' => [
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
                        'tags' => ['Location'],
                        'summary' => 'List states / UTs for a country',
                        'operationId' => 'listStatesByCountryId',
                        'parameters' => [
                            ['name' => 'country_id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 1],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Success',
                                'content' => [
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
                        'tags' => ['Location'],
                        'summary' => 'List states / UTs by country ISO code',
                        'operationId' => 'listStatesByIso',
                        'parameters' => [
                            ['name' => 'iso', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string', 'maxLength' => 2], 'example' => 'IN'],
                        ],
                        'responses' => ['200' => ['description' => 'Success']],
                    ],
                ],

                '/api/locations/states/{state_id}/districts' => [
                    'get' => [
                        'tags' => ['Location'],
                        'summary' => 'List districts for a state',
                        'operationId' => 'listDistricts',
                        'parameters' => [
                            ['name' => 'state_id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 13],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Success',
                                'content' => [
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
                        'tags' => ['Location'],
                        'summary' => 'List cities for a district',
                        'operationId' => 'listCities',
                        'parameters' => [
                            ['name' => 'district_id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 1],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Success',
                                'content' => [
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
                        'tags' => ['Location'],
                        'summary' => 'List PIN codes (post offices) for a city',
                        'description' => 'One city can have multiple post offices and multiple PIN codes. Returns all active entries.',
                        'operationId' => 'listPincodes',
                        'parameters' => [
                            ['name' => 'city_id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 1],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Success',
                                'content' => [
                                    'application/json' => [
                                        'example' => [
                                            ['id' => 1,  'pincode' => '452001', 'post_office_name' => 'Indore H.O',           'office_type' => 'HEAD POST OFFICE',  'delivery_status' => 'Delivery'],
                                            ['id' => 2,  'pincode' => '452002', 'post_office_name' => 'Indore Cloth Market S.O', 'office_type' => 'SUB POST OFFICE', 'delivery_status' => 'Delivery'],
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
                        'tags' => ['Location'],
                        'summary' => 'Reverse PIN code lookup',
                        'description' => 'Returns all post offices matching the 6-digit PIN, each with its state, district and city. Use this to auto-fill an address form when the user enters a PIN.',
                        'operationId' => 'lookupPincode',
                        'parameters' => [
                            ['name' => 'pincode', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string', 'pattern' => '^\d{6}$'], 'example' => '452001'],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'One or more post offices matching this PIN',
                                'content' => [
                                    'application/json' => [
                                        'example' => [
                                            [
                                                'id' => 1, 'pincode' => '452001', 'post_office_name' => 'Indore H.O', 'office_type' => 'HEAD POST OFFICE',
                                                'state' => ['id' => 13, 'name' => 'Madhya Pradesh', 'code' => 'MP'],
                                                'district' => ['id' => 1,  'name' => 'Indore'],
                                                'city' => ['id' => 1,  'name' => 'Indore'],
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
                        'tags' => ['Location'],
                        'summary' => 'Search cities, districts or PIN codes',
                        'description' => 'Type-aware search: a digit string searches PIN codes; text searches cities and districts. Minimum 2 characters.',
                        'operationId' => 'searchLocations',
                        'parameters' => [
                            [
                                'name' => 'q',
                                'in' => 'query',
                                'required' => true,
                                'description' => 'Search query — text for city/district, digits for PIN prefix',
                                'schema' => ['type' => 'string', 'minLength' => 2],
                                'example' => 'indore',
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Array of matched results (type = city | district | pincode)',
                                'content' => [
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
                        'tags' => ['Panchang'],
                        'summary' => 'Get daily Vedic Panchang for a location and date',
                        'description' => "Returns full or feature-specific Panchang data for a given location and date.\n\n"
                            .'**Caching:** coordinates are rounded to 2 decimal places (~1 km grid). '
                            .'The first request for a new location+date hits the Navamsha API; subsequent requests are served from the database cache. '
                            ."Cache expires at the end of the local calendar day (historical records are kept permanently).\n\n"
                            .'**No auth required** — this endpoint is called directly from the mobile APK. '
                            .'The Navamsha API key is **never** exposed; it is used only on the server.',
                        'operationId' => 'getPanchang',
                        'parameters' => [
                            [
                                'name' => 'latitude',
                                'in' => 'query',
                                'required' => true,
                                'description' => 'Latitude of the birth/query location (decimal degrees)',
                                'schema' => ['type' => 'number', 'format' => 'float', 'minimum' => -90, 'maximum' => 90],
                                'example' => 22.7196,
                            ],
                            [
                                'name' => 'longitude',
                                'in' => 'query',
                                'required' => true,
                                'description' => 'Longitude of the birth/query location (decimal degrees)',
                                'schema' => ['type' => 'number', 'format' => 'float', 'minimum' => -180, 'maximum' => 180],
                                'example' => 75.8577,
                            ],
                            [
                                'name' => 'timezone',
                                'in' => 'query',
                                'required' => true,
                                'description' => 'UTC offset in decimal hours (e.g. 5.5 for IST, -5.0 for EST)',
                                'schema' => ['type' => 'number', 'format' => 'float', 'minimum' => -14, 'maximum' => 14],
                                'example' => 5.5,
                            ],
                            [
                                'name' => 'date',
                                'in' => 'query',
                                'required' => false,
                                'description' => 'Calendar date in YYYY-MM-DD format. Defaults to today in the server\'s local timezone.',
                                'schema' => ['type' => 'string', 'format' => 'date'],
                                'example' => '2026-10-01',
                            ],
                            [
                                'name' => 'feature',
                                'in' => 'query',
                                'required' => false,
                                'description' => 'Which subset of Panchang data to return. Defaults to `panchang_full`.',
                                'schema' => [
                                    'type' => 'string',
                                    'enum' => ['panchang_full', 'choghadiya', 'hora', 'rahu_kaal', 'sun_times', 'abhijit'],
                                    'default' => 'panchang_full',
                                ],
                                'example' => 'panchang_full',
                            ],
                            [
                                'name' => 'location',
                                'in' => 'query',
                                'required' => false,
                                'description' => 'Human-readable location name stored in the cache record (informational only).',
                                'schema' => ['type' => 'string', 'maxLength' => 255],
                                'example' => 'Indore, Madhya Pradesh',
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Panchang data (live from Navamsha API or served from cache)',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/PanchangResponse'],
                                        'example' => [
                                            'success' => true,
                                            'cached' => false,
                                            'date' => '2026-10-01',
                                            'location' => [
                                                'name' => 'Indore, Madhya Pradesh',
                                                'latitude' => 22.72,
                                                'longitude' => 75.86,
                                                'timezone' => 5.5,
                                            ],
                                            'data' => [
                                                'tithi' => ['name' => 'Tritiya', 'number' => 3, 'paksha' => 'Shukla', 'end_time' => '14:32:00'],
                                                'nakshatra' => ['name' => 'Rohini', 'number' => 4, 'end_time' => '18:45:00'],
                                                'yoga' => ['name' => 'Shobhana', 'number' => 6, 'end_time' => '11:15:00'],
                                                'karana' => ['name' => 'Bava', 'number' => 1, 'end_time' => '14:32:00'],
                                                'weekday' => ['name' => 'Thursday', 'number' => 4],
                                                'sun_rise' => '06:17:42',
                                                'sun_set' => '18:08:33',
                                                'rahu_kaal' => ['start' => '13:45:00', 'end' => '15:15:00'],
                                                'abhijit' => ['start' => '11:52:00', 'end' => '12:38:00'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            '422' => ['$ref' => '#/components/responses/ValidationError'],
                            '503' => [
                                'description' => 'Navamsha API unavailable or returned an error',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/PanchangError'],
                                        'example' => [
                                            'success' => false,
                                            'error' => ['code' => 'API_ERROR', 'message' => 'Navamsha API returned HTTP 500'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                // ── PROMOTIONS ───────────────────────────────────────────────

                '/api/v1/promotions' => [
                    'get' => [
                        'tags'        => ['Promotions'],
                        'summary'     => 'List active promotions',
                        'description' => 'Returns active promotions filtered by placement, audience, and language. Used by the mobile app to display offer banners on the home screen.',
                        'operationId' => 'listPromotions',
                        'parameters'  => [
                            [
                                'name'        => 'placement',
                                'in'          => 'query',
                                'required'    => false,
                                'description' => 'Screen placement to filter by (e.g. `home`, `pooja`, `shop`).',
                                'schema'      => ['type' => 'string', 'example' => 'home'],
                            ],
                            [
                                'name'        => 'lang',
                                'in'          => 'query',
                                'required'    => false,
                                'description' => 'Language code for translated fields (`en`, `hi`). Defaults to `en`.',
                                'schema'      => ['type' => 'string', 'example' => 'en', 'default' => 'en'],
                            ],
                            [
                                'name'        => 'audience',
                                'in'          => 'query',
                                'required'    => false,
                                'description' => 'Audience segment: `all`, `guest`, or `user`. Defaults to `all`.',
                                'schema'      => ['type' => 'string', 'enum' => ['all', 'guest', 'user'], 'default' => 'all'],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Success',
                                'content' => [
                                    'application/json' => [
                                        'schema'  => ['$ref' => '#/components/schemas/PromotionsResponse'],
                                        'example' => [
                                            'success' => true,
                                            'data' => [
                                                [
                                                    'id'          => 1,
                                                    'title'       => 'Navratri Special Pooja',
                                                    'description' => 'Book your Navratri Pooja at 20% off. Limited slots available.',
                                                    'image'       => 'https://krmknd.avark.biz/storage/promotions/navratri.jpg',
                                                    'type'        => 'banner',
                                                    'placement'   => 'home',
                                                    'audience'    => 'all',
                                                    'cta'         => ['type' => 'screen', 'value' => 'Pooja'],
                                                    'starts_at'   => '2026-10-01T00:00:00+05:30',
                                                    'ends_at'     => '2026-10-15T23:59:59+05:30',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                // ── SAMAGRI ──────────────────────────────────────────────────

                '/api/v1/samagri/categories' => [
                    'get' => [
                        'tags'        => ['Samagri'],
                        'summary'     => 'List active product categories',
                        'description' => 'Returns all active root-level samagri categories ordered by sort_order.',
                        'operationId' => 'listSamagriCategories',
                        'responses'   => [
                            '200' => [
                                'description' => 'Success',
                                'content' => [
                                    'application/json' => [
                                        'schema'  => ['$ref' => '#/components/schemas/SamagriCategoryListResponse'],
                                        'example' => [
                                            'data' => [
                                                ['id' => 1, 'name' => 'Daily Puja',   'slug' => 'daily-puja',   'image' => 'https://krmknd.avark.biz/storage/categories/daily-puja.jpg'],
                                                ['id' => 2, 'name' => 'Havan',        'slug' => 'havan',        'image' => null],
                                                ['id' => 3, 'name' => 'Festival',     'slug' => 'festival',     'image' => null],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                '/api/v1/samagri/products' => [
                    'get' => [
                        'tags'        => ['Samagri'],
                        'summary'     => 'List products (paginated)',
                        'description' => "Returns active products with category, primary image, and inventory-derived stock status.\n\n"
                            ."**Filters:** `category_id`, `search` (name or short description), `stock` (`in_stock` | `low_stock`).\n\n"
                            .'**Sort:** `popular` (default, by reviews_count desc), `price_asc`, `price_desc`.',
                        'operationId' => 'listSamagriProducts',
                        'parameters'  => [
                            ['name' => 'search',      'in' => 'query', 'required' => false, 'schema' => ['type' => 'string', 'maxLength' => 100], 'example' => 'havan'],
                            ['name' => 'category_id', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'integer'], 'example' => 2],
                            ['name' => 'stock',       'in' => 'query', 'required' => false, 'schema' => ['type' => 'string', 'enum' => ['in_stock', 'low_stock']]],
                            ['name' => 'sort',        'in' => 'query', 'required' => false, 'schema' => ['type' => 'string', 'enum' => ['popular', 'price_asc', 'price_desc'], 'default' => 'popular']],
                            ['name' => 'per_page',    'in' => 'query', 'required' => false, 'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 20]],
                            ['name' => 'page',        'in' => 'query', 'required' => false, 'schema' => ['type' => 'integer', 'minimum' => 1, 'default' => 1]],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Paginated product list',
                                'content' => [
                                    'application/json' => [
                                        'schema'  => ['$ref' => '#/components/schemas/SamagriProductListResponse'],
                                        'example' => [
                                            'data' => [
                                                [
                                                    'id' => 1, 'name' => 'Complete Daily Puja Kit', 'category' => 'Daily Puja', 'category_id' => 1,
                                                    'description' => 'Essential poojan samagri for daily worship at home.',
                                                    'price' => 499.00, 'mrp' => 699.00, 'unit' => '1 kit', 'badge' => 'Best Seller',
                                                    'rating' => 4.8, 'reviews' => 384, 'stock' => 'In stock',
                                                    'uses' => ['Daily puja', 'Home mandir'], 'contents' => ['Roli', 'Akshat'],
                                                    'image' => 'https://krmknd.avark.biz/storage/products/puja-kit.jpg',
                                                ],
                                            ],
                                            'meta' => ['current_page' => 1, 'last_page' => 3, 'total' => 52],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                '/api/v1/samagri/products/{product}' => [
                    'get' => [
                        'tags'        => ['Samagri'],
                        'summary'     => 'Get a single product',
                        'description' => 'Returns full product detail including all images.',
                        'operationId' => 'getSamagriProduct',
                        'parameters'  => [
                            ['name' => 'product', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 1],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Product detail',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/SamagriProductDetailResponse'],
                                    ],
                                ],
                            ],
                            '404' => ['$ref' => '#/components/responses/NotFound'],
                        ],
                    ],
                ],

                // ── CART ─────────────────────────────────────────────────────

                '/api/v1/cart' => [
                    'get' => [
                        'tags'        => ['Cart'],
                        'summary'     => 'Get current user\'s cart',
                        'operationId' => 'getCart',
                        'security'    => [['BearerAuth' => []]],
                        'responses'   => [
                            '200' => [
                                'description' => 'Cart items',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/CartListResponse'],
                                    ],
                                ],
                            ],
                            '401' => ['description' => 'Unauthenticated'],
                        ],
                    ],
                    'post' => [
                        'tags'        => ['Cart'],
                        'summary'     => 'Add item to cart',
                        'description' => 'Adds a product to the cart. If the product already exists, increments the quantity.',
                        'operationId' => 'addToCart',
                        'security'    => [['BearerAuth' => []]],
                        'requestBody' => [
                            'required' => true,
                            'content'  => [
                                'application/json' => [
                                    'schema'  => ['$ref' => '#/components/schemas/AddToCartRequest'],
                                    'example' => ['product_id' => 1, 'quantity' => 2],
                                ],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Cart item (created or updated)',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['$ref' => '#/components/schemas/CartItemResponse'],
                                    ],
                                ],
                            ],
                            '401' => ['description' => 'Unauthenticated'],
                            '404' => ['$ref' => '#/components/responses/NotFound'],
                            '422' => ['$ref' => '#/components/responses/ValidationError'],
                        ],
                    ],
                    'delete' => [
                        'tags'        => ['Cart'],
                        'summary'     => 'Clear entire cart',
                        'operationId' => 'clearCart',
                        'security'    => [['BearerAuth' => []]],
                        'responses'   => [
                            '200' => ['description' => 'Cart cleared', 'content' => ['application/json' => ['schema' => ['type' => 'object', 'properties' => ['message' => ['type' => 'string', 'example' => 'Cart cleared.']]]]]],
                            '401' => ['description' => 'Unauthenticated'],
                        ],
                    ],
                ],

                '/api/v1/cart/{cartItem}' => [
                    'put' => [
                        'tags'        => ['Cart'],
                        'summary'     => 'Update cart item quantity',
                        'description' => 'Sets the exact quantity of a cart item. Sending `quantity: 0` removes the item.',
                        'operationId' => 'updateCartItem',
                        'security'    => [['BearerAuth' => []]],
                        'parameters'  => [
                            ['name' => 'cartItem', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 3],
                        ],
                        'requestBody' => [
                            'required' => true,
                            'content'  => [
                                'application/json' => [
                                    'schema'  => ['$ref' => '#/components/schemas/UpdateCartItemRequest'],
                                    'example' => ['quantity' => 3],
                                ],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Updated cart item (null data when quantity was 0 and item was removed)',
                                'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CartItemNullableResponse']]],
                            ],
                            '401' => ['description' => 'Unauthenticated'],
                            '403' => ['description' => 'Forbidden — not your cart item'],
                            '404' => ['$ref' => '#/components/responses/NotFound'],
                            '422' => ['$ref' => '#/components/responses/ValidationError'],
                        ],
                    ],
                    'delete' => [
                        'tags'        => ['Cart'],
                        'summary'     => 'Remove a single cart item',
                        'operationId' => 'removeCartItem',
                        'security'    => [['BearerAuth' => []]],
                        'parameters'  => [
                            ['name' => 'cartItem', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 3],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Item removed', 'content' => ['application/json' => ['schema' => ['type' => 'object', 'properties' => ['message' => ['type' => 'string', 'example' => 'Item removed.']]]]]],
                            '401' => ['description' => 'Unauthenticated'],
                            '403' => ['description' => 'Forbidden — not your cart item'],
                            '404' => ['$ref' => '#/components/responses/NotFound'],
                        ],
                    ],
                ],

                // ── ORDERS ───────────────────────────────────────────────────

                '/api/v1/orders' => [
                    'get' => [
                        'tags'        => ['Orders'],
                        'summary'     => 'List current user\'s orders',
                        'description' => 'Returns orders for the authenticated user, newest first, paginated at 20/page.',
                        'operationId' => 'listOrders',
                        'security'    => [['BearerAuth' => []]],
                        'parameters'  => [
                            ['name' => 'page', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'integer', 'minimum' => 1, 'default' => 1]],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Paginated order list',
                                'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/OrderListResponse']]],
                            ],
                            '401' => ['description' => 'Unauthenticated'],
                        ],
                    ],
                    'post' => [
                        'tags'        => ['Orders'],
                        'summary'     => 'Place a new order',
                        'description' => "Creates an order from the provided items. The server computes `total_amount = subtotal + platform_fee + gst_amount`.\n\n"
                            .'Fetch `platform_fee` and `gst_rate` from `GET /api/booking/charges` and compute `gst_amount` client-side before sending.',
                        'operationId' => 'createOrder',
                        'security'    => [['BearerAuth' => []]],
                        'requestBody' => [
                            'required' => true,
                            'content'  => [
                                'application/json' => [
                                    'schema'  => ['$ref' => '#/components/schemas/CreateOrderRequest'],
                                    'example' => [
                                        'name' => 'Rahul Sharma', 'phone' => '9876543210',
                                        'address' => '12 MG Road, Indore, MP', 'notes' => null,
                                        'payment_method' => 'cod',
                                        'platform_fee' => 10.00, 'gst_amount' => 1.80,
                                        'items' => [['product_id' => 1, 'quantity' => 2], ['product_id' => 5, 'quantity' => 1]],
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '201' => [
                                'description' => 'Order placed',
                                'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/OrderResponse']]],
                            ],
                            '401' => ['description' => 'Unauthenticated'],
                            '422' => ['$ref' => '#/components/responses/ValidationError'],
                        ],
                    ],
                ],

                '/api/v1/orders/{order}' => [
                    'get' => [
                        'tags'        => ['Orders'],
                        'summary'     => 'Get order detail',
                        'description' => 'Returns a single order with all line items. Users can only access their own orders.',
                        'operationId' => 'getOrder',
                        'security'    => [['BearerAuth' => []]],
                        'parameters'  => [
                            ['name' => 'order', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 42],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Order detail',
                                'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/OrderResponse']]],
                            ],
                            '401' => ['description' => 'Unauthenticated'],
                            '403' => ['description' => 'Forbidden — not your order'],
                            '404' => ['$ref' => '#/components/responses/NotFound'],
                        ],
                    ],
                ],

            ],

            // ── Components ───────────────────────────────────────────────────
            'components' => [
                'responses' => [
                    'NotFound' => [
                        'description' => 'Resource not found',
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ErrorMessage']]],
                    ],
                    'ValidationError' => [
                        'description' => 'Validation failed',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/ValidationErrors'],
                                'example' => ['message' => 'The donation_amount field is required.', 'errors' => ['donation_amount' => ['The donation_amount field is required.']]],
                            ],
                        ],
                    ],
                ],
                'schemas' => [

                    // ── Guru ─────────────────────────────────────────────────
                    'Guru' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer',          'example' => 1],
                            'name' => ['type' => 'string',           'example' => 'Shri XYZ Maharaj'],
                            'description' => ['type' => 'string', 'nullable' => true, 'example' => 'Renowned spiritual guide.'],
                            'image' => ['type' => 'string', 'nullable' => true, 'format' => 'uri'],
                            'status' => ['type' => 'string', 'enum' => ['active', 'inactive'], 'example' => 'active'],
                        ],
                    ],
                    'GuruWithStats' => [
                        'allOf' => [
                            ['$ref' => '#/components/schemas/Guru'],
                            [
                                'type' => 'object',
                                'properties' => [
                                    'stats' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'total_donations' => ['type' => 'number', 'format' => 'float', 'example' => 52500.00],
                                            'total_donors' => ['type' => 'integer', 'example' => 120],
                                            'total_transactions' => ['type' => 'integer', 'example' => 145],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'GuruListResponse' => ['type' => 'object', 'properties' => ['data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Guru']]]],
                    'GuruSingleResponse' => ['type' => 'object', 'properties' => ['data' => ['$ref' => '#/components/schemas/GuruWithStats']]],
                    'GuruCategoriesResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'data' => [
                                'type' => 'object',
                                'properties' => [
                                    'guru' => ['$ref' => '#/components/schemas/Guru'],
                                    'categories' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/DonationCategory']],
                                    'fee_config' => ['$ref' => '#/components/schemas/FeeConfigSimple'],
                                ],
                            ],
                        ],
                    ],

                    // ── Donation Category ─────────────────────────────────────
                    'DonationCategory' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'name' => ['type' => 'string',  'example' => 'Ann Prasadhan'],
                            'description' => ['type' => 'string',  'nullable' => true],
                            'image' => ['type' => 'string',  'nullable' => true, 'format' => 'uri'],
                            'status' => ['type' => 'string',  'enum' => ['active', 'inactive']],
                        ],
                    ],

                    // ── Fee Config ───────────────────────────────────────────
                    'FeeConfigSimple' => [
                        'type' => 'object',
                        'properties' => [
                            'handling_charge' => ['type' => 'number', 'format' => 'float', 'example' => 1.00],
                            'gst_rate' => ['type' => 'number', 'format' => 'float', 'example' => 18.00],
                            'gst_label' => ['type' => 'string', 'example' => 'GST @ 18%'],
                        ],
                    ],
                    'FeeCalculation' => [
                        'type' => 'object',
                        'properties' => [
                            'donation_amount' => ['type' => 'number', 'format' => 'float', 'example' => 500.00],
                            'handling_charge' => ['type' => 'number', 'format' => 'float', 'example' => 1.00],
                            'gst_rate' => ['type' => 'number', 'format' => 'float', 'example' => 18.00],
                            'gst_amount' => ['type' => 'number', 'format' => 'float', 'example' => 0.18],
                            'total_amount' => ['type' => 'number', 'format' => 'float', 'example' => 501.18],
                        ],
                    ],
                    'FeeConfigResponse' => [
                        'type' => 'object',
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
                        'type' => 'object',
                        'required' => ['guru_id', 'category_id', 'donation_amount', 'payment_status'],
                        'properties' => [
                            'guru_id' => ['type' => 'integer', 'example' => 1, 'description' => 'ID of the Guruji'],
                            'category_id' => ['type' => 'integer', 'example' => 1, 'description' => 'ID of the donation category'],
                            'donation_amount' => ['type' => 'number',  'format' => 'float', 'minimum' => 1, 'example' => 500, 'description' => 'Pure donation amount (excluding fees)'],
                            'user_id' => ['type' => 'integer', 'nullable' => true, 'example' => 1],
                            'payment_status' => ['type' => 'string',  'enum' => ['pending', 'success', 'failed', 'cancelled'], 'example' => 'success'],
                            'payment_id' => ['type' => 'string',  'nullable' => true, 'example' => 'pay_Abc123XYZ', 'description' => 'Payment gateway payment ID'],
                            'transaction_id' => ['type' => 'string',  'nullable' => true, 'example' => 'TXN20260930001', 'description' => 'Idempotency key — same transaction_id returns existing record'],
                            'payment_method' => ['type' => 'string',  'nullable' => true, 'example' => 'UPI', 'enum' => ['UPI', 'card', 'netbanking', 'wallet', 'cash', null]],
                        ],
                    ],
                    'DonationBreakdown' => [
                        'type' => 'object',
                        'properties' => [
                            'donation_amount' => ['type' => 'number', 'format' => 'float', 'example' => 500.00],
                            'handling_charge' => ['type' => 'number', 'format' => 'float', 'example' => 1.00],
                            'gst_rate' => ['type' => 'number', 'format' => 'float', 'example' => 18.00],
                            'gst_amount' => ['type' => 'number', 'format' => 'float', 'example' => 0.18],
                            'total_amount' => ['type' => 'number', 'format' => 'float', 'example' => 501.18],
                            'currency' => ['type' => 'string', 'example' => 'INR'],
                        ],
                    ],
                    'Donation' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'donation_id' => ['type' => 'string',  'example' => 'DON-00001'],
                            'guru' => ['type' => 'object',  'properties' => ['id' => ['type' => 'integer'], 'name' => ['type' => 'string']]],
                            'category' => ['type' => 'object',  'properties' => ['id' => ['type' => 'integer'], 'name' => ['type' => 'string']]],
                            'breakdown' => ['$ref' => '#/components/schemas/DonationBreakdown'],
                            'payment_status' => ['type' => 'string',  'enum' => ['pending', 'success', 'failed', 'cancelled', 'refunded']],
                            'payment_id' => ['type' => 'string',  'nullable' => true],
                            'transaction_id' => ['type' => 'string',  'nullable' => true],
                            'payment_method' => ['type' => 'string',  'nullable' => true],
                            'created_at' => ['type' => 'string',  'format' => 'date-time'],
                        ],
                    ],
                    'DonationResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'data' => ['$ref' => '#/components/schemas/Donation'],
                            'created' => ['type' => 'boolean', 'example' => true, 'description' => 'false when an existing record was returned due to idempotency'],
                        ],
                    ],

                    // ── Services ─────────────────────────────────────────────
                    'ServicePricing' => [
                        'type' => 'object',
                        'properties' => [
                            'amount' => ['type' => 'integer', 'nullable' => true, 'example' => 999],
                            'currency' => ['type' => 'string',  'example' => 'INR'],
                            'discount_amount' => ['type' => 'integer', 'nullable' => true],
                            'formatted' => ['type' => 'string',  'nullable' => true, 'example' => '₹999'],
                        ],
                    ],
                    'Service' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer', 'example' => 1],
                            'name' => ['type' => 'string',  'nullable' => true, 'example' => 'Hair Spa'],
                            'title' => ['type' => 'string',  'nullable' => true],
                            'description' => ['type' => 'string',  'nullable' => true],
                            'language' => ['type' => 'string',  'example' => 'en', 'description' => 'Language actually returned'],
                            'fallback_used' => ['type' => 'boolean', 'example' => false],
                            'available_languages' => ['type' => 'array',   'items' => ['type' => 'string'], 'example' => ['en', 'hi']],
                            'primary_image' => ['type' => 'string',  'nullable' => true, 'format' => 'uri'],
                            'gallery' => ['type' => 'array',   'items' => ['type' => 'string', 'format' => 'uri']],
                            'pricing' => ['$ref' => '#/components/schemas/ServicePricing'],
                            'status' => ['type' => 'string',  'enum' => ['active', 'inactive']],
                            'created_at' => ['type' => 'string',  'format' => 'date-time'],
                            'updated_at' => ['type' => 'string',  'format' => 'date-time'],
                        ],
                    ],
                    'ServiceListResponse' => ['type' => 'object', 'properties' => ['data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Service']]]],
                    'ServiceSingleResponse' => ['type' => 'object', 'properties' => ['data' => ['$ref' => '#/components/schemas/Service']]],

                    // ── Panchang ─────────────────────────────────────────────
                    'PanchangLocation' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => ['type' => 'string',  'nullable' => true, 'example' => 'Indore, Madhya Pradesh'],
                            'latitude' => ['type' => 'number',  'format' => 'float', 'example' => 22.72, 'description' => 'Normalized to 2 decimal places'],
                            'longitude' => ['type' => 'number',  'format' => 'float', 'example' => 75.86],
                            'timezone' => ['type' => 'number',  'format' => 'float', 'example' => 5.5],
                        ],
                    ],
                    'PanchangResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'success' => ['type' => 'boolean', 'example' => true],
                            'cached' => ['type' => 'boolean', 'example' => false, 'description' => 'true when served from the database cache, false when freshly fetched from Navamsha API'],
                            'date' => ['type' => 'string',  'format' => 'date', 'example' => '2026-10-01'],
                            'location' => ['$ref' => '#/components/schemas/PanchangLocation'],
                            'data' => ['type' => 'object', 'description' => 'Panchang payload from Navamsha API. Shape varies by `feature`; `panchang_full` includes tithi, nakshatra, yoga, karana, weekday, sun_rise, sun_set, rahu_kaal, abhijit.', 'additionalProperties' => true],
                        ],
                    ],
                    'PanchangError' => [
                        'type' => 'object',
                        'properties' => [
                            'success' => ['type' => 'boolean', 'example' => false],
                            'error' => [
                                'type' => 'object',
                                'properties' => [
                                    'code' => ['type' => 'string', 'example' => 'API_ERROR'],
                                    'message' => ['type' => 'string', 'example' => 'Navamsha API returned HTTP 500'],
                                ],
                            ],
                        ],
                    ],

                    // ── Promotions ───────────────────────────────────────────
                    'PromotionCta' => [
                        'type' => 'object',
                        'properties' => [
                            'type'  => ['type' => 'string', 'description' => 'CTA action type: `url`, `screen`, or `none`', 'example' => 'screen'],
                            'value' => ['type' => 'string', 'description' => 'URL or screen name depending on type', 'example' => 'Pooja'],
                        ],
                    ],
                    'Promotion' => [
                        'type' => 'object',
                        'properties' => [
                            'id'          => ['type' => 'integer', 'example' => 1],
                            'title'       => ['type' => 'string',  'example' => 'Navratri Special Pooja'],
                            'description' => ['type' => 'string',  'example' => 'Book at 20% off. Limited slots.'],
                            'image'       => ['type' => 'string',  'format' => 'uri', 'nullable' => true, 'example' => 'https://krmknd.avark.biz/storage/promotions/navratri.jpg'],
                            'type'        => ['type' => 'string',  'example' => 'banner'],
                            'placement'   => ['type' => 'string',  'example' => 'home'],
                            'audience'    => ['type' => 'string',  'example' => 'all'],
                            'cta'         => ['$ref' => '#/components/schemas/PromotionCta'],
                            'starts_at'   => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
                            'ends_at'     => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
                        ],
                    ],
                    'PromotionsResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'success' => ['type' => 'boolean', 'example' => true],
                            'data'    => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Promotion']],
                        ],
                    ],

                    // ── Samagri ──────────────────────────────────────────────
                    'SamagriCategory' => [
                        'type' => 'object',
                        'properties' => [
                            'id'    => ['type' => 'integer', 'example' => 1],
                            'name'  => ['type' => 'string',  'example' => 'Daily Puja'],
                            'slug'  => ['type' => 'string',  'example' => 'daily-puja'],
                            'image' => ['type' => 'string',  'nullable' => true, 'format' => 'uri'],
                        ],
                    ],
                    'SamagriCategoryListResponse' => [
                        'type' => 'object',
                        'properties' => ['data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/SamagriCategory']]],
                    ],
                    'SamagriProduct' => [
                        'type' => 'object',
                        'properties' => [
                            'id'          => ['type' => 'integer', 'example' => 1],
                            'name'        => ['type' => 'string',  'example' => 'Complete Daily Puja Kit'],
                            'category'    => ['type' => 'string',  'nullable' => true, 'example' => 'Daily Puja'],
                            'category_id' => ['type' => 'integer', 'example' => 1],
                            'description' => ['type' => 'string',  'nullable' => true, 'example' => 'Essential poojan samagri for daily worship.'],
                            'detail'      => ['type' => 'string',  'nullable' => true, 'example' => 'A neatly packed daily puja kit...'],
                            'price'       => ['type' => 'number',  'format' => 'float', 'example' => 499.00],
                            'mrp'         => ['type' => 'number',  'format' => 'float', 'nullable' => true, 'example' => 699.00],
                            'unit'        => ['type' => 'string',  'nullable' => true, 'example' => '1 kit'],
                            'badge'       => ['type' => 'string',  'nullable' => true, 'example' => 'Best Seller'],
                            'rating'      => ['type' => 'number',  'format' => 'float', 'example' => 4.8],
                            'reviews'     => ['type' => 'integer', 'example' => 384],
                            'stock'       => ['type' => 'string',  'enum' => ['In stock', 'Low stock', 'Out of stock'], 'example' => 'In stock', 'description' => 'Derived from inventory.available_stock: >10 = In stock, 1-10 = Low stock, 0 = Out of stock'],
                            'uses'        => ['type' => 'array',   'items' => ['type' => 'string'], 'example' => ['Daily puja', 'Home mandir']],
                            'contents'    => ['type' => 'array',   'items' => ['type' => 'string'], 'example' => ['Roli', 'Akshat', 'Camphor']],
                            'image'       => ['type' => 'string',  'nullable' => true, 'format' => 'uri', 'description' => 'Primary image URL'],
                        ],
                    ],
                    'SamagriProductWithImages' => [
                        'allOf' => [
                            ['$ref' => '#/components/schemas/SamagriProduct'],
                            ['type' => 'object', 'properties' => [
                                'images' => ['type' => 'array', 'items' => ['type' => 'string', 'format' => 'uri'], 'description' => 'All product images (only in detail response)'],
                            ]],
                        ],
                    ],
                    'SamagriProductListResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/SamagriProduct']],
                            'meta' => [
                                'type' => 'object',
                                'properties' => [
                                    'current_page' => ['type' => 'integer', 'example' => 1],
                                    'last_page'    => ['type' => 'integer', 'example' => 3],
                                    'total'        => ['type' => 'integer', 'example' => 52],
                                ],
                            ],
                        ],
                    ],
                    'SamagriProductDetailResponse' => [
                        'type' => 'object',
                        'properties' => ['data' => ['$ref' => '#/components/schemas/SamagriProductWithImages']],
                    ],

                    // ── Cart ─────────────────────────────────────────────────
                    'CartItem' => [
                        'type' => 'object',
                        'properties' => [
                            'id'       => ['type' => 'integer', 'example' => 3, 'description' => 'cart_items.id'],
                            'quantity' => ['type' => 'integer', 'example' => 2],
                            'product'  => ['$ref' => '#/components/schemas/SamagriProduct'],
                        ],
                    ],
                    'CartListResponse' => [
                        'type' => 'object',
                        'properties' => ['data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/CartItem']]],
                    ],
                    'CartItemResponse' => [
                        'type' => 'object',
                        'properties' => ['data' => ['$ref' => '#/components/schemas/CartItem']],
                    ],
                    'CartItemNullableResponse' => [
                        'type' => 'object',
                        'properties' => ['data' => ['nullable' => true, 'oneOf' => [['$ref' => '#/components/schemas/CartItem'], ['type' => 'null']]]],
                    ],
                    'AddToCartRequest' => [
                        'type' => 'object',
                        'required' => ['product_id'],
                        'properties' => [
                            'product_id' => ['type' => 'integer', 'example' => 1],
                            'quantity'   => ['type' => 'integer', 'minimum' => 1, 'default' => 1, 'example' => 2],
                        ],
                    ],
                    'UpdateCartItemRequest' => [
                        'type' => 'object',
                        'required' => ['quantity'],
                        'properties' => [
                            'quantity' => ['type' => 'integer', 'minimum' => 0, 'example' => 3, 'description' => '0 removes the item'],
                        ],
                    ],

                    // ── Orders ────────────────────────────────────────────────
                    'OrderItem' => [
                        'type' => 'object',
                        'properties' => [
                            'id'         => ['type' => 'integer', 'example' => 7],
                            'product_id' => ['type' => 'integer', 'example' => 1],
                            'name'       => ['type' => 'string',  'example' => 'Complete Daily Puja Kit'],
                            'price'      => ['type' => 'number',  'format' => 'float', 'example' => 499.00],
                            'quantity'   => ['type' => 'integer', 'example' => 2],
                            'subtotal'   => ['type' => 'number',  'format' => 'float', 'example' => 998.00],
                        ],
                    ],
                    'Order' => [
                        'type' => 'object',
                        'properties' => [
                            'id'             => ['type' => 'integer', 'example' => 42],
                            'status'         => ['type' => 'string',  'enum' => ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'], 'example' => 'pending'],
                            'name'           => ['type' => 'string',  'example' => 'Rahul Sharma'],
                            'phone'          => ['type' => 'string',  'example' => '9876543210'],
                            'address'        => ['type' => 'string',  'nullable' => true, 'example' => '12 MG Road, Indore'],
                            'notes'          => ['type' => 'string',  'nullable' => true],
                            'payment_method' => ['type' => 'string',  'enum' => ['cod', 'online'], 'example' => 'cod'],
                            'subtotal'       => ['type' => 'number',  'format' => 'float', 'example' => 998.00],
                            'platform_fee'   => ['type' => 'number',  'format' => 'float', 'example' => 10.00],
                            'gst_amount'     => ['type' => 'number',  'format' => 'float', 'example' => 1.80],
                            'total_amount'   => ['type' => 'number',  'format' => 'float', 'example' => 1009.80],
                            'items'          => ['type' => 'array',   'items' => ['$ref' => '#/components/schemas/OrderItem']],
                            'created_at'     => ['type' => 'string',  'format' => 'date-time'],
                        ],
                    ],
                    'OrderResponse' => [
                        'type' => 'object',
                        'properties' => ['data' => ['$ref' => '#/components/schemas/Order']],
                    ],
                    'OrderListResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Order']],
                            'meta' => [
                                'type' => 'object',
                                'properties' => [
                                    'current_page' => ['type' => 'integer', 'example' => 1],
                                    'last_page'    => ['type' => 'integer', 'example' => 2],
                                    'total'        => ['type' => 'integer', 'example' => 31],
                                ],
                            ],
                        ],
                    ],
                    'CreateOrderRequest' => [
                        'type' => 'object',
                        'required' => ['name', 'phone', 'payment_method', 'platform_fee', 'gst_amount', 'items'],
                        'properties' => [
                            'name'           => ['type' => 'string', 'maxLength' => 100, 'example' => 'Rahul Sharma'],
                            'phone'          => ['type' => 'string', 'maxLength' => 15, 'example' => '9876543210'],
                            'address'        => ['type' => 'string', 'maxLength' => 500, 'nullable' => true, 'example' => '12 MG Road, Indore, MP'],
                            'notes'          => ['type' => 'string', 'maxLength' => 1000, 'nullable' => true],
                            'payment_method' => ['type' => 'string', 'enum' => ['cod', 'online'], 'example' => 'cod'],
                            'platform_fee'   => ['type' => 'number', 'format' => 'float', 'minimum' => 0, 'example' => 10.00],
                            'gst_amount'     => ['type' => 'number', 'format' => 'float', 'minimum' => 0, 'example' => 1.80],
                            'items'          => [
                                'type' => 'array',
                                'minItems' => 1,
                                'items' => [
                                    'type' => 'object',
                                    'required' => ['product_id', 'quantity'],
                                    'properties' => [
                                        'product_id' => ['type' => 'integer', 'example' => 1],
                                        'quantity'   => ['type' => 'integer', 'minimum' => 1, 'example' => 2],
                                    ],
                                ],
                            ],
                        ],
                    ],

                    // ── Errors ───────────────────────────────────────────────
                    'ErrorMessage' => [
                        'type' => 'object',
                        'properties' => ['message' => ['type' => 'string', 'example' => 'No query results for model.']],
                    ],
                    'ValidationErrors' => [
                        'type' => 'object',
                        'properties' => [
                            'message' => ['type' => 'string'],
                            'errors' => ['type' => 'object', 'additionalProperties' => ['type' => 'array', 'items' => ['type' => 'string']]],
                        ],
                    ],
                ],
                'securitySchemes' => [
                    'BearerAuth' => [
                        'type'         => 'http',
                        'scheme'       => 'bearer',
                        'bearerFormat' => 'Sanctum personal access token',
                    ],
                ],
            ],
        ];

        return response()->json($spec)
            ->header('Access-Control-Allow-Origin', '*');
    }
}
