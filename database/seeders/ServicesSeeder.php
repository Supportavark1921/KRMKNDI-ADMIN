<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'translations' => [
                    'en' => [
                        'name'        => 'Shobhagya Laxmi Poojan',
                        'title'       => 'Invoke Goddess Lakshmi\'s Blessings of Wealth & Fortune',
                        'description' => 'Shobhagya Laxmi Poojan is a sacred Vedic ritual performed to invoke the grace of Goddess Lakshmi — the deity of wealth, prosperity, and good fortune. The puja includes chanting of Shri Sukta, offering of lotus flowers, kumkum, and sweets, and lighting of ghee diyas. Ideal for new business ventures, housewarming, and attracting financial abundance. Pandit Ji performs this puja with full Vedic rites for your family\'s long-lasting prosperity.',
                    ],
                    'hi' => [
                        'name'        => 'सौभाग्य लक्ष्मी पूजन',
                        'title'       => 'धन, समृद्धि और सौभाग्य के लिए माँ लक्ष्मी का आह्वान',
                        'description' => 'सौभाग्य लक्ष्मी पूजन एक पवित्र वैदिक अनुष्ठान है जो धन, वैभव और सौभाग्य की देवी माँ लक्ष्मी की कृपा प्राप्त करने के लिए किया जाता है। इस पूजा में श्री सूक्त का पाठ, कमल पुष्प, कुमकुम और मिष्ठान्न का अर्पण तथा घी के दीपक जलाना सम्मिलित है। नवीन व्यापार, गृह प्रवेश एवं आर्थिक समृद्धि के लिए यह पूजन अत्यंत शुभकारी है। पंडित जी आपके परिवार की दीर्घकालीन समृद्धि के लिए पूर्ण वैदिक विधि-विधान से यह पूजन संपन्न करते हैं।',
                    ],
                ],
                'images'  => [
                    'primary' => 'services/shobhagya-laxmi-poojan.jpg',
                    'gallery' => [
                        'services/shobhagya-laxmi-poojan.jpg',
                    ],
                ],
                'pricing' => [
                    'amount'          => 5100,
                    'currency'        => 'INR',
                    'discount_amount' => null,
                ],
                'status'  => 'active',
            ],

            [
                'translations' => [
                    'en' => [
                        'name'        => 'Mahavrat Kalp Anushthan',
                        'title'       => 'Powerful 40-Day Vedic Anushthan for Deep Spiritual Transformation',
                        'description' => 'Mahavrat Kalp Anushthan is an intensive 40-day Vedic anushthan (continuous ritual discipline) performed for complete spiritual purification, removal of doshas, and fulfillment of deep desires. Pandit Ji undertakes strict vows of celibacy, sattvic diet, and daily Vedic recitation across the entire period. This anushthan is prescribed for overcoming severe planetary afflictions, chronic illness, persistent obstacles in life, or seeking profound spiritual advancement. Each day includes havan, mantra japa, and tarpan. A rare and potent ritual — performed in limited sessions per year.',
                    ],
                    'hi' => [
                        'name'        => 'महाव्रत कल्प अनुष्ठान',
                        'title'       => 'गहन आध्यात्मिक परिवर्तन हेतु ४०-दिवसीय वैदिक अनुष्ठान',
                        'description' => 'महाव्रत कल्प अनुष्ठान एक ४०-दिवसीय गहन वैदिक साधना है जो पूर्ण आत्म-शुद्धि, दोष-निवारण और गहरी मनोकामनाओं की पूर्ति के लिए की जाती है। पंडित जी इस संपूर्ण अवधि में ब्रह्मचर्य, सात्विक आहार और दैनिक वैदिक पाठ का कठोर पालन करते हैं। गंभीर ग्रह-पीड़ा, दीर्घकालीन रोग, जीवन में बाधाओं के निवारण या गहन आध्यात्मिक उन्नति के लिए यह अनुष्ठान विशेष रूप से निर्धारित है। प्रतिदिन हवन, मंत्र जप और तर्पण किया जाता है। यह एक दुर्लभ एवं अत्यंत शक्तिशाली अनुष्ठान है — वर्ष में सीमित सत्रों में आयोजित।',
                    ],
                ],
                'images'  => [
                    'primary' => 'services/mahavrat-kalp-anushthan.jpg',
                    'gallery' => [
                        'services/mahavrat-kalp-anushthan.jpg',
                    ],
                ],
                'pricing' => [
                    'amount'          => 51000,
                    'currency'        => 'INR',
                    'discount_amount' => null,
                ],
                'status'  => 'active',
            ],

            [
                'translations' => [
                    'en' => [
                        'name'        => 'Lalita Sahastrachan',
                        'title'       => 'Recitation of the Thousand Names of Goddess Lalita for Grace & Liberation',
                        'description' => 'Lalita Sahastrachan (Lalita Sahasranama parayana) is the sacred recitation of the thousand divine names of Goddess Lalita Tripura Sundari — the supreme form of Shakti. Each name is a mantra in itself, bestowing beauty, intelligence, marital harmony, spiritual liberation (moksha), and protection from negative energies. Performed with fresh flowers, bilva leaves, kumkum abhishek, and deepam, this puja is especially auspicious on Fridays and during Navratri. Recommended for health, family harmony, removal of black magic, and spiritual progress.',
                    ],
                    'hi' => [
                        'name'        => 'ललिता सहस्रचन',
                        'title'       => 'माँ ललिता के सहस्र नामों का पाठ — कृपा एवं मोक्ष हेतु',
                        'description' => 'ललिता सहस्रचन (ललिता सहस्रनाम पारायण) देवी ललिता त्रिपुरा सुंदरी — शक्ति के परम स्वरूप — के एक हजार दिव्य नामों का पवित्र पाठ है। प्रत्येक नाम स्वयं एक मंत्र है जो सौंदर्य, बुद्धि, वैवाहिक सौहार्द, आध्यात्मिक मुक्ति (मोक्ष) और नकारात्मक शक्तियों से सुरक्षा प्रदान करता है। ताजे पुष्प, बिल्वपत्र, कुमकुम अभिषेक और दीपम के साथ संपन्न यह पूजा शुक्रवार और नवरात्रि में विशेष रूप से शुभ है। स्वास्थ्य, पारिवारिक सौहार्द, काले जादू के निवारण और आध्यात्मिक उन्नति के लिए अनुशंसित।',
                    ],
                ],
                'images'  => [
                    'primary' => 'services/lalita-sahastrachan.jpg',
                    'gallery' => [
                        'services/lalita-sahastrachan.jpg',
                    ],
                ],
                'pricing' => [
                    'amount'          => 3100,
                    'currency'        => 'INR',
                    'discount_amount' => null,
                ],
                'status'  => 'active',
            ],

            [
                'translations' => [
                    'en' => [
                        'name'        => 'Lalita Astottar Pooja',
                        'title'       => '108 Names of Goddess Lalita — Blessings for Prosperity & Well-being',
                        'description' => 'Lalita Astottar Pooja is the recitation and ritual worship using the 108 sacred names (Ashtottara Shatanamavali) of Goddess Lalita. Shorter and more accessible than the Sahastrachan, this puja is ideal for weekly or monthly observance. It bestows blessings of good health, success in endeavours, harmonious relationships, and spiritual merit. The puja includes kumkum archana, pushpa archana, naivedya, and deepam. Particularly beneficial for women seeking wellbeing and protection, and for households seeking the continuous grace of the Divine Mother.',
                    ],
                    'hi' => [
                        'name'        => 'ललिता अष्टोत्तर पूजा',
                        'title'       => 'माँ ललिता के १०८ नामों से अर्चना — समृद्धि व कल्याण हेतु',
                        'description' => 'ललिता अष्टोत्तर पूजा में देवी ललिता के १०८ पवित्र नामों (अष्टोत्तर शतनामावली) का पाठ एवं विधिवत् पूजन किया जाता है। सहस्रचन की तुलना में संक्षिप्त एवं अधिक सुलभ, यह पूजा साप्ताहिक या मासिक अनुष्ठान के लिए आदर्श है। यह सुस्वास्थ्य, कार्यसिद्धि, सुखद संबंध और आध्यात्मिक पुण्य प्रदान करती है। पूजन में कुमकुम अर्चना, पुष्प अर्चना, नैवेद्य और दीपम सम्मिलित हैं। महिलाओं के कल्याण एवं सुरक्षा और परिवार पर दिव्य माँ की निरंतर कृपा के लिए विशेष रूप से लाभकारी।',
                    ],
                ],
                'images'  => [
                    'primary' => 'services/lalita-astottar-pooja.jpg',
                    'gallery' => [
                        'services/lalita-astottar-pooja.jpg',
                    ],
                ],
                'pricing' => [
                    'amount'          => 1100,
                    'currency'        => 'INR',
                    'discount_amount' => null,
                ],
                'status'  => 'active',
            ],

            [
                'translations' => [
                    'en' => [
                        'name'        => 'Shree Yantra Abhishek',
                        'title'       => 'Sacred Consecration of Shree Yantra for Wealth, Success & Cosmic Energy',
                        'description' => 'Shree Yantra Abhishek is the ritual bathing and energisation of the Shree Yantra — the most powerful of all Vedic geometric diagrams, representing the totality of the cosmos and the abode of Goddess Lakshmi. The abhishek is performed with panchamrit (milk, curd, honey, ghee, and sugar), rose water, saffron water, and kumkum, accompanied by chanting of the Shree Sukta and Kanakdhara Stotram. A properly energised Shree Yantra draws prosperity, removes financial obstacles, and fills the home or business with positive cosmic energy. Includes prana pratishtha (consecration) and the yantra is handed to you after the ritual.',
                    ],
                    'hi' => [
                        'name'        => 'श्री यंत्र अभिषेक',
                        'title'       => 'धन, सफलता व ब्रह्मांडीय ऊर्जा के लिए श्री यंत्र का पवित्र अभिषेक',
                        'description' => 'श्री यंत्र अभिषेक सबसे शक्तिशाली वैदिक ज्यामितीय आकृति — श्री यंत्र का अनुष्ठानिक स्नान और ऊर्जान्वयन है। यह संपूर्ण ब्रह्मांड का प्रतीक और देवी लक्ष्मी का निवास माना जाता है। अभिषेक पंचामृत (दूध, दही, शहद, घी और शर्करा), गुलाब जल, केसर जल और कुमकुम से श्री सूक्त एवं कनकधारा स्तोत्र के पाठ के साथ संपन्न होता है। विधिवत् ऊर्जान्वित श्री यंत्र समृद्धि को आकर्षित करता है, आर्थिक बाधाओं को दूर करता है और घर या व्यापार को सकारात्मक ब्रह्मांडीय ऊर्जा से भर देता है। प्राण-प्रतिष्ठा (प्रतिष्ठापन) सम्मिलित — अनुष्ठान के पश्चात् यंत्र आपको प्रदान किया जाता है।',
                    ],
                ],
                'images'  => [
                    'primary' => 'services/shree-yantra-abhishek.jpg',
                    'gallery' => [
                        'services/shree-yantra-abhishek.jpg',
                    ],
                ],
                'pricing' => [
                    'amount'          => 7100,
                    'currency'        => 'INR',
                    'discount_amount' => null,
                ],
                'status'  => 'active',
            ],
        ];

        foreach ($services as $data) {
            $enName = $data['translations']['en']['name'];

            // Match on the English name stored in JSON
            $existing = Service::whereRaw(
                "JSON_EXTRACT(translations, '$.en.name') = ?",
                [$enName]
            )->first();

            if ($existing) {
                $existing->update($data);
            } else {
                Service::create($data);
            }
        }
    }
}
