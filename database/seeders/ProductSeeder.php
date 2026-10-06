<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Structure: parent category => [ subcategories => [ products ] ]
        $tree = [
            'Sarees & Vastras' => [
                'description' => 'Traditional sarees and fabrics for Mataji offerings',
                'subcategories' => [
                    'Silk Sarees' => [
                        ['name' => 'Red Silk Saree (Navratri Special)', 'price' => 1499, 'compare_at_price' => 1999, 'product_type' => 'MATAJI_OFFERING', 'offering_eligible' => true,  'stock' => 25],
                        ['name' => 'Kanchipuram Silk Saree',            'price' => 3499, 'compare_at_price' => 4500, 'product_type' => 'MATAJI_OFFERING', 'offering_eligible' => true,  'stock' => 10],
                        ['name' => 'Banarasi Silk Saree',               'price' => 2799, 'compare_at_price' => 3500, 'product_type' => 'MATAJI_OFFERING', 'offering_eligible' => true,  'stock' => 15],
                    ],
                    'Cotton & Chanderi Sarees' => [
                        ['name' => 'Yellow Cotton Saree',   'price' => 799,  'compare_at_price' => 999,  'product_type' => 'MATAJI_OFFERING', 'offering_eligible' => true, 'stock' => 40],
                        ['name' => 'Pink Chanderi Saree',   'price' => 999,  'compare_at_price' => 1299, 'product_type' => 'MATAJI_OFFERING', 'offering_eligible' => true, 'stock' => 30],
                        ['name' => 'Orange Georgette Saree','price' => 1199, 'compare_at_price' => 1499, 'product_type' => 'MATAJI_OFFERING', 'offering_eligible' => true, 'stock' => 20],
                    ],
                    'Chunri & Dupatta' => [
                        ['name' => 'Red Chunri (Small)',  'price' => 299, 'compare_at_price' => null, 'product_type' => 'MATAJI_OFFERING', 'offering_eligible' => true, 'stock' => 100],
                        ['name' => 'Printed Chunri Set',  'price' => 499, 'compare_at_price' => 599,  'product_type' => 'MATAJI_OFFERING', 'offering_eligible' => true, 'stock' => 50],
                    ],
                ],
            ],
            'Shringar Items' => [
                'description' => 'Jewellery and adornments for deity',
                'subcategories' => [
                    'Jewellery' => [
                        ['name' => 'Maang Tikka (Gold Plated)',  'price' => 350, 'compare_at_price' => 499, 'product_type' => 'NORMAL', 'offering_eligible' => true, 'stock' => 30],
                        ['name' => 'Silver Payal (Anklet)',       'price' => 599, 'compare_at_price' => 799, 'product_type' => 'NORMAL', 'offering_eligible' => true, 'stock' => 20],
                        ['name' => 'Necklace Set (Deity Size)',   'price' => 799, 'compare_at_price' => 999, 'product_type' => 'NORMAL', 'offering_eligible' => true, 'stock' => 15],
                    ],
                    'Flower Garlands' => [
                        ['name' => 'Fresh Marigold Garland',   'price' => 99,  'compare_at_price' => null, 'product_type' => 'NORMAL', 'offering_eligible' => true, 'stock' => 200],
                        ['name' => 'Rose Petal Garland',        'price' => 149, 'compare_at_price' => null, 'product_type' => 'NORMAL', 'offering_eligible' => true, 'stock' => 150],
                        ['name' => 'Flower Garland Set (5pc)', 'price' => 299, 'compare_at_price' => 350,  'product_type' => 'NORMAL', 'offering_eligible' => true, 'stock' => 80],
                    ],
                ],
            ],
            'Puja Accessories' => [
                'description' => 'Diyas, incense, camphor and puja essentials',
                'subcategories' => [
                    'Diyas & Lamps' => [
                        ['name' => 'Brass Diya Set (12 pcs)',  'price' => 249, 'compare_at_price' => 350,  'product_type' => 'NORMAL', 'offering_eligible' => false, 'stock' => 60],
                        ['name' => 'Silver Diya (Single)',      'price' => 199, 'compare_at_price' => null, 'product_type' => 'NORMAL', 'offering_eligible' => false, 'stock' => 35],
                        ['name' => 'Kalash with Lid (Brass)',   'price' => 450, 'compare_at_price' => 599,  'product_type' => 'NORMAL', 'offering_eligible' => true,  'stock' => 25],
                    ],
                    'Incense & Camphor' => [
                        ['name' => 'Premium Agarbatti Box',  'price' => 99, 'compare_at_price' => null, 'product_type' => 'NORMAL', 'offering_eligible' => false, 'stock' => 120],
                        ['name' => 'Camphor Tablets (100g)', 'price' => 79, 'compare_at_price' => null, 'product_type' => 'NORMAL', 'offering_eligible' => false, 'stock' => 100],
                        ['name' => 'Dhoop Cones Pack',       'price' => 59, 'compare_at_price' => null, 'product_type' => 'NORMAL', 'offering_eligible' => false, 'stock' => 90],
                    ],
                ],
            ],
            'Prasad & Offerings' => [
                'description' => 'Sweets, fruits and offering items',
                'subcategories' => [
                    'Panchamrit & Naivedya' => [
                        ['name' => 'Panchamrit Pack',             'price' => 199, 'compare_at_price' => null, 'product_type' => 'NORMAL', 'offering_eligible' => true, 'stock' => 75],
                        ['name' => 'Mishri & Batasha Box',        'price' => 149, 'compare_at_price' => null, 'product_type' => 'NORMAL', 'offering_eligible' => true, 'stock' => 80],
                        ['name' => 'Dry Fruit Prasad Box (250g)', 'price' => 399, 'compare_at_price' => 499,  'product_type' => 'NORMAL', 'offering_eligible' => true, 'stock' => 45],
                    ],
                    'Sindoor & Kumkum' => [
                        ['name' => 'Natural Sindoor Pack', 'price' => 49, 'compare_at_price' => null, 'product_type' => 'NORMAL', 'offering_eligible' => true, 'stock' => 150],
                        ['name' => 'Kumkum Powder (50g)',  'price' => 39, 'compare_at_price' => null, 'product_type' => 'NORMAL', 'offering_eligible' => true, 'stock' => 150],
                    ],
                ],
            ],
            'Devotional Books' => [
                'description' => 'Chalisa, aarti sangrah and scriptures',
                'subcategories' => [
                    'Chalisa & Aarti' => [
                        ['name' => 'Durga Chalisa & Aarti Sangrah', 'price' => 49, 'compare_at_price' => null, 'product_type' => 'NORMAL', 'offering_eligible' => false, 'stock' => 200],
                        ['name' => 'Navratri Vrat Katha',           'price' => 39, 'compare_at_price' => null, 'product_type' => 'NORMAL', 'offering_eligible' => false, 'stock' => 150],
                    ],
                    'Scriptures' => [
                        ['name' => 'Devi Mahatmyam (Hindi)', 'price' => 149, 'compare_at_price' => null, 'product_type' => 'NORMAL', 'offering_eligible' => false, 'stock' => 60],
                        ['name' => 'Shrimad Devi Bhagwat',   'price' => 299, 'compare_at_price' => 399,  'product_type' => 'NORMAL', 'offering_eligible' => false, 'stock' => 40],
                    ],
                ],
            ],
        ];

        // Archive seed-generated products; use withTrashed to catch already-archived runs
        Product::withTrashed()->where('product_code', 'like', 'KRMK-%')->each(function ($p) {
            // Randomise unique fields so new inserts don't collide with archived rows
            $p->forceFill(['sku' => 'DEL-'.uniqid(), 'product_code' => 'DEL-'.uniqid()])->save();
            $p->delete();
        });

        $productCount = 0;
        $catCount     = 0;
        $code         = 1;

        foreach ($tree as $parentName => $parentData) {
            $parent = ProductCategory::firstOrCreate(
                ['slug' => Str::slug($parentName)],
                [
                    'parent_id'   => null,
                    'name'        => $parentName,
                    'description' => $parentData['description'],
                    'status'      => 'active',
                    'sort_order'  => 0,
                ]
            );
            $catCount++;

            foreach ($parentData['subcategories'] as $subName => $products) {
                $sub = ProductCategory::firstOrCreate(
                    ['slug' => Str::slug($subName)],
                    [
                        'parent_id'   => $parent->id,
                        'name'        => $subName,
                        'description' => null,
                        'status'      => 'active',
                        'sort_order'  => 0,
                    ]
                );
                $catCount++;

                foreach ($products as $p) {
                    $product = Product::create([
                        'name'             => $p['name'],
                        'product_code'     => 'KRMK-'.str_pad($code, 4, '0', STR_PAD_LEFT),
                        'sku'              => 'SKU-'.strtoupper(substr(md5($p['name']), 0, 6)),
                        'category_id'      => $sub->id,
                        'price'            => $p['price'],
                        'compare_at_price' => $p['compare_at_price'],
                        'product_type'     => $p['product_type'],
                        'offering_eligible'=> $p['offering_eligible'],
                        'resale_eligible'  => true,
                        'status'           => 'active',
                    ]);

                    $stock = $p['stock'] ?? 0;
                    Inventory::updateOrCreate(
                        ['product_id' => $product->id],
                        [
                            'total_stock'     => $stock,
                            'available_stock' => $stock,
                            'reserved_stock'  => 0,
                            'sold_stock'      => 0,
                            'offering_stock'  => 0,
                            'resale_stock'    => 0,
                        ]
                    );

                    $code++;
                    $productCount++;
                }
            }
        }

        $this->command->info("ProductSeeder: {$productCount} products + {$catCount} categories/subcategories inserted/updated.");
    }
}
