<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        // Resolve Mayank guruji ID — used as default for services & donation CTAs
        $mayankUserId = User::where('email', 'mayank@krmknd.com')->value('id');
        $mayankGuruId = Guru::when($mayankUserId, fn ($q) => $q->where('user_id', $mayankUserId))
            ->where('name', 'Pt. Mayank')
            ->value('id');

        // Resolve real IDs so cta_value points to actual DB records
        $sareesCatId   = ProductCategory::where('slug', 'sarees-vastras')->value('id');
        $shringarCatId = ProductCategory::where('slug', 'shringar-items')->value('id');
        $prasadCatId   = ProductCategory::where('slug', 'prasad-offerings')->value('id');
        $pujaCatId     = ProductCategory::where('slug', 'puja-accessories')->value('id');

        $prasadProductId = Product::where('name', 'Panchamrit Pack')->value('id');
        $kalashProductId = Product::where('name', 'Kalash with Lid (Brass)')->value('id');
        $sareesProductId = Product::where('name', 'Red Silk Saree (Navratri Special)')->value('id');

        $promotions = [
            [
                'title'       => 'Mataji Navratri Poojan',
                'description' => 'Celebrate the nine divine nights of Navratri with a special Mataji Poojan ceremony. Book your slot and receive blessings for health, wealth and prosperity. Our Guruji will perform the rituals with full Vedic procedure.',
                'type'        => 'promotion',
                'placement'   => 'home_top',
                'status'      => 'active',
                'cta_type'    => 'services',
                'cta_value'   => (string) ($mayankGuruId ?? ''),
                'audience'    => 'all',
                'sort_order'  => 1,
                'translations' => [
                    'hi' => [
                        'title'       => 'माताजी नवरात्रि पूजन',
                        'description' => 'नवरात्रि के पावन नौ दिनों में माताजी के विशेष पूजन में भाग लें। अपना स्थान बुक करें और स्वास्थ्य, धन और समृद्धि का आशीर्वाद प्राप्त करें। हमारे गुरुजी पूर्ण वैदिक विधि से अनुष्ठान करेंगे।',
                    ],
                ],
            ],
            [
                'title'       => 'Mataji Vastra & Shringar Offer',
                'description' => 'Adorn Mataji with beautiful traditional vastra, jewellery and shringar items. Special combo packs available at discounted prices. All items are handpicked and blessed by our Guruji before dispatch.',
                'type'        => 'offer',
                'placement'   => 'shop',
                'status'      => 'active',
                'cta_type'    => 'category',
                'cta_value'   => (string) ($shringarCatId ?? ''),
                'audience'    => 'all',
                'sort_order'  => 2,
                'translations' => [
                    'hi' => [
                        'title'       => 'माताजी वस्त्र और श्रृंगार ऑफर',
                        'description' => 'माताजी को सुंदर पारंपरिक वस्त्र, आभूषण और श्रृंगार सामग्री से सजाएं। विशेष कॉम्बो पैक रियायती मूल्य पर उपलब्ध। सभी वस्तुएं हमारे गुरुजी द्वारा भेजने से पहले अभिमंत्रित की जाती हैं।',
                    ],
                ],
            ],
            [
                'title'       => 'Daily Mataji Aarti Darshan',
                'description' => 'Join the live Mataji Aarti streamed directly from our temple every morning at 6:00 AM and evening at 7:30 PM. Get notified on your phone and participate from anywhere in the world.',
                'type'        => 'announcement',
                'placement'   => 'home_middle',
                'status'      => 'active',
                'cta_type'    => 'url',
                'cta_value'   => 'https://krmknd.avark.biz/aarti',
                'audience'    => 'all',
                'sort_order'  => 3,
                'translations' => [
                    'hi' => [
                        'title'       => 'दैनिक माताजी आरती दर्शन',
                        'description' => 'हमारे मंदिर से सीधे प्रसारित माताजी की आरती में जुड़ें — प्रतिदिन सुबह 6:00 बजे और शाम 7:30 बजे। अपने फोन पर सूचना प्राप्त करें और दुनिया के किसी भी कोने से भाग लें।',
                    ],
                ],
            ],
            [
                'title'       => 'Mataji Prasad Home Delivery',
                'description' => 'Receive blessed Mataji Prasad at your doorstep. Place your order before 10:00 AM for same-day delivery within city limits. Prasad includes panchamrit, sindoor, chunri and fresh flowers.',
                'type'        => 'promotion',
                'placement'   => 'pooja',
                'status'      => 'active',
                'cta_type'    => 'product',
                'cta_value'   => (string) ($prasadProductId ?? ''),
                'audience'    => 'user',
                'sort_order'  => 4,
                'translations' => [
                    'hi' => [
                        'title'       => 'माताजी प्रसाद होम डिलीवरी',
                        'description' => 'माताजी का अभिमंत्रित प्रसाद अपने घर पर प्राप्त करें। शहर की सीमा के भीतर उसी दिन डिलीवरी के लिए सुबह 10:00 बजे से पहले ऑर्डर करें। प्रसाद में पंचामृत, सिंदूर, चुनरी और ताजे फूल शामिल हैं।',
                    ],
                ],
            ],
            [
                'title'       => 'Navratri Special Kalash Sthapana Booking',
                'description' => 'Book your Kalash Sthapana for Navratri and start the nine-day celebration with divine blessings. Limited slots available. Includes Ghatasthapana, Nav Durga Poojan and Havan on Ashtami.',
                'type'        => 'banner',
                'placement'   => 'popup',
                'status'      => 'active',
                'cta_type'    => 'product',
                'cta_value'   => (string) ($kalashProductId ?? ''),
                'audience'    => 'all',
                'sort_order'  => 5,
                'translations' => [
                    'hi' => [
                        'title'       => 'नवरात्रि विशेष कलश स्थापना बुकिंग',
                        'description' => 'नवरात्रि के लिए अपनी कलश स्थापना बुक करें और नौ दिवसीय उत्सव की शुभ शुरुआत करें। सीमित स्थान उपलब्ध। घटस्थापना, नव दुर्गा पूजन और अष्टमी को हवन शामिल है।',
                    ],
                ],
            ],
            [
                'title'       => 'Mataji Chunri & Chadar Arpan',
                'description' => 'Offer a sacred Chunri or Chadar to Mataji in your name. Our Guruji will perform the Arpan ceremony at the temple and send you a blessed photo and video as confirmation of the ritual.',
                'type'        => 'offer',
                'placement'   => 'home_top',
                'status'      => 'active',
                'cta_type'    => 'category',
                'cta_value'   => (string) ($sareesCatId ?? ''),
                'audience'    => 'all',
                'sort_order'  => 6,
                'translations' => [
                    'hi' => [
                        'title'       => 'माताजी चुनरी और चादर अर्पण',
                        'description' => 'आपके नाम से माताजी को पवित्र चुनरी या चादर अर्पित करें। हमारे गुरुजी मंदिर में अर्पण समारोह करेंगे और अनुष्ठान की पुष्टि के रूप में आपको एक अभिमंत्रित फोटो और वीडियो भेजेंगे।',
                    ],
                ],
            ],
            [
                'title'       => 'Support Guruji – Make a Donation',
                'description' => 'Your donation directly supports Pt. Mayank in continuing his sacred work — performing daily pujas, Navratri anushthan and community sevas at the temple. Every contribution, big or small, carries divine merit.',
                'type'        => 'promotion',
                'placement'   => 'home_middle',
                'status'      => 'active',
                'cta_type'    => 'donation',
                'cta_value'   => (string) ($mayankGuruId ?? ''),
                'audience'    => 'all',
                'sort_order'  => 7,
                'translations' => [
                    'hi' => [
                        'title'       => 'गुरुजी को सहयोग करें – दान करें',
                        'description' => 'आपका दान पं. मयंक को उनके पवित्र कार्य — दैनिक पूजा, नवरात्रि अनुष्ठान और मंदिर में सामुदायिक सेवाएं — जारी रखने में सीधे सहायता करता है। हर योगदान, चाहे बड़ा हो या छोटा, दिव्य पुण्य देता है।',
                    ],
                ],
            ],
        ];

        foreach ($promotions as $data) {
            Promotion::updateOrCreate(
                ['title' => $data['title']],
                $data
            );
        }

        $this->command->info('PromotionSeeder: '.count($promotions).' records inserted/updated.');
    }
}
