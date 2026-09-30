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
        $base = rtrim(config('app.url'), '/');

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
                    . "- ✦ **Services** — multilingual service catalogue",
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
