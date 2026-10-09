<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('email', 'admin@krmknd.com')->value('id')
            ?? User::first()?->id;

        $articles = [
            [
                'category' => 'spiritual',
                'title'    => 'The Sacred Significance of Sri Yantra in Daily Worship',
                'excerpt'  => 'Discover how the Sri Yantra — a geometric representation of the cosmos — can elevate your daily puja practice and deepen your connection with the divine.',
                'content'  => <<<HTML
<p>The Sri Yantra is one of the most powerful and sacred geometric symbols in Hinduism. Composed of nine interlocking triangles that radiate from a central point, it represents the entire cosmos and the union of Shiva and Shakti.</p>
<h2>Origin and Meaning</h2>
<p>Believed to have originated thousands of years ago, the Sri Yantra is described in the Atharva Veda and numerous Tantric texts. The 43 smaller triangles formed by the intersecting lines each represent a different aspect of the divine.</p>
<h2>How to Use in Daily Worship</h2>
<ul>
<li>Place the yantra on a clean cloth facing east in your puja space.</li>
<li>Offer fresh flowers, incense and a lit diya before beginning your meditation.</li>
<li>Gaze at the central bindu point to still the mind and invite divine energy.</li>
</ul>
<p>Regular worship of the Sri Yantra is said to bestow clarity, prosperity and spiritual growth upon the devotee.</p>
HTML,
                'tags'         => ['yantra', 'puja', 'meditation', 'shakti'],
                'status'       => 'published',
                'published_at' => now()->subDays(10),
                'translations' => [
                    'hi' => [
                        'title'   => 'दैनिक पूजा में श्री यंत्र का पवित्र महत्व',
                        'excerpt' => 'जानिए कैसे श्री यंत्र — ब्रह्मांड का ज्यामितीय प्रतिनिधित्व — आपकी दैनिक पूजा को उन्नत कर सकता है।',
                    ],
                ],
            ],
            [
                'category' => 'astrology',
                'title'    => 'Navratri 2025: Auspicious Muhurtas and Puja Timings',
                'excerpt'  => 'Complete guide to Navratri 2025 — Ghatasthapana muhurta, daily puja timings, fasting rules and the significance of each of the nine nights.',
                'content'  => <<<HTML
<p>Navratri, the nine-night festival dedicated to Goddess Durga, is one of the most spiritually potent periods in the Hindu calendar. In 2025, Shardiya Navratri begins on 2 October.</p>
<h2>Ghatasthapana Muhurta</h2>
<p>The first day's Ghatasthapana (Kalash Sthapana) should ideally be performed during the Pratipada tithi in the first one-third of the day. Performing it during the Chitra nakshatra or Vaidhriti yoga should be avoided.</p>
<h2>The Nine Forms of Durga</h2>
<ol>
<li>Pratipada — Shailputri</li>
<li>Dwitiya — Brahmacharini</li>
<li>Tritiya — Chandraghanta</li>
<li>Chaturthi — Kushmanda</li>
<li>Panchami — Skandamata</li>
<li>Shashthi — Katyayani</li>
<li>Saptami — Kaalratri</li>
<li>Ashtami — Mahagauri</li>
<li>Navami — Siddhidatri</li>
</ol>
<p>Each form carries a distinct energy. Meditating on the correct form on the corresponding night amplifies the benefits of your sadhana.</p>
HTML,
                'tags'         => ['navratri', 'durga', 'muhurta', 'festival'],
                'status'       => 'published',
                'published_at' => now()->subDays(7),
                'translations' => [
                    'hi' => [
                        'title'   => 'नवरात्रि 2025: शुभ मुहूर्त और पूजा समय',
                        'excerpt' => 'नवरात्रि 2025 की संपूर्ण गाइड — घटस्थापना मुहूर्त, दैनिक पूजा समय, व्रत के नियम और नौ रातों का महत्व।',
                    ],
                ],
            ],
            [
                'category' => 'puja',
                'title'    => 'Step-by-Step Guide to Performing Satyanarayan Katha at Home',
                'excerpt'  => 'Everything you need to know to perform a Satyanarayan Puja at home — the required samagri, procedure, and the significance of each step.',
                'content'  => <<<HTML
<p>Satyanarayan Puja is a devotional ceremony performed in honour of Lord Vishnu in his aspect as Satyanarayan — the Lord of Truth. It can be conducted at home on any auspicious occasion or on the Purnima (full moon) day.</p>
<h2>Required Samagri</h2>
<ul>
<li>Banana leaves and ripe bananas</li>
<li>Panchamrit (milk, curd, honey, ghee, sugar)</li>
<li>Tulsi leaves, marigold flowers</li>
<li>Wheat flour, sugar and ghee for the prasad (sheera)</li>
<li>Incense sticks, camphor, diya and ghee</li>
</ul>
<h2>Procedure</h2>
<p>Begin by installing a picture or idol of Lord Vishnu on a clean platform. Offer panchamrit abhishek followed by the panchopachar puja. The Katha is then read or heard in five chapters, after which the prasad is distributed to all present.</p>
<p>The puja is considered incomplete without distributing prasad — even a small amount given with devotion carries the full blessing.</p>
HTML,
                'tags'         => ['satyanarayan', 'vishnu', 'puja', 'katha'],
                'status'       => 'published',
                'published_at' => now()->subDays(4),
                'translations' => [
                    'hi' => [
                        'title'   => 'घर पर सत्यनारायण कथा करने की चरण-दर-चरण मार्गदर्शिका',
                        'excerpt' => 'घर पर सत्यनारायण पूजा करने के लिए आवश्यक सामग्री, विधि और प्रत्येक चरण का महत्व।',
                    ],
                ],
            ],
            [
                'category' => 'festival',
                'title'    => 'Diwali Puja Vidhi: How to Welcome Goddess Lakshmi',
                'excerpt'  => 'The correct procedure for Diwali Lakshmi Puja — the right muhurta, what to place on the puja thali and how to perform aarti for maximum blessings.',
                'content'  => <<<HTML
<p>Diwali Lakshmi Puja is performed on Amavasya, the new moon night of the Kartik month. The Pradosh Kaal (after sunset) is considered the most auspicious time.</p>
<h2>Puja Thali Essentials</h2>
<ul>
<li>Idol or image of Goddess Lakshmi and Lord Ganesha</li>
<li>Roli, kumkum, haldi, akshat (whole rice)</li>
<li>Lotus flowers or rose petals</li>
<li>Coins and notes (symbolising wealth to be blessed)</li>
<li>Kheel (puffed rice), batashe (sugar drops), dry fruits</li>
</ul>
<h2>Performing the Aarti</h2>
<p>Light the camphor and cotton-wick diya in the thali. Move the thali clockwise in small circles before the deity while chanting the Lakshmi Aarti. Ring the bell continuously during the aarti to purify the space.</p>
<p>After the puja keep the lights in your home burning through the night — Goddess Lakshmi visits homes that are bright and welcoming.</p>
HTML,
                'tags'         => ['diwali', 'lakshmi', 'puja', 'festival'],
                'status'       => 'published',
                'published_at' => now()->subDays(2),
                'translations' => [
                    'hi' => [
                        'title'   => 'दिवाली पूजा विधि: देवी लक्ष्मी का स्वागत कैसे करें',
                        'excerpt' => 'दिवाली लक्ष्मी पूजा की सही विधि — शुभ मुहूर्त, पूजा थाली में क्या रखें और आरती कैसे करें।',
                    ],
                ],
            ],
            [
                'category' => 'tips',
                'title'    => '7 Daily Habits to Strengthen Your Spiritual Practice',
                'excerpt'  => 'Small, consistent actions that bring lasting spiritual growth — from morning routines and mantra repetition to the right way to light a diya.',
                'content'  => <<<HTML
<p>Spirituality does not require hours of elaborate ritual every day. A handful of consistent, mindful habits can build a powerful sadhana over time.</p>
<ol>
<li><strong>Wake before sunrise.</strong> The Brahma Muhurta (roughly 90 minutes before sunrise) is considered the most sattvic time for meditation and prayer.</li>
<li><strong>Light a diya first thing.</strong> Offering light to the divine before you pick up your phone sets an intentional, sacred tone for the day.</li>
<li><strong>Chant a seed mantra.</strong> Even five minutes of Om chanting or your personal ishta-devata mantra clears mental noise.</li>
<li><strong>Read a verse of scripture.</strong> One shloka from the Gita, Ramayana or any upanishad, reflected on slowly, is enough.</li>
<li><strong>Offer water to the Tulsi plant.</strong> Tulsi is sacred to Lord Vishnu. Daily seva of the plant is itself a form of worship.</li>
<li><strong>Practice gratitude before meals.</strong> A moment of silent thanks before eating transforms an ordinary act into a spiritual one.</li>
<li><strong>End the day with a brief prayer.</strong> Surrender the day's actions to the divine before sleep to maintain inner peace.</li>
</ol>
HTML,
                'tags'         => ['tips', 'sadhana', 'daily-practice', 'meditation'],
                'status'       => 'published',
                'published_at' => now()->subDay(),
                'translations' => [
                    'hi' => [
                        'title'   => 'आपकी आध्यात्मिक साधना को मजबूत करने की 7 दैनिक आदतें',
                        'excerpt' => 'छोटी, नियमित क्रियाएं जो स्थायी आध्यात्मिक विकास लाती हैं — सुबह की दिनचर्या, मंत्र जप और दीया जलाने की सही विधि।',
                    ],
                ],
            ],
            [
                'category' => 'general',
                'title'    => 'Understanding Panchang: How to Read the Hindu Calendar',
                'excerpt'  => 'A beginner-friendly explanation of the five limbs of the Panchang — tithi, vara, nakshatra, yoga and karana — and why they matter for every puja.',
                'content'  => <<<HTML
<p>The Panchang (from Sanskrit "pancha" = five, "anga" = limb) is the traditional Hindu almanac. Every auspicious activity — from starting a business to booking a marriage date — consults the Panchang first.</p>
<h2>The Five Limbs</h2>
<ul>
<li><strong>Tithi</strong> — the lunar day (30 per lunar month). Each tithi has a presiding deity and specific qualities.</li>
<li><strong>Vara</strong> — the weekday. Each day is ruled by a planet (Ravivara/Sun, Somavara/Moon, etc.).</li>
<li><strong>Nakshatra</strong> — the lunar mansion the Moon occupies. There are 27 nakshatras, each spanning 13°20'.</li>
<li><strong>Yoga</strong> — a combination of the Sun and Moon's longitudes. There are 27 yogas; some are auspicious, others inauspicious.</li>
<li><strong>Karana</strong> — half a tithi. There are 11 karanas, repeating through the month.</li>
</ul>
<p>An experienced Jyotishi reads all five together to determine whether a given moment is suitable for a particular action. Our daily Panchang section in the app shows all five elements so you always know the quality of the day.</p>
HTML,
                'tags'         => ['panchang', 'jyotish', 'astrology', 'calendar'],
                'status'       => 'published',
                'published_at' => now(),
                'translations' => [
                    'hi' => [
                        'title'   => 'पंचांग को समझना: हिंदू कैलेंडर कैसे पढ़ें',
                        'excerpt' => 'पंचांग के पाँच अंगों — तिथि, वार, नक्षत्र, योग और करण — की शुरुआती-अनुकूल व्याख्या।',
                    ],
                ],
            ],
            [
                'category' => 'spiritual',
                'title'    => 'The Power of Sankalpa: Setting Sacred Intentions Before Puja',
                'excerpt'  => 'Learn why the Sankalpa — a formal intention-setting statement — is the most important step of any puja, and how to take it correctly.',
                'content'  => <<<HTML
<p>Before any puja or ritual in the Hindu tradition, the priest or worshipper takes a Sankalpa — a solemn declaration of intent. Without Sankalpa, the ritual is considered incomplete in the Vedic sense.</p>
<h2>What a Sankalpa Contains</h2>
<p>A traditional Sankalpa includes: the cosmic time (Kalpa, Manvantara, Yuga), the geographic location (continent, country, region, river), the current date in the Hindu calendar, the worshipper's name and gotra, and finally the specific purpose of the puja.</p>
<h2>The Modern Sankalpa</h2>
<p>You do not need to recite the full Sanskrit Sankalpa to benefit from the principle. Simply sitting quietly before your puja, placing your hand over your heart, and clearly stating your intention — in your own language — is enough to align your action with divine will.</p>
<p>The Sankalpa transforms mechanical ritual into conscious prayer. It is the difference between lighting a diya out of habit and lighting it as an offering.</p>
HTML,
                'tags'         => ['sankalpa', 'intention', 'puja', 'spiritual'],
                'status'       => 'draft',
                'published_at' => null,
                'translations' => [
                    'hi' => [
                        'title'   => 'संकल्प की शक्ति: पूजा से पहले पवित्र इरादा कैसे लें',
                        'excerpt' => 'जानिए क्यों संकल्प — एक औपचारिक इरादा-निर्धारण वक्तव्य — किसी भी पूजा का सबसे महत्वपूर्ण चरण है।',
                    ],
                ],
            ],
        ];

        foreach ($articles as $data) {
            $slug = Str::slug($data['title']);
            Article::updateOrCreate(
                ['slug' => $slug],
                array_merge($data, [
                    'slug'       => $slug,
                    'author_id'  => $adminId,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ])
            );
        }

        $this->command->info('ArticleSeeder: '.count($articles).' records inserted/updated.');
    }
}
