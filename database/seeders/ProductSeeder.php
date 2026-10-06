<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Sarees & Vastras',    'description' => 'Silk and cotton sarees for Mataji offerings'],
            ['name' => 'Shringar Items',       'description' => 'Jewellery and adornments for deity'],
            ['name' => 'Puja Accessories',     'description' => 'Diyas, incense, camphor and puja essentials'],
            ['name' => 'Prasad & Offerings',   'description' => 'Sweets, fruits and offering items'],
            ['name' => 'Devotional Books',     'description' => 'Chalisa, aarti sangrah and scriptures'],
        ];

        $catMap = [];
        foreach ($categories as $cat) {
            $record = ProductCategory::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                array_merge($cat, ['status' => 'active', 'sort_order' => 0])
            );
            $catMap[$cat['name']] = $record->id;
        }

        $products = [
            // Sarees & Vastras
            ['name' => 'Red Silk Saree (Navratri Special)',   'category' => 'Sarees & Vastras',  'price' => 1499, 'compare_at_price' => 1999, 'product_type' => 'MATAJI_OFFERING', 'offering_eligible' => true],
            ['name' => 'Yellow Cotton Saree',                 'category' => 'Sarees & Vastras',  'price' => 799,  'compare_at_price' => 999,  'product_type' => 'MATAJI_OFFERING', 'offering_eligible' => true],
            ['name' => 'Kanchipuram Silk Saree',              'category' => 'Sarees & Vastras',  'price' => 3499, 'compare_at_price' => 4500, 'product_type' => 'MATAJI_OFFERING', 'offering_eligible' => true],
            ['name' => 'Pink Chanderi Saree',                 'category' => 'Sarees & Vastras',  'price' => 999,  'compare_at_price' => 1299, 'product_type' => 'MATAJI_OFFERING', 'offering_eligible' => true],

            // Shringar Items
            ['name' => 'Maang Tikka (Gold Plated)',           'category' => 'Shringar Items',    'price' => 350,  'compare_at_price' => 499,  'product_type' => 'NORMAL', 'offering_eligible' => true],
            ['name' => 'Flower Garland Set',                  'category' => 'Shringar Items',    'price' => 150,  'compare_at_price' => null,  'product_type' => 'NORMAL', 'offering_eligible' => true],
            ['name' => 'Silver Payal (Anklet)',               'category' => 'Shringar Items',    'price' => 599,  'compare_at_price' => 799,  'product_type' => 'NORMAL', 'offering_eligible' => true],

            // Puja Accessories
            ['name' => 'Brass Diya Set (12 pcs)',             'category' => 'Puja Accessories',  'price' => 249,  'compare_at_price' => 350,  'product_type' => 'NORMAL', 'offering_eligible' => false],
            ['name' => 'Premium Agarbatti Box',               'category' => 'Puja Accessories',  'price' => 99,   'compare_at_price' => null,  'product_type' => 'NORMAL', 'offering_eligible' => false],
            ['name' => 'Camphor Tablets (100g)',              'category' => 'Puja Accessories',  'price' => 79,   'compare_at_price' => null,  'product_type' => 'NORMAL', 'offering_eligible' => false],
            ['name' => 'Kalash with Lid (Brass)',             'category' => 'Puja Accessories',  'price' => 450,  'compare_at_price' => 599,  'product_type' => 'NORMAL', 'offering_eligible' => true],

            // Prasad & Offerings
            ['name' => 'Panchamrit Pack',                     'category' => 'Prasad & Offerings','price' => 199,  'compare_at_price' => null,  'product_type' => 'NORMAL', 'offering_eligible' => true],
            ['name' => 'Mishri & Batasha Box',                'category' => 'Prasad & Offerings','price' => 149,  'compare_at_price' => null,  'product_type' => 'NORMAL', 'offering_eligible' => true],
            ['name' => 'Dry Fruit Prasad Box (250g)',         'category' => 'Prasad & Offerings','price' => 399,  'compare_at_price' => 499,  'product_type' => 'NORMAL', 'offering_eligible' => true],

            // Devotional Books
            ['name' => 'Durga Chalisa & Aarti Sangrah',       'category' => 'Devotional Books',  'price' => 49,   'compare_at_price' => null,  'product_type' => 'NORMAL', 'offering_eligible' => false],
            ['name' => 'Navratri Vrat Katha',                 'category' => 'Devotional Books',  'price' => 39,   'compare_at_price' => null,  'product_type' => 'NORMAL', 'offering_eligible' => false],
        ];

        $count = 0;
        foreach ($products as $i => $p) {
            $catId = $catMap[$p['category']];
            Product::firstOrCreate(
                ['name' => $p['name']],
                [
                    'product_code'      => 'KRMK-'.str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'sku'               => 'SKU-'.strtoupper(Str::random(6)),
                    'category_id'       => $catId,
                    'price'             => $p['price'],
                    'compare_at_price'  => $p['compare_at_price'],
                    'product_type'      => $p['product_type'],
                    'offering_eligible' => $p['offering_eligible'],
                    'resale_eligible'   => true,
                    'status'            => 'active',
                ]
            );
            $count++;
        }

        $this->command->info("ProductSeeder: {$count} products + ".count($categories)." categories inserted/updated.");
    }
}
